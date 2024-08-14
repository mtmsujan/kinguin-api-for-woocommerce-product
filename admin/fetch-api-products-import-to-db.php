<?php

// TRUNCATE Table
function kapi_truncate_table($table_name)
{
    global $wpdb;
    $wpdb->query("TRUNCATE TABLE $table_name");
}

// Fetch products from the API
function kapi_fetch_products_from_api($page = 1): string
{
    $page = $page == 0 ? 1 : $page;
    $api_key = get_option('kinguin_api_key') ?? '';
    $api_url = get_option('kinguin_base_url') ?? '';


    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $api_url . '/esa/api/v1/products?limit=100&page=' . $page,
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

    // Get total number of products
    $total_products = (int) get_option('kinguin_total_products') ?? 0;
    $current_page = (int) get_option('kinguin_current_page') ?? 1;

    // calculate total number of pages
    $total_pages = ceil($total_products / 100);

    for ($page = $current_page; $page <= $total_pages; $page++) {

        // Fetch products from the API
        $api_response = kapi_fetch_products_from_api($page);
        $data = json_decode($api_response, true);
        if (!array_key_exists('results', $data)) {
            return $api_response;
        }
        $products = $data['results'];



        $_total_products = $data['item_count'];
        // Update total number of products
        update_option('kinguin_total_products', $_total_products);

        // Track Creant pages
        update_option('kinguin_current_page', $page);

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
    }



    return "Kinguin Id inserted into database";
}



