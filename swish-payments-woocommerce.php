<?php

/**
 * Plugin Name: Swish Payments Woocommerce
 * Plugin URI: test.se
 * Author Name: Savvyleads Media AB
 * Author URI: test.se
 * Description: Swish Payments
 * Version: 0.0.1
 * License: 0.0.1
 * License URL: https://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: Swish-Payments 
 */

 if ( ! in_array('woocommerce/woocommerce.php', apply_filters(
    'active_plugins', get_option( 'active_plugins ') ) ) ) return;

add_action( 'plugins_loaded', 'swish_payment_init', 99);

function swish_payment_init() {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
        class WC_Swish_Payments extends WC_Payment_Gateway {
            public function __construct(){
                $this->id = 'swish_payments';
                $this->icon = apply_filters( 'woocommerce_swish_icon', plugins_url('/assets/icon.png', __FILE__ ) );
                $this->has_fields = false;
                $this->method_title = __('Swish Payments', 'swish-payments-woo');
                $this->method_description = __( 'Swish Payments made easy', 'swish-payments-woo');

                $this->title = "Swish";

                $this->init_form_fields();
                $this->init_settings();

                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
            }

            public function init_form_fields() {
                $this-> form_fields = apply_filters( 'swish_payment_fields', array(
                    'enable' => array(
                        'title' => __('Enable/Disable', 'swish-payments-woo'),
                        'type' => 'checkbox',
                        'label' => __('Enable Swish Payments', 'swish-payments-woo'),
                    ),
                    'title' => array(
                        'title' => __( 'IBAN', 'swish-payments-woo'),
                        'type' => 'text',
                        'description' => __('Enter your IBAN number for payouts', 'swish-payments-woo'),
                        'desc_tip' => true,
                    ),
                    'bic_swift' => array(
                        'title' => __( 'BIC/Swift', 'swish-payments-woo'),
                        'type' => 'text',
                        'description' => __('Enter your BIC/Swift number for payouts', 'swish-payments-woo'),
                        'desc_tip' => true,
                    )
                ));
            }

            public function process_payments($order_id) {
                $order_id = wc_get_order( $order_id );

                $order->update_status( 'on_hold', __('Awaiting Swish Payment', 'swish-payments-woo'));

                $order->reduce_order_stock();

                WC()->cart->empty_cart;

                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url( $order )
                );
            }

        }
    }
}

add_filter( 'woocommerce_payment_gateways', 'add_to_swish_payment_gateway');

function add_to_swish_payment_gateway($gateways) {
    $gateways[] = 'WC_Swish_Payments';
    return $gateways;
}