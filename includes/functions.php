<?php
/**
 * 
 * @package         WC_Payme_WhatsApp
 * @author          Mega Creativo <brayan262@gmail.com>
 * @version         2.0.0
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}   


// Include the dependencies needed to instantiate the plugin.
foreach ( glob( WCPWS_PATH . 'admin/*.php' ) as $file ) {
    //include_once $file;
}

require_once WCPWS_PATH . 'includes/class-wc-payme-whatsapp-core.php';


/**
 * Setting internal plugin helper values.
 *
 * @since 1.0.0
 *
 * @uses mc_wcpwsget_info_url()
 *
 * @param string $url_key String of value key
 * @param string $text    String of text and link attribute
 * @param string $class   String of CSS class
 * @return string HTML markup for linked URL.
 */
function mc_wcpws_get_info_link( $url_key = '', $text = '', $class = '' )
{
	$link = sprintf(
		'<a class="%1$s" href="%2$s" target="_blank" rel="nofollow noopener noreferrer" title="%3$s">%3$s</a>',
		strtolower( esc_attr( $class ) ),	//sanitize_html_class( $class ),
		mc_wcpwsget_info_url( $url_key ),
		esc_html( $text )
	);

	return $link;

}  // end function


/**
 * Get URL of specific MSTBA info value.
 *
 * @since 1.0.0
 *
 * @uses mc_wcpws_info_values()
 *
 * @param string $url_key String of value key from array of mc_wcpws_info_values()
 * @param bool   $raw     If raw escaping or regular escaping of URL gets used
 * @return string URL for info value.
 */
function mc_wcpwsget_info_url( $url_key = '', $raw = FALSE )
{
	$mstba_info = (array) mc_wcpws_info_values();

	$output = esc_url( $mstba_info[ sanitize_key( $url_key ) ] );

	if ( TRUE === $raw ) {
		$output = esc_url_raw( $mstba_info[ esc_attr( $url_key ) ] );
	}

	return $output;

}  // end function


/**
 * Setting internal plugin helper values.
 *
 * @since 1.0.0
 *
 * @return array $mstba_info Array of info values.
 */
function mc_wcpws_info_values()
{
	/** Get current user */
	$user = wp_get_current_user();

	$mstba_info = array(
		'issus'         => 'https://github.com/megacreativo/WooCommerce-Payment-Gateway-WhatsApp/issues',
		'license'       => 'GPL-2.0-or-later',
		'url_license'   => 'https://opensource.org/licenses/GPL-2.0',
		'url_donate'    => 'https://www.paypal.me/MegaCreativo',
		'author'        => __( 'Brayan Rincon - MEGA CREATIVO', 'wcpws' ),
		'author_uri'    => 'https://github.com/brayan2rincon',

	);  // end array

	return $mstba_info;

}  // end function


add_filter( 'plugin_row_meta', 'mc_wcpws_plugin_links', 10, 2 );   
/**
 * Add various support links to plugin page.
 *
 * @since 1.0.0
 *
 * @uses mc_wcpws_get_info_link()
 *
 * @param array  $links (Default) Array of plugin meta links
 * @param string $file  URL of base plugin file
 * @return array  $wcpws Array of plugin link strings to build HTML markup.
 */
function mc_wcpws_plugin_links( $links, $file )
{
    /** Capability check */
    if ( ! current_user_can( 'install_plugins' ) ) {
        return $links;
    }

    /** List additional links only for this plugin */
    if ( $file === 'wp-payme-whatsapp/wp-payme-whatsapp.php' ) {

        /* translators: Plugins page listing */
        $links[] = mc_wcpws_get_info_link(
            'issus',
            esc_html_x( 'Issus', 'Plugins page listing', 'wp-payme-whatsapp' ),
            'dashicons-before dashicons-sos'
        );

        /* translators: Plugins page listing */
        $links[] = mc_wcpws_get_info_link(
            'url_donate',
            esc_html_x( 'Donate', 'Plugins page listing', 'wp-payme-whatsapp' ),
            'dashicons-before dashicons-thumbs-up'
        );

    }  // end if plugin links

    return $links;

    

}  // end function


add_action( 'plugins_loaded', 'mc_wcpws_init_plugin' );
/**
 * Starts the plugin
 */
function mc_wcpws_init_plugin()
{
    $woows = new Wc_Payme_Whatsapp_Core;
    $woows->run();
}


add_filter( 'woocommerce_payment_gateways', 'mc_wcpws_add_wc_payme_whatsapp' );
/**
 * Register Payment Gateways
 *
 * @param array $methods
 * @return void
 */
function mc_wcpws_add_wc_payme_whatsapp( $methods )
{
    $methods[] = 'Wc_Payme_Whatsapp';
    return $methods;
}
