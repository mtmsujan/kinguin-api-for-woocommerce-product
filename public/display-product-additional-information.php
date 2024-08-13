<?php
// Add additional product information to WooCommerce attributes
add_filter('woocommerce_display_product_attributes', 'kapi_display_selected_product_information', 10, 2);
function kapi_display_selected_product_information($product_attributes, $product)
{
    // Get product ID
    $product_id = $product->get_id();

    // Array of meta keys and their respective labels to display
    $meta_fields = [
        '_product_systemRequirements' => __('System Requirements', 'kinguin-api-for-woocommerce-product'),
        '_product_activationDetails' => __('Key Activation', 'kinguin-api-for-woocommerce-product'),
        '_product_languages' => __('Languages', 'kinguin-api-for-woocommerce-product'),
    ];

    // Loop through each meta field
    foreach ($meta_fields as $meta_key => $label) {
        $meta_value = get_post_meta($product_id, $meta_key, true);

        if ($meta_value) {
            if ($meta_key === '_product_systemRequirements') {
                // System requirements should be displayed in a list
                $systemRequirements = json_decode($meta_value, true);
                $meta_value = '<ul>';
                foreach ($systemRequirements as $system) {
                    $requirements = $system['requirements'] ?? ($system['requirement'] ?? []);
                    if (count($requirements) == 1) {
                        $requirements = explode("\n", $requirements[0]);
                    }
                    foreach ($requirements as $requirement) {
                        if (strpos($requirement, ":") !== false) {
                            [$key, $value] = explode(":", $requirement, 2);
                            $value = htmlspecialchars(trim($value));
                            $list = '<li>' . htmlspecialchars(trim($key)) . ': ' . $value . '</li>';
                        } else {
                            $list = '<li>' . htmlspecialchars(trim($requirement)) . '</li>';
                        }
                        $meta_value .= $list;
                    }
                }
                $meta_value .= '</ul>';
            } elseif ($meta_key === '_product_languages') {
                // Languages should be displayed as a comma-separated list
                $meta_value = json_decode($meta_value, true);
                $meta_value = is_array($meta_value) ? implode(', ', $meta_value) : '';
            } else {
                $activationDetails = $meta_value; 
                $meta_value = $activationDetails;
            }

            // Ensure meta_value is a string before adding to product attributes
            if (!is_array($meta_value)) {
                $product_attributes[$meta_key] = [
                    'label' => $label,
                    'value' => $meta_value,
                ];
            }
        }
    }

    return $product_attributes;
}

// Add custom product tab
add_filter( 'woocommerce_product_tabs', 'add_custom_product_tab', 98 );
function add_custom_product_tab( $tabs ) {
    $tabs['additional_information'] = array(
        'title'    => __( 'Additional Information', 'kinguin-api-for-woocommerce-product' ),
        'priority' => 50,
        'callback' => 'custom_product_tab_content'
    );
    return $tabs;
}

// Display content for custom product tab
function custom_product_tab_content() {
    global $post;
    $product_id = $post->ID;

    // Array of meta keys and their respective labels to display
    $meta_fields = [
        '_product_systemRequirements' => __('System Requirements', 'kinguin-api-for-woocommerce-product'),
        '_product_activationDetails' => __('Key Activation', 'kinguin-api-for-woocommerce-product'),
        '_product_languages' => __('Languages', 'kinguin-api-for-woocommerce-product'),
    ];

    // Loop through each meta field

    foreach ($meta_fields as $meta_key => $label) {
        $meta_value = get_post_meta($product_id, $meta_key, true);

        if ($meta_value) {
            if ($meta_key === '_product_systemRequirements') {
                // System requirements should be displayed in a list
                $systemRequirements = json_decode($meta_value, true);
                echo '<h3>' . $label . '</h3>';
                echo '<ul>';
                foreach ($systemRequirements as $system) {
                    $requirements = $system['requirements'] ?? ($system['requirement'] ?? []);
                    if (count($requirements) == 1) {
                        $requirements = explode("\n", $requirements[0]);
                    }
                    foreach ($requirements as $requirement) {
                        if (strpos($requirement, ":") !== false) {
                            [$key, $value] = explode(":", $requirement, 2);
                            $value = htmlspecialchars(trim($value));
                            echo '<li>' . htmlspecialchars(trim($key)) . ': ' . $value . '</li>';
                        } else {
                            echo '<li>' . htmlspecialchars(trim($requirement)) . '</li>';
                        }
                    }
                }
                echo '</ul>';
            } elseif ($meta_key === '_product_languages') {
                // Languages should be displayed as a comma-separated list
                $meta_value = json_decode($meta_value, true);
                echo '<h3>' . $label . '</h3>';
                echo '<p>' . (is_array($meta_value) ? implode(', ', $meta_value) : '') . '</p>';
            } else {
                $activationDetails = $meta_value; 
                echo '<h3>' . $label . '</h3>';
                echo $activationDetails;
            }
        }
    }
}
