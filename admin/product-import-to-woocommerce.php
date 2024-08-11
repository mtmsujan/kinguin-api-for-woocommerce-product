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

    curl_setopt_array($curl, array(
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

function kapi_product_import_to_woocommerce(){
    try {

        return "Product Import To Woocommerce Successfully Completed";

    } catch (HttpClientException $e) {
        echo '<pre><code>' . print_r($e->getMessage(), true) . '</code><pre>'; // Error message.
        echo '<pre><code>' . print_r($e->getRequest(), true) . '</code><pre>'; // Last request data.
        echo '<pre><code>' . print_r($e->getResponse(), true) . '</code><pre>'; // Last response data.
    }
}