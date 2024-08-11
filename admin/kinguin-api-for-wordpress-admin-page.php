<?php
/**
 * The admin-specific functionality of the plugin.
 */
define('KINGUIN_API_OPTION_GROUP', 'kinguin_sync_settings_group');
define('KINGUIN_BASE_URL_OPTION', 'kinguin_base_url');
define('KINGUIN_API_KEY_OPTION', 'kinguin_api_key');
define('WOOCOMMERCE_CLIENT_ID_OPTION', 'woocommerce_client_id');
define('WOOCOMMERCE_CLIENT_SECRET_OPTION', 'woocommerce_client_secret');
define('KINGUIN_PROFIT_PERCENTAGE_OPTION', 'kinguin_profit_percentage');

// Add a menu item in the WordPress admin.
add_action('admin_menu', 'kinguin_woocommerce_sync_menu');
function kinguin_woocommerce_sync_menu() {
    add_menu_page(
        __('Kinguin API Settings Page', 'kinguin-api-for-woocommerce-product'), 
        __('Kinguin API Settings', 'kinguin-api-for-woocommerce-product'), 
        'manage_options', 
        'kinguin-sync-settings', 
        'kinguin_woocommerce_sync_settings_page', 
        'dashicons-admin-generic'
    );
}

// Render the settings page content.
function kinguin_woocommerce_sync_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Kinguin WooCommerce API Settings', 'kinguin-api-for-woocommerce-product'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields(KINGUIN_API_OPTION_GROUP);
            do_settings_sections('kinguin-sync-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings and add fields to the settings page.
add_action('admin_init', 'kinguin_woocommerce_sync_register_settings');
function kinguin_woocommerce_sync_register_settings() {
    // Register settings
    register_setting(KINGUIN_API_OPTION_GROUP, KINGUIN_BASE_URL_OPTION);
    register_setting(KINGUIN_API_OPTION_GROUP, KINGUIN_API_KEY_OPTION);
    register_setting(KINGUIN_API_OPTION_GROUP, WOOCOMMERCE_CLIENT_ID_OPTION);
    register_setting(KINGUIN_API_OPTION_GROUP, WOOCOMMERCE_CLIENT_SECRET_OPTION);
    register_setting(KINGUIN_API_OPTION_GROUP, KINGUIN_PROFIT_PERCENTAGE_OPTION);

    // Add settings section
    add_settings_section('kinguin_api_section', __('Kinguin API Settings', 'kinguin-api-for-woocommerce-product'), null, 'kinguin-sync-settings');

    // Add settings fields
    add_settings_field(KINGUIN_BASE_URL_OPTION, __('Kinguin API Base URL', 'kinguin-api-for-woocommerce-product'), 'kinguin_base_url_callback', 'kinguin-sync-settings', 'kinguin_api_section');
    add_settings_field(KINGUIN_API_KEY_OPTION, __('Kinguin API Key', 'kinguin-api-for-woocommerce-product'), 'kinguin_api_key_callback', 'kinguin-sync-settings', 'kinguin_api_section');
    add_settings_field(WOOCOMMERCE_CLIENT_ID_OPTION, __('WooCommerce Client ID', 'kinguin-api-for-woocommerce-product'), 'woocommerce_client_id_callback', 'kinguin-sync-settings', 'kinguin_api_section');
    add_settings_field(WOOCOMMERCE_CLIENT_SECRET_OPTION, __('WooCommerce Client Secret', 'kinguin-api-for-woocommerce-product'), 'woocommerce_client_secret_callback', 'kinguin-sync-settings', 'kinguin_api_section');
    add_settings_field(KINGUIN_PROFIT_PERCENTAGE_OPTION, __('Profit Percentage', 'kinguin-api-for-woocommerce-product'), 'kinguin_profit_percentage_callback', 'kinguin-sync-settings', 'kinguin_api_section');
}


// Callback functions for rendering input fields.
function kinguin_base_url_callback() {
    $value = esc_attr(get_option(KINGUIN_BASE_URL_OPTION));
    echo '<input type="text" name="' . KINGUIN_BASE_URL_OPTION . '" value="' . $value . '" class="regular-text" />';
    ?>
    <p>
        <strong><?php _e('Production', 'kinguin-api-for-woocommerce-product'); ?>:</strong>
        <a href="https://www.kinguin.net/integration/" target="_blank">https://www.kinguin.net/integration/</a>
    </p>
    <p>
        <strong><?php _e('Sandbox', 'kinguin-api-for-woocommerce-product'); ?>:</strong>
        <a href="https://www.sandbox.kinguin.net/integration/" target="_blank">https://www.sandbox.kinguin.net/integration/</a>
    </p>
    <?php
}

function kinguin_api_key_callback() {
    $value = esc_attr(get_option(KINGUIN_API_KEY_OPTION));
    echo '<input type="text" name="' . KINGUIN_API_KEY_OPTION . '" value="' . $value . '" class="regular-text" />';
}

function woocommerce_client_id_callback() {
    $value = esc_attr(get_option(WOOCOMMERCE_CLIENT_ID_OPTION));
    echo '<input type="text" name="' . WOOCOMMERCE_CLIENT_ID_OPTION . '" value="' . $value . '" class="regular-text" />';
}

function woocommerce_client_secret_callback() {
    $value = esc_attr(get_option(WOOCOMMERCE_CLIENT_SECRET_OPTION));
    echo '<input type="text" name="' . WOOCOMMERCE_CLIENT_SECRET_OPTION . '" value="' . $value . '" class="regular-text" />';
}

function kinguin_profit_percentage_callback() {
    $value = esc_attr(get_option(KINGUIN_PROFIT_PERCENTAGE_OPTION));
    echo '<input type="number" step="0.01" name="' . KINGUIN_PROFIT_PERCENTAGE_OPTION . '" value="' . $value . '" class="small-text" /> %';
}

// Access the settings wherever needed.
$base_url = get_option(KINGUIN_BASE_URL_OPTION);
$api_key = get_option(KINGUIN_API_KEY_OPTION);
$client_id = get_option(WOOCOMMERCE_CLIENT_ID_OPTION);
$client_secret = get_option(WOOCOMMERCE_CLIENT_SECRET_OPTION);
$profit_percentage = get_option(KINGUIN_PROFIT_PERCENTAGE_OPTION);

?>