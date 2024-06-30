<?php
/**
Plugin Name: kwt_sms_965
Plugin URI: https://github.com/amalviju/
Description: Custom SMS
Version: 1.0
Author: qwerty_1999
Author URI: #
*/

class Woo_SMS {
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    public function init() {
        if ( class_exists( 'WooCommerce' ) ) {
            add_action( 'woocommerce_order_status_completed', array( $this, 'completed' ) );
        } else {
            add_action( 'admin_notices', array( $this, 'no_wc' ) );
        }
    }

    public function send_sms( $order, $message ) {
        ////////////////////////////////////////////////////
        $url = "https://www.kwtsms.com/API/send/";
        $username = "username here";
        $password = "password here";
        $sender = "Sender_id_here"; //////////////////////////////////////////
        $mobileNumbers = ltrim( $order->billing_phone, '+' ); ///////////////////
        $lang = 1; // Language code: 1 for English
        $test = 0; // Set to 1 for testing, 0 for actual sending

        ///////////////////////////////////////////////////////////////////////////
        $params = array(
            'username' => $username,
            'password' => $password,
            'sender' => $sender,
            'mobile' => $mobileNumbers,
            'lang' => $lang,
            'test' => $test,
            'message' => $message
        );

        /////////////////////////////////////////////////////////////
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        ////////////////////////////////////////////////////////////////////////
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_message = "Error: " . curl_error($ch);
            return new WP_Error( 'sms_error', $error_message );
        } else {
            return $result;
        }

        ////////////////////////////////////////////////////////
        curl_close($ch);
    }

    public function completed( $order_id ) {
        $order = wc_get_order( $order_id );

        // Check if the customer username is "defaultcustomer"
        $customer_username = $order->get_user()->user_login;
        if ($customer_username === 'defaultcustomer') {
            // If the customer username is "defaultcustomer", exit the function without sending SMS
            return;
        }

        $message = 'Hi there ' . $order->billing_first_name . '! We just processed your order #' . $order->get_order_number() . '. Thanks for Using your website name';

        foreach ( $order->get_items() as $item_id => $item ) {
            $like4card_product_id = check_if_has_like4card_product_id_meta($item->get_product_id());
            if ($like4card_product_id) {
                $cards_details = get_like4card_orders_details($order->get_id(), $like4card_product_id);
                if (!empty($cards_details)) {
                    $serial_codes = array();
                    foreach ($cards_details as $card_details) {
                        $serial_codes[] = esc_html(decrypt_serial($card_details->serial_code));
                    }
                    $message .= "\nSerial Codes: " . implode(', ', $serial_codes);
                }
            }
        }

        $sms = $this->send_sms( $order, $message );

        if ( is_wp_error( $sms ) ) {
            $order->add_order_note( 'Failed to send \'completed\' SMS: ' . $sms->get_error_message() );
        } else {
            $order->add_order_note( 'Completed Order SMS sent' );
        }
    }

    public function no_wc() {
        echo '<div class="error"><p>' . sprintf( __( 'kwt_sms_965 requires %s to be installed and active.', 'kwt_sms_965' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
    }
}

new Woo_SMS();
