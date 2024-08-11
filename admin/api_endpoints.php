<?php 
// admin\api_endpoints.php
add_action('rest_api_init', 'kapi_rest_api_init'); 
function kapi_rest_api_init() {
    register_rest_route('kapi/v1', '/products', [
        'methods' => 'GET',
        'callback' => 'kapi_fetch_product_from_api'
    ]);  
    
    register_rest_route('kapi/v1', '/sync_product', [
        'methods' => 'GET',
        'callback' => 'kapi_sync_product_to_woocommerce'
    ]);
};



function kapi_fetch_product_from_api() {

    return kapi_insert_products_into_db();
}

function kapi_sync_product_to_woocommerce() {
    return kapi_product_import_to_woocommerce();

}