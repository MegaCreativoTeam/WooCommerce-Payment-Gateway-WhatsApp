<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/brayan2rincon
 * @since      2.0.0
 *
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Wc_Payme_Whatsapp
 * @subpackage Wc_Payme_Whatsapp/includes
 * @extends    WooCommerce/Abstracts/WC_Payment_Gateway
 * @author     Brayan Rincon <brayan262@gmail.com>
 */
class Wc_Payme_Whatsapp extends WC_Payment_Gateway 
{
    const API_WHSTSAPP = "http://api.whatsapp.com/send?phone=%s&text=%s";
    
    /**
    * Undocumented function
    */
    public function __construct()
    {
        $this->id                   = 'wc-payme-whatsapp';
        $this->icon                 = '';
        $this->has_fields           = true;
        $this->method_title         = __( 'PayMe for WhatsApp', 'wc-payme-whatsapp' );
        $this->method_description   = __('A WooCommerce extension that adds a payment gateway for WhatsApp. At the end of the purchase a message is created with the data of the Order and the products of the same and it is sent through WhatsApp.');

        // gateways can support subscriptions, refunds, saved payment methods,
        // but in this tutorial we begin with simple payments
        $this->supports = array(
            'products'
        );

        // Method with all the options fields
        $this->init_form_fields();
    
        // Load the settings.
        $this->init_settings();
        $this->title              = $this->get_option( 'title' );
        $this->description        = $this->get_option( 'description' );
        $this->enabled            = $this->get_option( 'enabled' );
        $this->testmode           = 'yes' === $this->get_option( 'testmode' );
        $this->order_button_text  = apply_filters( 'woocommerce_wcpws_order_button_text', __( 'Place order', 'wc-payme-whatsapp' ) );
        $this->instructions       = $this->get_option( 'instructions' );
        $this->enable_for_methods = $this->get_option( 'enable_for_methods', [] );

        if ( is_admin() ) {
            // This action hook saves the settings
            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options'] );
            
            add_action( 'woocommerce_thankyou_wcpws', [$this, 'thankyou'] );

            // We need custom JavaScript to obtain a token
	        add_action( 'wp_enqueue_scripts', [$this, 'payment_scripts'] );
        }
    }

    /**
     * Enqueue scripts
     */
    public function payment_scripts()
    {
        // we need JavaScript to process a token only on cart/checkout pages, right?
        if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
            return;
        }
    
        // if our payment gateway is disabled, we do not have to enqueue JS too
        if ( 'no' === $this->enabled ) {
            return;
        }

        // and this is our custom JS in your plugin directory that works with token.js
	    //wp_register_script( 'woocommerce_payme_whatsapp', plugins_url('public/js/wc-payme-whatsapp.js', WCPWS_PATH ), array( 'jquery' ) );
        
        //wp_enqueue_script( 'woocommerce_payme_whatsapp' );

    }
 
    /**
     * Depending on the payment processor you use, the option fields could be different, 
     * but in most cases you will have “Enabled/Disabled”, “Title”, “Description” and 
     * “Test mode” options.
     *
     * @return void
     */
    public function init_form_fields()
    {
        $shipping_methods = [];

        if ( is_admin() ) {
            foreach ( WC()->shipping->load_shipping_methods() as $method ) {
                $shipping_methods[$method->id] = $method->get_title();
            }
        }
        
        $this->form_fields = [
            'enabled' => [
                'title'         => __('Enable/Disable', 'wc-payme-whatsapp'),
                'label'         => __('Enable PayMe for WhatsApp', 'wc-payme-whatsapp'),
                'description'   => '',
                'type'          => 'checkbox',
                'default'       => 'yes'
            ],

            'testmode' => [
                'title'       => __('Test mode'),
                'label'       => __('Enable Test Mode'),
                'type'        => 'checkbox',
                'description' => 'Place the payment gateway in test mode using test API keys.',
                'default'     => 'yes',
                'desc_tip'    => true,
            ],

            'whatsapp_number' => [
                'title'         => __('Phone', 'wc-payme-whatsapp'),                
                'description'   => __('WhatsApp number', 'wc-payme-whatsapp'),
                'type'          => 'tel',
                'css'           => 'width: 150px; max-width: 150px;',
                'default'       => '',
                'desc_tip'      => true,
            ],

            'message' => [
                'title'         => __(' Message', 'wc-payme-whatsapp'),
                'type'          => 'textarea',
                'description'   => __("Payment method description which the customer will see during checkout.<br/>Wildcards to display order data: <br/><strong>%SITE%</strong> Site name. <br/><strong>%AMOUNT%</strong> Amount order. <br/><strong>%CURRENCY%</strong> Currency. <br/><strong>%CLIENT_NAME%</strong> Customer's full name. <br/><strong>%CLIENT_PHONE%</strong> Customer's phone number.<br/> <strong>%DETATILS%</strong> Order details, contains all the Items, quantity and total of each", 'wc-payme-whatsapp'),
                'css'           => 'width: 500px; max-width: 95%; height: 100px',
                'default'       => __(
                    '*NEW ORDER CREATED IN "%SITE%". NRO %ORDER_ID% - (%AMOUNT% %CURRENCY%)* | ' . PHP_EOL .
                    '*CLIENT:* %CLIENT_NAME% | ' . PHP_EOL .
                    '*PHONE:* %CLIENT_PHONE% | ' . PHP_EOL .
                    '*PRODUCTS:* ' . PHP_EOL .
                    '%DETATILS%' . PHP_EOL
                ),
                
            ],

            'title' => [
                'title'         => __('Title', 'wc-payme-whatsapp'),
                'description'   => __('Payment method title which the customer will see during checkout', 'wc-payme-whatsapp'),
                'type'          => 'text',
                'css'           => 'width: 600px; max-width: 95%;',
                'default'       => __('PayMe for WhatsApp', 'wc-payme-whatsapp'),
                'desc_tip'      => true,
            ],

            'description' => [
                'title'         => __('Description', 'wc-payme-whatsapp'),
                'type'          => 'textarea',
                'description'   => __('Payment method description which the customer will see during checkout', 'wc-payme-whatsapp'),
                'css'           => 'width: 600px; max-width: 95%; height: 100px',
                'default'       => __(''),
                'desc_tip' => true,
            ],

            'instructions' => [
                'title'         => __('Instructions', 'wc-payme-whatsapp'),
                'description'   => __('Instructions that will be added to the thank you page.', 'wc-payme-whatsapp'),
                'type'          => 'textarea',
                'default'       => '',
                'css'           => 'width: 600px; max-width: 95%;  height: 100px',
                'desc_tip'      => true,
            ],

            'enable_for_methods' => [
                'title'         => __('Enable for shipping methods', 'wc-payme-whatsapp'),
                'description'   => __('Set up shipping methods that are available for  PayMe for WhatsApp. Leave blank to enable for all shipping methods.', 'wc-payme-whatsapp'),
                'type'          => 'multiselect',
                'class'         => 'chosen_select',
                'css'           => 'width: 600px; max-width: 95%;',
                'default'       => '',                
                'options'       => $shipping_methods,
                'desc_tip'      => true,
            ]
        ];
    }
    
    /**
     * Create a payment form with card fields like this
     *
     * @return void
     */
    public function payment_fields()
    {
        // ok, let's display some description before the payment form
        if ( $this->description ) {
            // you can instructions for test mode, I mean test card numbers etc.
            if ( $this->testmode ) {
                $this->description .= ' <strong>TEST MODE ENABLED</strong>.';
                $this->description  = trim( $this->description );
            }
            // display the description with <p> tags etc.
            echo wpautop( wp_kses_post( $this->description ) );
        }

        // I will echo() the form, but you can close PHP tags and print it directly in HTML
        /*echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';
    
        // Add this action hook if you want your custom payment gateway to support it
        do_action( 'woocommerce_credit_card_form_start', $this->id );
    
        // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
        echo '<div class="form-row form-row-wide">
                <label>WhatsApp number <span class="required">*</span></label>
                <input id="client_whatsapp" name="client_whatsapp" type="tel">
            </div>
            <div class="clear"></div>';
    
        //do_action( 'woocommerce_credit_card_form_end', $this->id );
    
        echo '<div class="clear"></div></fieldset>';*/
    
    }

    /**
     * I know that checkout fields like First name should be validated earlier
     *
     * @return void
     */
    public function validate_fields()
    {
        if( empty( $_POST[ 'billing_phone' ]) ) {
            wc_add_notice(  'Phone number is required!', 'error' );
            return false;
        }
        return true;
     
    } // end function

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function is_available()
    {
        if ( ! empty( $this->enable_for_methods ) ){

            $chosen_shipping_methods_session = WC()->session->get( 'chosen_shipping_methods' );
            
            if ( isset( $chosen_shipping_methods_session ) ) {
                $chosen_shipping_methods = array_unique( $chosen_shipping_methods_session );
            }
            else {
                $chosen_shipping_methods = array();
            }

            $check_method = false;

            if ( is_page( wc_get_page_id( 'checkout' ) ) && ! empty( $wp->query_vars['order-pay'] ) ){
                $order_id = absint( $wp->query_vars['order-pay'] );
                $order    = new WC_Order( $order_id );

                if ( $order->shipping_method ){
                    $check_method = $order->shipping_method;
                }                            
            }
            elseif ( empty( $chosen_shipping_methods ) || sizeof( $chosen_shipping_methods ) > 1 ) {
                $check_method = false;
            }
            elseif ( sizeof( $chosen_shipping_methods ) == 1 ) {
                $check_method = $chosen_shipping_methods[0];
            }

            if ( ! $check_method ){
                return false;
            }
                
            $found = false;

            foreach ( $this->enable_for_methods as $method_id ) {
                if ( strpos( $check_method, $method_id ) === 0 ) {
                    $found = true;
                    break;
                }
            }

            if ( ! $found ){
                return false;
            }
                
        }

        return parent::is_available();
    }

    /**
     * Process order payment
     *
     * @param int $order_id
     * @return void
     */
    public function process_payment( $order_id )
    {
        global $woocommerce;

        $order           = new WC_Order( $order_id );
        $whatsapp_number = $this->settings['whatsapp_number'];
        $template        = $this->settings['message'];

        $order_data      = $order->get_data(); 
        $client_name     = $order_data['billing']["first_name"] .' '. $order_data['billing']['last_name'];
        $client_phone    = $order_data['billing']['phone'];
        $amount          = $order->get_total();
        $currency        = get_woocommerce_currency();
        $products        = $order->get_items();        
        $details         = [];

        foreach($products as $product){
            $name       = $product['name'];                        
            $quantity   = $product['quantity']; 
            $total      = $product['total'];
            $details[]  = "[$quantity] " . $name . " $total ";
        }
        
        $details = implode(' | ', $details);
        $message = str_ireplace(
            [
                '%SITE%', '%ORDER_ID%', '%AMOUNT%', '%CURRENCY%', '%CLIENT_NAME%', '%CLIENT_PHONE%', '%DETATILS%'
            ], [
                get_bloginfo('name'), $order_id, $amount, $currency, $client_name, $client_phone, $details
            ], $template);
                       

        // Mark as on-hold
        $order->update_status( 'on-hold', __('Awaiting offline payment', 'wc-payme-whatsapp') );
        
        // Reduce stock levels
        $order->reduce_order_stock();
        
        // Remove cart
        WC()->cart->empty_cart();

        $redirect = sprintf(self::API_WHSTSAPP, $whatsapp_number, $message);

        $result = [
            'result'    => 'success',
            'redirect'  => $redirect
        ];

        return $result;
    }

    /**
     * Output for the order received page.
     *
     * @return void
     */
    public function thankyou()
    {
        if ( $this->instructions ) {
            echo wpautop( wptexturize( wp_kses_post( $this->instructions ) ) ) ;
        }        
    }

    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false )
    {
        if ( $this->instructions && ! $sent_to_admin && 'offline' === $order->payment_method && $order->has_status( 'on-hold' ) ) {
            echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
        }
    }

}


