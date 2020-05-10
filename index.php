<?php

/**
 * Plugin Name:     WooCommerce Payment Gateway WhatsApp
 * Plugin URI:      https://labs.megacreativo.com/wordpress/woocommerce-payment-gateway-whatsapp
 * Description:     A WooCommerce Extension that adds payment gateway "Payment Gateway WhatsApp"
 * Author URI:      http://megacreativo.com
 * License:         MIT
 * 
 * @author      Mega Creativo <brayan262@gmail.com>
 * @version     1.0.1
 * @class       WC_Payment_Gateway_WhatsApp
 * @extends     WC_Payment_Gateway
 */
function wc_pgws_init()
{
    global $woocommerce;

    if( !isset( $woocommerce ) ) {
        return;
    }

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }        

    if( ! class_exists( 'WC_Payment_Gateway_WhatsApp' ) ) :
    
        class WC_Payment_Gateway_WhatsApp extends WC_Payment_Gateway 
        {
            /**
             * Undocumented function
             */
            public function __construct()
            {
                $this->id                = 'wcgpws';
                $this->icon              = apply_filters('woocommerce_wcgpws_icon', '');
                $this->has_fields        = false;
                $this->method_title      = __( 'Payment Gateway WhatsApp', 'wcgpws' );
                $this->order_button_text = apply_filters( 'woocommerce_wcgpws_order_button_text', __( 'Place order', 'wcgpws' ) );
                $this->init_form_fields();
                $this->init_settings();
                $this->title              = $this->get_option( 'title' );
                $this->description        = $this->get_option( 'description' );
                $this->instructions       = $this->get_option( 'instructions' );
                $this->enable_for_methods = $this->get_option( 'enable_for_methods', [] );

                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options'] );
                add_action( 'woocommerce_thankyou_wcgpws', [$this, 'thankyou'] );
            }


            /**
             * Undocumented function
             *
             * @return void
             */
            function admin_options()
            {
                echo 
                '<h3>'._e('Payment Gateway WhatsApp','wcgpws').'</h3>'.
                '<p>'._e('Extra payment gateway with selection for shipping methods', 'wcgpws' ).'</p>'.
                '<table class="form-table">'.$this->generate_settings_html().'</table>';
            }


            /**
             * Undocumented function
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
                        'title' => __('Enable/Disable', 'wcgpws'),
                        'type' => 'checkbox',
                        'label' => __('Enable Payment Gateway WhatsApp', 'wcgpws'),
                        'default' => 'yes'
                    ],
                    'title' => [
                        'title' => __('Title', 'wcgpws'),
                        'type' => 'text',
                        'description' => __('Payment method title which the customer will see during checkout', 'wcgpws'),
                        'default' => __('Payment Gateway WhatsApp', 'wcgpws'),
                        'desc_tip'      => true,
                    ],
                    'description' => [
                        'title' => __('Description', 'wcgpws'),
                        'type' => 'textarea',
                        'description' => __('Payment method description which the customer will see during checkout', 'wcgpws'),
                        'default' => __('
                            <style>
                            .page-header__logo {
                                width: 35px;
                                height: 35px;
                                background-size: auto 35px;
                                overflow: hidden;
                                padding-right: 0;
                                float: left;
                                display: block;
                                background-repeat: no-repeat;
                                background-image: url(https://www-cdn.whatsapp.net/img/v4/whatsapp-logo.svg?v=bfe2fe6);
                            }                            
                            </style>
                            <h2>Información de pago vía WhatsApp.<a class="page-header__logo" href="#"></a></h2><br/><p>Presione el botón <strong>REALIZAR PEDIDO</strong> para continuar</p>', 'wcgpws'),
                        'desc_tip' => true,
                    ],
                    'phone_number' => [
                        'title' => __('Title', 'wcgpws'),
                        'type' => 'text',
                        'description' => __('Phone number', 'wcgpws'),
                        'default' => '',
                        'desc_tip' => true,
                    ],
                    'instructions' => [
                        'title' => __('Instructions', 'wcgpws'),
                        'type' => 'textarea',
                        'description' => __('Instructions that will be added to the thank you page.', 'wcgpws'),
                        'default' => __('<h2><a class="page-header__logo" href="#">Informacion de pago via WhatsApp.</a></h2>', 'wcgpws'),
                        'desc_tip'      => true,
                    ],
                    'enable_for_methods' => [
                        'title'         => __('Enable for shipping methods', 'wcgpws'),
                        'type'          => 'multiselect',
                        'class'         => 'chosen_select',
                        'css'           => 'width: 450px;',
                        'default'       => '',
                        'description'   => __('Set up shipping methods that are available for  Payment Gateway WhatsApp. Leave blank to enable for all shipping methods.', 'wcgpws'),
                        'options'       => $shipping_methods,
                        'desc_tip'      => true,
                    ]
                ];
            }
            

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
             * Undocumented function
             *
             * @param [type] $order_id
             * @return void
             */
            public function process_payment( $order_id )
            {
                $order = new WC_Order( $order_id );            
                $nombre = "";
            
                $description = [];
                $products = $order->get_items();

                foreach($products as $product){
                    if( isset ($product['name']) ) $description[] = $product['name'];
                }

                $description = $nombre.'Productos: '.implode(', ',$description);
                
                $order->update_status( apply_filters( 'wcgpws' ) );
                $order->reduce_order_stock();
                WC()->cart->empty_cart();

                $result = [
                    'result' => 'success',
                    'redirect'  => 'http://api.whatsapp.com/send?phone=+00123456789&text=' . $description
                ];

                return $result;
            }


            /**
             * Undocumented function
             *
             * @return void
             */
            public function thankyou()
            {
                echo $this->instructions != '' ? wpautop( wptexturize( wp_kses_post( $this->instructions ) ) ) : '';
            }

        }

    endif;
}

add_action( 'plugins_loaded', 'wc_pgws_init' );
function add_wc_pgws( $methods )
{
    $methods[] = 'WC_Payment_Gateway_WhatsApp';
    return $methods;
}
add_filter( 'woocommerce_payment_gateways', 'add_wc_pgws' );