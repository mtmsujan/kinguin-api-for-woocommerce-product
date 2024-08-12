<?php 

// public/display-product-additional-information.php
add_filter('woocommerce_display_product_attributes', 'kapi_display_product_additional_information', 10, 2);
function kapi_display_product_additional_information($product_attributes, $product) {

    // Get product additional information
    $product_id = $product->get_id();
     
    $product_productid = get_post_meta($product_id, '_product_productId', true);

    if ($product_productid) {
        $product_attributes['product_productid'] = [
            'label' => __('Product ID', 'your-text-domain'), // Replace 'your-text-domain' with your actual text domain
            'value' => esc_html($product_productid),
        ];
    }

    return $product_attributes;
}
