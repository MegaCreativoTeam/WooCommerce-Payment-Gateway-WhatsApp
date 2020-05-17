<?php
/**
 * PAYME FOR WHATSAPP
 * 
 * A WooCommerce extension that adds a payment gateway for WhatsApp. 
 * At the end of the purchase a message is created with the data of 
 * the Order and the products of the same and it is sent through WhatsApp.
 * 
 * @package         WC_Payme_WhatsApp
 * @author          Brayan Rincón <brayan262@gmail.com>
 * @version         2.0.0
 * @copyright    	Copyright (c) 2017-2020, Brayan Rincon - MEGA CREATIVO
 * @link         	https://github.com/brayan2rincon
 * 
 * Plugin Name:		Payme for Whatsapp
 * Description:		A WooCommerce Extension that adds payment gateway "Payment Gateway WhatsApp"
 * Plugin URI:		https://labs.megacreativo.com/wordpress/wc-payme-for-whatsapp
 * Author:			Brayan Rincon
 * Author URI:		https://github.com/brayan2rincon
 * License:		    GNU General Public License v2.0
 * License URI:		http://www.gnu.org/licenses/gpl-2.0.html 
 * Domain Path:		/languages
 * Text Domain:		wc-payme-whatsapp
 * Requires WP:  	4.7
 * Requires PHP: 	5.6
 * 
 * Copyright (c) 2017-2020 Brayan Rincon - MEGA CREATIVO
 *
 *     This file is part of Payme for Whatsapp,
 *     a plugin for WordPress.
 *
 *     Payme for Whatsapp is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 2 of the License, or (at your option)
 *     any later version.
 *
 *     Payme for Whatsapp is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */
 
/**
 * Prevent direct access to this file.
 *
 * @since 1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}   

/**
 * Setting constants.
 *
 * @since 2.0.0
 */

/**
 * Currently plugin version.
 * Start at version 2.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WC_PAYME_WHATSAPP_VERSION', '2.0.0' );


/** Plugin directory */
define('WCPWS_PATH', plugin_dir_path(__FILE__));


// Include the main Payme for Whatsapp class.
require_once WCPWS_PATH . 'includes/functions.php';


register_activation_hook( __FILE__, 'activate_wc_payme_whatsapp' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-payme-whatsapp-activator.php
 */
function activate_wc_payme_whatsapp()
{
	require_once WCPWS_PATH . 'includes/class-wc-payme-whatsapp-activator.php';
	Wc_Payme_Whatsapp_Activator::activate();
}


register_deactivation_hook( __FILE__, 'deactivate_wc_payme_whatsapp' );
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-payme-whatsapp-deactivator.php
 */
function deactivate_wc_payme_whatsapp()
{
	require_once WCPWS_PATH . 'includes/class-wc-payme-whatsapp-deactivator.php';
	Wc_Payme_Whatsapp_Deactivator::deactivate();
}

