<?php

/**
 *
 * @link       https://github.com/brayan2rincon
 * @since      2.0.0
 *
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/includes
 */

/**
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/includes
 * @author     Brayan Rincon <brayan262@gmail.com>
 */
class Wc_Payme_Whatsapp_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;
	
		$table_name = $wpdb->prefix . "wc_payme_whatsapp";
		$charset_collate = $wpdb->get_charset_collate();

		if($wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "DROP TABLE wp_wc_payme_whatsapp";
			$created = dbDelta($sql);
		}
	}

}
