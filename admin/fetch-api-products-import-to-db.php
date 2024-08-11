<?php

// TRUNCATE Table
function kapi_truncate_table( $table_name ) {
    global $wpdb;
    $wpdb->query( "TRUNCATE TABLE $table_name" );
}
function kapi_fetch_products_from_api()
{
    $api_key = get_option('kinguin_api_key') ?? '';
    $api_url = get_option('kinguin_base_url') ?? '';


    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $api_url . '/esa/api/v1/products?limit=100',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'X-Api-Key: ' . $api_key,
                'Cookie: _cfuvid=q9IBfkNkTWJQYlVNoi84pBeWaHt47vrytPsTNFImjyg-1723349422698-0.0.1.1-604800000'
            ),
        )
    );


    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

// Insert kinguinid into database
function kapi_insert_products_into_db()
{
    // Fetch products from the API
    $api_response = kapi_fetch_products_from_api();
    $api_response = json_decode($api_response, true);
    $products = $api_response['results'];

    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_products';
    // kapi_truncate_table($table_name);

    if (!empty($products) && is_array($products)) {
        foreach ($products as $product) {

            $kinguinid = $product['kinguinId'];

            // Check if the product already exists
            $wpdb->insert(
                $table_name,
                [
                    'kinguinid' => $kinguinid,
                    'status' => 'pending'
                ]
            );

        }
    }

    return "Kinguin Id inserted into database";
}



