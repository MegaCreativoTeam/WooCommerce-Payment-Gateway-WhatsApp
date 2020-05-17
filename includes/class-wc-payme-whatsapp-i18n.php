<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/brayan2rincon
 * @since      2.0.0
 *
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      2.0.0
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/includes
 * @author     Brayan Rincon <brayan262@gmail.com>
 */
class Wc_Payme_Whatsapp_i18n
{
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain(
			'wc-payme-whatsapp',
			false,
			dirname( dirname( WCPWS_PATH ) ) . '/languages/'
		);
	}
}
