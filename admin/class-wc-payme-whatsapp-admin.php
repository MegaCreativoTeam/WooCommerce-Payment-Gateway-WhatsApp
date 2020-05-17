<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/brayan2rincon
 * @since      2.0.0
 *
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/admin
 * @author     Brayan Rincon <brayan262@gmail.com>
 */
class Wc_Payme_Whatsapp_Admin
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Payme_Whatsapp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Payme_Whatsapp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, WCPWS_PATH . 'css/wc-payme-whatsapp-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts()
	{
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Payme_Whatsapp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Payme_Whatsapp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, WCPWS_PATH . 'js/wc-payme-whatsapp-admin.js', array( 'jquery' ), $this->version, false );
	}

}
