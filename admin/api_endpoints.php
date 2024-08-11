<?php 
// admin\api_endpoints.php
add_action('rest_api_init', 'kapi_rest_api_init'); 
function kapi_rest_api_init() {
    register_rest_route('kapi/v1', '/products', [
        'methods' => 'GET',
        'callback' => 'kapi_fetch_product_from_api'
    ]);
};

function kapi_fetch_product_from_api() {

    return kapi_insert_products_into_db();
}





?>