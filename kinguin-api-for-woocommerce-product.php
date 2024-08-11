<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://https://github.com/mtmsujan
 * @since             1.0.0
 * @package           Kinguin_Api_For_Woocommerce_Product
 *
 * @wordpress-plugin
 * Plugin Name:        Kinguin API for WooCommerce Product
 * Plugin URI:        https://github.com/mtmsujan/kinguin-api-for-woocommerce-product
 * Description:       Bulk Product Import Form API.
 * Version:           1.0.0
 * Author:            MTM Sujan
 * Author URI:        https://https://github.com/mtmsujan/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kinguin-api-for-woocommerce-product
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin path
if ( !defined( 'KAPI_PLUGIN_PATH' ) ) {
    define( 'KAPI_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Define plugin uri
if ( !defined( 'KAPI_PLUGIN_URL' ) ) {
    define( 'KAPI_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KINGUIN_API_FOR_WOOCOMMERCE_PRODUCT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kinguin-api-for-woocommerce-product-activator.php
 */
function activate_kinguin_api_for_woocommerce_product() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kinguin-api-for-woocommerce-product-activator.php';
	Kinguin_Api_For_Woocommerce_Product_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kinguin-api-for-woocommerce-product-deactivator.php
 */
function deactivate_kinguin_api_for_woocommerce_product() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kinguin-api-for-woocommerce-product-deactivator.php';
	Kinguin_Api_For_Woocommerce_Product_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kinguin_api_for_woocommerce_product' );
register_deactivation_hook( __FILE__, 'deactivate_kinguin_api_for_woocommerce_product' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kinguin-api-for-woocommerce-product.php';

/**
 * The admin-specific functionality of the plugin.
 */
require plugin_dir_path( __FILE__ ) . 'admin/kinguin-api-for-wordpress-admin-page.php';

/**
 * fetch-api-products-import-to-db file
 */
require plugin_dir_path( __FILE__ ) . 'admin/fetch-api-products-import-to-db.php';

/**
 * API Endpoints
 */
require plugin_dir_path( __FILE__ ) . 'admin/api_endpoints.php';

// product-import-to-woocommerce
require plugin_dir_path( __FILE__ ) . 'admin/product-import-to-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kinguin_api_for_woocommerce_product() {

	$plugin = new Kinguin_Api_For_Woocommerce_Product();
	$plugin->run();

}
run_kinguin_api_for_woocommerce_product();
