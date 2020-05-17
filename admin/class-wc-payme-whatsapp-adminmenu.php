<?php
/**
 * Creates the submenu item for the plugin.
 *
 * @package Custom_Admin_Settings
 */
 
/**
 * Creates the submenu item for the plugin.
 *
 * Registers a new menu item under 'Tools' and uses the dependency passed into
 * the constructor in order to display the page corresponding to this menu item.
 *
 * @package Custom_Admin_Settings
 */
class Wc_Payme_Whatsapp_Adminmenu 
{  
    /**
     * Adds a submenu for this plugin to the 'Tools' menu.
     */
    public function run()
    {
        //add_action( 'admin_menu', [$this, 'add_admin_menu'] );
    }

    /**
     * Creates the submenu item and calls on the Submenu Page object to render
     * the actual contents of the page.
     */
    public function add_admin_menu()
    { 
        add_menu_page(
            __( 'Payments WhatsApp', 'wc-payme-whatsapp' ),
            __( 'Payments WhatsApp', 'wc-payme-whatsapp' ),
            'administrator', // Capability requirement to see the link
            'payme-whatsapp',
            [$this, 'render_main_page'],
            'dashicons-phone'
        );

        add_submenu_page( 'payme-whatsapp',
            __( 'Settings Payments WhatsApp', 'wc-payme-whatsapp' ),
            __( 'Settings', 'wc-payme-whatsapp' ),
            'administrator',
            'payme-whatsapp-settings',
            [$this, 'render_settings_page']
        ); 
    }

    /**
     * This function renders the contents of the page associated with the Submenu
     * that invokes the render method. In the context of this plugin, this is the
     * Submenu class.
     */
    public function render_main_page()
    {
        echo '.';
    }

    /**
     * This function renders the contents of the page associated with the Submenu
     * that invokes the render method. In the context of this plugin, this is the
     * Submenu class.
     */
    public function render_settings_page()
    {
        include_once WCPWS_PATH . 'admin/partials/settings.php';
    }

}
