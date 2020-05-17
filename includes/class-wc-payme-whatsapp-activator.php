<?php

/**
 * 
 *
 * @link       https://github.com/brayan2rincon
 * @since      2.0.0
 *
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/includes
 */

/**
 * 
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/includes
 * @author     Brayan Rincon <brayan262@gmail.com>
 */
class Wc_Payme_Whatsapp_Activator
{
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;
	
		$table_name = $wpdb->prefix . "wc_payme_whatsapp";
		$charset_collate = $wpdb->get_charset_collate();

		if($wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name) {

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table_name (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				slug LONGTEXT NULL DEFAULT NULL,
				content LONGTEXT NULL DEFAULT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;";
			$created = dbDelta($sql);
		}
	}

}
