<?php
// admin\product-import-to-woocommerce.php
require_once KAPI_PLUGIN_PATH . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;

// Fetch single product from the API
function kapi_fetch_single_product_from_api($kinguinid)
{
    $api_key = get_option('kinguin_api_key') ?? '';
    $api_url = get_option('kinguin_base_url') ?? '';

    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $api_url . '/esa/api/v1/products/' . $kinguinid,
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

function kapi_product_import_to_woocommerce()
{
    try {

        // Get Global Variable
        global $wpdb;

        // Define table name
        $table_name = $wpdb->prefix . 'sync_products';

        // Sql Query
        $sql = "SELECT id, kinguinid FROM $table_name WHERE status = 'pending' LIMIT 1";

        // Execute Query
        $result = $wpdb->get_results($wpdb->prepare($sql));

        $serial_id = 0;
        $kinguinid = null;
        // Check if result is not empty
        if (!empty($result) && is_array($result)) {
            // Loop through result
            foreach ($result as $product) {
                $serial_id = $product->id;
                $kinguinid = $product->kinguinid;
            }
        }

        // Fetch single product from the API
        $api_response = kapi_fetch_single_product_from_api($kinguinid);
        $product = json_decode($api_response, true);
        // print_r(json_encode($api_response));

        // Extract product details
        $product_name = $product['name'] ?? '';
        $product_description = $product['description'] ?? '';
        $thumbnail_image = $product['coverImageOriginal'] ?? '';

        $product_developers = $product['developers'] ?? [];
        $product_publishers = $product['publishers'] ?? [];
        $product_genres = $product['genres'] ?? [];
        $platform = $product['platform'] ?? '';
        $product_releaseDate = $product['releaseDate'] ?? '';

        $product_qty = $product['qty'] ?? 0;

        $product_textQty = $product['textQty'] ?? 0;

        $profit_percentage = get_option('kinguin_profit_percentage') ?? 0;
        $product_price = $product['price'] ?? 0;

        // Calculate profit Percentage
        $percentage = $product_price * ($profit_percentage / 100);
        $product_price = $product_price + $percentage;


        $product_cheapestOfferId = $product['cheapestOfferId'] ?? [];
        $product_preorder = $product['isPreorder'] ?? false; 
        $product_metacriticScore = $product['metacriticScore'] ?? 0;
        $product_regionalLimitations = $product['regionalLimitations'] ?? '';
        $product_regionId = $product['regionId'] ?? 0;
        $product_activationDetails = $product['activationDetails'] ?? '';
        $product_kinguinId = $product['kinguinId'] ?? 0;
        $product_productId = $product['productId'] ?? '';
        $product_originalName = $product['originalName'] ?? '';

        $product_screenshots = $product['screenshots'] ?? [];

        $converted_product_screenshots = [];
        if (!empty($product_screenshots) && is_array($product_screenshots)) {
            foreach ($product_screenshots as $screenshot) {
                $converted_product_screenshots[] = $screenshot['url_original'];
            }
        }

        // Merge Thumbnail Image with Screenshots
        $converted_product_screenshots = array_merge([$thumbnail_image], $converted_product_screenshots);

        $product_videos = $product['videos'] ?? [];
        $product_languages = $product['languages'] ?? [];
        $product_systemRequirements = $product['systemRequirements'] ?? [];

        $product_tags = $product['tags'] ?? [];

        $product_offers = $product['offers'] ?? [];
        $product_offersCount = $product['offersCount'] ?? 0;
        $product_totalQty = $product['totalQty'] ?? 0;
        $product_merchantName = $product['merchantName'] ?? [];
        $product_ageRating = $product['ageRating'] ?? '';
        $product_steam = $product['steam'] ?? '';
        $product_images = $product['images'] ?? [];


        // get woocommerce store information
        $woo_consumer_key = get_option('woocommerce_client_id') ?? '';
        $woo_consumer_secret = get_option('woocommerce_client_secret') ?? '';

        //woocommerce store information
        $website_url = home_url();
        $consumer_key = $woo_consumer_key;
        $consumer_secret = $woo_consumer_secret;

        // Set up the API client with your WooCommerce store URL and credentials
        $client = new Client(
            $website_url,
            $consumer_key,
            $consumer_secret,
            [
                'verify_ssl' => false,
                'wp_api' => true,
                'version' => 'wc/v3',
                'timeout' => 400,
            ]
        );

        // if sku already exists, update the product
        $args = array(
            'post_type' => 'product',
            'meta_query' => array(
                array(
                    'key' => '_sku',
                    'value' => $kinguinid,
                    'compare' => '=',
                ),
            ),
        );

        // Check if the product already exists
        $existing_products = new WP_Query($args);

        if ($existing_products->have_posts()) {
            $existing_products->the_post();

            // get product id
            $_product_id = get_the_ID();

            // Update the product
            $product_data = [
                'name' => "$product_name",
                'sku' => "$kinguinid",
                'type' => 'simple',
                'description' => "$product_description",
                'attributes' => [],
            ];

            // update product
            $client->put('products/' . $_product_id, $product_data);

            // Update the status of the processed product in your database
            $wpdb->update(
                $table_name,
                ['status' => 'completed'],
                ['id' => $serial_id]
            );

            return "Product Updated";

        } else {
            // Create a new product
            $_product_data = [
                'name' => "$product_name",
                'sku' => "$kinguinid",
                'type' => 'simple',
                'description' => "$product_description",
                'attributes' => [],
            ];

            // Create the product
            $_product = $client->post('products', $_product_data);
            $product_id = $_product->id;

            $wpdb->update(
                $table_name,
                ['status' => 'completed'],
                ['id' => $serial_id]
            );

            // Update product meta data
            update_post_meta($product_id, '_price', $product_price);

            // Update product sale price
            update_post_meta($product_id, '_sale_price', $product_price);

            // Update Product Tags
            if (!empty($product_tags) && is_array($product_tags)) {
                wp_set_object_terms($product_id, $product_tags, 'product_tag');
            }

            // Update Product Categories
            if (!empty($product_genres) && is_array($product_genres)) {
                wp_set_object_terms($product_id, $product_genres, 'product_cat');
            }

            //Update product meta data in WordPress
            update_post_meta($product_id, '_stock', $product_qty);

            //display out of stock message if stock is 0
            update_post_meta($product_id, '_manage_stock', 'yes');

            if ($product_qty <= 0) {
                update_post_meta($product_id, '_stock_status', 'outofstock');
            } else {
                update_post_meta($product_id, '_stock_status', 'instock');
            }

            kapi_set_product_images($product_id, $converted_product_screenshots);
            // system requirements data Formating 
            $systemRequirements = array_map(function($item) {
                $requirements = $item['requirement'];
                if (count($requirements) == 1) {
                    $requirements = explode("\n", $requirements[0]);
                }
                $newItem = [
                    "system" => $item['system'],
                    "requirements" => $requirements
                ];
                return $newItem;
            }, $product_systemRequirements);

            $json_systemRequirements = json_encode($systemRequirements);

            // product activationDetails data Formating
            $formattedDetails = nl2br(htmlspecialchars($product_activationDetails));
            

            // Update Product additional information 
            update_post_meta($product_id, '_product_developers', json_encode($product_developers));
            update_post_meta($product_id, '_product_publishers', json_encode($product_publishers));
            update_post_meta($product_id, '_platform', $platform);
            update_post_meta($product_id, '_product_releaseDate', $product_releaseDate);
            update_post_meta($product_id, '_product_textQty', $product_textQty);
            update_post_meta($product_id, '_product_cheapestOfferId', json_encode($product_cheapestOfferId));
            update_post_meta($product_id, '_product_preorder', $product_preorder);
            update_post_meta($product_id, '_product_metacriticScore', $product_metacriticScore);
            update_post_meta($product_id, '_product_regionalLimitations', $product_regionalLimitations);
            update_post_meta($product_id, '_product_regionId', $product_regionId);
            update_post_meta($product_id, '_product_activationDetails', $formattedDetails);
            update_post_meta($product_id, '_product_productId', $product_productId);
            update_post_meta($product_id, '_product_originalName', $product_originalName);
            update_post_meta($product_id, '_product_videos', json_encode($product_videos));
            update_post_meta($product_id, '_product_languages', json_encode($product_languages));
            update_post_meta($product_id, '_product_systemRequirements', $json_systemRequirements);
            update_post_meta($product_id, '_product_offers', json_encode($product_offers));
            update_post_meta($product_id, '_product_offersCount', $product_offersCount);
            update_post_meta($product_id, '_product_totalQty', $product_totalQty);
            update_post_meta($product_id, '_product_merchantName', json_encode($product_merchantName));
            update_post_meta($product_id, '_product_ageRating', $product_ageRating);
            update_post_meta($product_id, '_product_steam', $product_steam);
            update_post_meta($product_id, '_product_images', $product_images);

            return "Product Import Successfully";

        }

    } catch (HttpClientException $e) {
        echo '<pre><code>' . print_r($e->getMessage(), true) . '</code><pre>'; // Error message.
        echo '<pre><code>' . print_r($e->getRequest(), true) . '</code><pre>'; // Last request data.
        echo '<pre><code>' . print_r($e->getResponse(), true) . '</code><pre>'; // Last response data.
    }
}


function kapi_set_product_images($product_id, $images)
{
    if (!empty($images) && is_array($images)) {
        foreach ($images as $image) {

            // Extract image name
            $image_name = basename($image);

            // Get WordPress upload directory
            $upload_dir = wp_upload_dir();

            // Download the image from URL and save it to the upload directory
            $image_data = file_get_contents($image);

            if ($image_data !== false) {
                $image_file = $upload_dir['path'] . '/' . $image_name;
                file_put_contents($image_file, $image_data);

                // Prepare image data to be attached to the product
                $file_path = $upload_dir['path'] . '/' . $image_name;
                $file_name = basename($file_path);

                // Insert the image as an attachment
                $attachment = [
                    'post_mime_type' => mime_content_type($file_path),
                    'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                    'post_content' => '',
                    'post_status' => 'inherit',
                ];

                $attach_id = wp_insert_attachment($attachment, $file_path, $product_id);

                // Add the image to the product gallery
                $gallery_ids = get_post_meta($product_id, '_product_image_gallery', true);
                $gallery_ids = explode(',', $gallery_ids);
                $gallery_ids[] = $attach_id;
                update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_ids));

                if (strpos($image, '_cover_original.jpg') !== false) {
                    // Set the image as the product thumbnail
                    set_post_thumbnail($product_id, $attach_id);
                }

                // if not set post-thumbnail then set a random thumbnail from gallery
                if (!has_post_thumbnail($product_id)) {
                    if (!empty($gallery_ids)) {
                        $random_attach_id = $gallery_ids[array_rand($gallery_ids)];
                        set_post_thumbnail($product_id, $random_attach_id);
                    }
                }

            }
        }
    }
}