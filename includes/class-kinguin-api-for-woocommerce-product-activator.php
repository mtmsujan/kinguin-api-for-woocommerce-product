<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/mtmsujan
 * @since      1.0.0
 *
 * @package    Kinguin_Api_For_Woocommerce_Product
 * @subpackage Kinguin_Api_For_Woocommerce_Product/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Kinguin_Api_For_Woocommerce_Product
 * @subpackage Kinguin_Api_For_Woocommerce_Product/includes
 * @author     MTM Sujan <mtmsujon@gmail.com>
 */
class Kinguin_Api_For_Woocommerce_Product_Activator {

    /**
     * Method to run during plugin activation.
     *
     * Creates the sync_products table with columns for kinguinid, status, created_at, and updated_at.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'sync_products';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT AUTO_INCREMENT,
            kinguinid VARCHAR(255) NOT NULL,
            status VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            PRIMARY KEY (id)
        )";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

}

