<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://github.com/mtmsujan
 * @since      1.0.0
 *
 * @package    Kinguin_Api_For_Woocommerce_Product
 * @subpackage Kinguin_Api_For_Woocommerce_Product/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Kinguin_Api_For_Woocommerce_Product
 * @subpackage Kinguin_Api_For_Woocommerce_Product/includes
 * @author     MTM Sujan <mtmsujon@gmail.com>
 */
class Kinguin_Api_For_Woocommerce_Product_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'kinguin-api-for-woocommerce-product',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
