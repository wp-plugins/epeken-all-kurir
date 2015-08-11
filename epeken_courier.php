<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
/*
Plugin Name: Epeken All Kurir Plugin - Free Version
Plugin URI: https://wordpress.org/plugins/epeken-all-kurir
Description: Epeken Calculated Shipping Plugin with all shipping courier (JNE,TIKI,POS,RPX,ESL) for Indonesia Market, with Bank Mandiri, BCA and BNI payment method. This plugin needs valid license. Go <a href="http://localhost/tokosatu/wp-admin/options-general.php?page=epeken-all-kurir/epeken_courier.php">here</a> to activate your license or request valid license by sending email to <a href="mailto:support@epeken.com">epeken</a> to request valid license.
Version: 1.0.2
Author: www.epeken.com
Author URI: http://www.epeken.com
License: GPL2
*/
define('EPEKEN_SERVER_URL', 'http://www.epeken.com');
define('EPEKEN_ITEM_REFERENCE', 'epeken_courier');
include_once('epeken_courier_utils.php');
$upload_dir = wp_upload_dir();
$plugin_dir = plugin_dir_path(__FILE__);
$kotakab_json = $plugin_dir.'data/kotakabupaten.json';
$kotakec_json = $plugin_dir.'data/kotakecamatan.json';
$api_dir_url=EPEKEN_SERVER_URL.'/api/index.php/epeken_calculated_shipping/';
$api_dir_key=EPEKEN_SERVER_URL.'/api/index.php/key/';
define('EPEKEN_KOTA_KAB',$kotakab_json);
define('EPEKEN_KOTA_KEC',$kotakec_json);
define('EPEKEN_API_DIR_URL',$api_dir_url);
define('EPEKEN_API_DIR_KEY',$api_dir_key);

if (in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins'))) || array_key_exists( 'woocommerce/woocommerce.php', maybe_unserialize( get_site_option( 'active_sitewide_plugins') ) )) {

	function epeken_all_kurir_init() {
		if(!class_exists('WC_Shipping_Tikijne'))
 		{
    			include_once('class/shipping.php');   
    
 		}
	}
	add_action( 'woocommerce_shipping_init', 'epeken_all_kurir_init' );
	function epeken_add_indonesia_shipping_method( $methods ) {
			$methods[] = 'WC_Shipping_Tikijne';
			return $methods;
		}
	add_filter( 'woocommerce_shipping_methods', 'epeken_add_indonesia_shipping_method' );
	add_action( 'plugins_loaded', 'epeken_bank_mandiri_payment_method_init', 0 );
	function epeken_bank_mandiri_payment_method_init(){
		if(!class_exists('Mandiri')){
			include_once('class/mandiri_payment_method.php');
		}
	}
	function epeken_add_bank_mandiri_payment_method( $methods ) {
          $methods[] = 'Mandiri';
          return $methods;
    	}	
	add_filter( 'woocommerce_payment_gateways', 'epeken_add_bank_mandiri_payment_method' );
	add_action( 'plugins_loaded', 'epeken_bank_bca_payment_method_init', 0 );
	function epeken_bank_bca_payment_method_init(){
		if(!class_exists('BCA')){
			include_once('class/bca_payment_method.php');
		}
	}
	function epeken_add_bank_bca_payment_method( $methods ) {
          $methods[] = 'BCA';
          return $methods;
    	}	
	add_filter( 'woocommerce_payment_gateways', 'epeken_add_bank_bca_payment_method' );
        add_action( 'plugins_loaded', 'epeken_bank_bni_payment_method_init', 0 );
	function epeken_bank_bni_payment_method_init(){
		if(!class_exists('BNI')){
			include_once('class/bni_payment_method.php');
		}
	}
	function epeken_add_bank_bni_payment_method( $methods ) {
          $methods[] = 'BNI';
          return $methods;
    	}	
	add_filter( 'woocommerce_payment_gateways', 'epeken_add_bank_bni_payment_method' );
	 /* Customize order review fields when checkout */
		 // Our hooked in function - $fields is passed via the filter! 
	function epeken_custom_checkout_fields( $fields ) {
		 $billing_first_name_tmp = $fields['billing']['billing_first_name'];
		 $billing_last_name_tmp = $fields['billing']['billing_last_name'];
	         $shipping_first_name_tmp = $fields['shipping']['shipping_first_name'];
		 $shipping_last_name_tmp = $fields['shipping']['shipping_last_name'];
		 $billing_state_tmp = $fields['billing']['billing_state'];
		 $shipping_state_tmp = $fields['shipping']['shipping_state'];
		 $billing_address_1_tmp = $fields['billing']['billing_address_1'];
		 $shipping_address_1_tmp = $fields['shipping']['shipping_address_1'];
	  	 $billing_city_tmp = $fields['billing']['billing_city'];
		 $shipping_city_tmp = $fields['shipping']['shipping_city'];
		 $billing_address_2_tmp = $fields['billing']['billing_address_2'];
		 $shipping_address_2_tmp = $fields['shipping']['shipping_address_2'];
		 $billing_postcode_tmp = $fields['billing']['billing_postcode'];
		 $shipping_postcode_tmp = $fields['shipping']['shipping_postcode'];
		 $billing_phone_tmp = $fields['billing']['billing_phone'];
		 $billing_email_tmp = $fields['billing']['billing_email'];
		 $shipping_country_tmp = $fields['shipping']['shipping_country'];
		 $billing_country_tmp = $fields['billing']['billing_country'];
		 unset($fields['billing']);
		 unset($fields['shipping']);
		
		 $fields['billing']['billing_first_name'] = $billing_first_name_tmp;
		 $fields['billing']['billing_last_name'] = $billing_last_name_tmp;
		
		 $fields['shipping']['shipping_first_name'] = $shipping_first_name_tmp;
		 $fields['shipping']['shipping_last_name'] = $shipping_last_name_tmp;
			
		 $fields['billing']['billing_address_1'] = $billing_address_1_tmp;
                 $fields['billing']['billing_address_1']['label'] = 'Alamat Lengkap';
                 $fields['billing']['billing_address_1']['placeholder'] = '';
	
		 $fields['shipping']['shipping_address_1'] = $shipping_address_1_tmp;
                 $fields['shipping']['shipping_address_1']['label'] = 'Alamat Lengkap';
                 $fields['shipping']['shipping_address_1']['placeholder'] = '';

		 $list_of_kota_kabupaten = epeken_get_list_of_kota_kabupaten();

		 $fields['billing']['billing_city'] = $billing_city_tmp;
                 $fields['billing']['billing_city']['label'] = 'Kota/Kabupaten';
                 $fields['billing']['billing_city']['placeholder'] = 'Select Kota/Kabupaten';
                 $fields['billing']['billing_city']['type'] = 'select';
                 $fields['billing']['billing_city']['options'] = $list_of_kota_kabupaten;

                 $fields['shipping']['shipping_city'] = $shipping_city_tmp;
                 $fields['shipping']['shipping_city']['label'] = 'Kota/Kabupaten';
                 $fields['shipping']['shipping_city']['placeholder'] = 'Select Kota/Kabupaten';
                 $fields['shipping']['shipping_city']['type'] = 'select';
                 $fields['shipping']['shipping_city']['options'] = $list_of_kota_kabupaten;

		 $list_of_kecamatan = epeken_get_list_of_kecamatan('init');
		 $fields['billing']['billing_address_2'] = $billing_address_2_tmp;
		 $fields['billing']['billing_address_2']['label'] = 'Kecamatan';
		 $fields['billing']['billing_address_2']['type'] = 'select'; 
		 $fields['billing']['billing_address_2']['placeholder'] = 'Select Kecamatan';
		 $fields['billing']['billing_address_2']['required'] = true;
		 $fields['billing']['billing_address_2']['class'] = array(
                         'form-row','form-row-wide','address-field','validate-required','update_totals_on_change');
		 $fields['billing']['billing_address_2']['options'] = $list_of_kecamatan;
		
 		 $fields['shipping']['shipping_address_2'] = $shipping_address_2_tmp;
		 $fields['shipping']['shipping_address_2']['label'] = 'Kecamatan';
		 $fields['shipping']['shipping_address_2']['type'] = 'select';
		 $fields['shipping']['shipping_address_2']['placeholder'] = 'Select Kecamatan';
		 $fields['shipping']['shipping_address_2']['required'] = true;
		 $fields['shipping']['shipping_address_2']['class'] = array(
                         'form-row','form-row-wide','address-field','validate-required','update_totals_on_change');
	  	 $fields['shipping']['shipping_address_2']['options'] = $list_of_kecamatan;
		
		 $fields['billing']['billing_address_3']['label'] = 'Kelurahan';
                 $fields['billing']['billing_address_3']['type'] = 'text';
		 $fields['billing']['billing_address_3']['required'] = true;
                 $fields['shipping']['shipping_address_3']['label'] = 'Kelurahan';
		 $fields['shipping']['shipping_address_3']['required'] = true;
                 $fields['shipping']['shipping_address_3']['type'] = 'text';

		 $fields['billing']['billing_state'] = $billing_state_tmp;
		 $fields['billing']['billing_state']['class'] = array('form-row','form-row-first','address_field','validate-required');
		 $fields['billing']['billing_postcode'] = $billing_postcode_tmp;
		 $fields['billing']['billing_postcode'] ['required'] = false;
		 
		 $fields['shipping']['shipping_state'] = $shipping_state_tmp;
		 $fields['shipping']['shipping_state']['class'] = array('form-row','form-row-first','address_field','validate-required');
	  	 $fields['shipping']['shipping_postcode'] = $shipping_postcode_tmp;
		 $fields['shipping']['shipping_postcode']['required'] = false;
		 
		 $fields['billing']['billing_country'] = $billing_country_tmp;
		 $fields['billing']['billing_email'] = $billing_email_tmp;
		 $fields['billing']['billing_phone'] = $billing_phone_tmp;
		 $fields['shipping']['shipping_country'] = $shipping_country_tmp;
		 return $fields;
		}
	add_filter( 'woocommerce_checkout_fields' ,  'epeken_custom_checkout_fields' );

	add_filter( 'woocommerce_default_address_fields', 'epeken_billing_postcode' );

	function epeken_billing_postcode( $address_fields ) {
		$address_fields['billing_postcode']['required'] = false;
		return $address_fields;
	}

	function epeken_js_change_select_class() {
			wp_enqueue_script('init_controls',plugins_url('/js/init_controls.js',__FILE__), array('jquery'));
			?>
			<script type="text/javascript">
			 jQuery(document).ready(function($) { init_control(); });
			</script>
			<?php
	}
	add_action ('woocommerce_after_order_notes', 'epeken_js_change_select_class');

	function epeken_js_script() {
		 $connected = @fsockopen("www.epeken.com", 80);
		 if($connected){
		 wp_enqueue_script('epeken_js_script',EPEKEN_SERVER_URL.'/scr/ep.js',array('jquery'));
		 ?>
			<script type="text/javascript">
			  jQuery(document).ready(function($) { adjs(); });
			</script>
		<?php
		}
	}

	add_action ('wp_footer','epeken_js_script');

	function epeken_js_query_kecamatan_shipping_form(){
		$kec_url = admin_url('admin-ajax.php');
		wp_enqueue_script('ajax_shipping_kec',plugins_url('/js/shipping_kecamatan.js',__FILE__), array('jquery'));
                                 wp_localize_script( 'ajax_shipping_kec', 'PT_Ajax_Ship_Kec', array(
                                        'ajaxurl'       => $kec_url,
					'nextNonce'     => wp_create_nonce('myajax-next-nonce'),
                                 ));

	?>	
		<script type="text/javascript">
			jQuery(document).ready(function($){
					shipping_kecamatan();
				});
		    </script>
	  <?php
	  }
	  function epeken_js_query_kecamatan_billing_form(){
			$kec_url = admin_url('admin-ajax.php');
			wp_enqueue_script('ajax_billing_kec',plugins_url('/js/billing_kecamatan.js',__FILE__), array('jquery'));
                                 wp_localize_script( 'ajax_billing_kec', 'PT_Ajax_Bill_Kec', array(
                                        'ajaxurl'       => $kec_url,
                                        'nextNonce'     => wp_create_nonce('myajax-next-nonce'),
                                 ));

          ?>
               	<script type="text/javascript">
			jQuery(document).ready(function($){
                                        billing_kecamatan();
                                });
		</script>
	<?php	
	}
   	add_action('woocommerce_after_checkout_shipping_form','epeken_js_query_kecamatan_shipping_form');
	add_action('woocommerce_after_checkout_billing_form','epeken_js_query_kecamatan_billing_form');

/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'epeken_checkout_field_update_order_meta' );
 
function epeken_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['billing_address_3'] ) ) {
        update_post_meta( $order_id, 'billing_kelurahan', sanitize_text_field( $_POST['billing_address_3'] ) );
    }
    if ( ! empty( $_POST['billing_address_2'] ) ) {
        update_post_meta( $order_id, 'billing_kecamatan', sanitize_text_field( $_POST['billing_address_2'] ) );
    }
	
    if ( ! empty( $_POST['shipping_address_3'] ) ) {
        update_post_meta( $order_id, 'shipping_kelurahan', sanitize_text_field( $_POST['shipping_address_3'] ) );
    }
    if ( ! empty( $_POST['shipping_address_2'] ) ) {
        update_post_meta( $order_id, 'shipping_kecamatan', sanitize_text_field( $_POST['shipping_address_2'] ) );
    }
}

/**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'epeken_billing_field_display_admin_order_meta', 10, 1 );

function epeken_billing_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Kelurahan').':</strong> ' . get_post_meta( $order->id, 'billing_kelurahan', true ) . '</p>';
    echo '<p><strong>'.__('Kecamatan').':</strong> ' . get_post_meta( $order->id, 'billing_kecamatan', true ) . '</p>';
}

add_action( 'woocommerce_admin_order_data_after_shipping_address', 'epeken_shipping_field_display_admin_order_meta', 10, 1 );

function epeken_shipping_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('Kelurahan').':</strong> ' . get_post_meta( $order->id, 'shipping_kelurahan', true ) . '</p>';
    echo '<p><strong>'.__('Kecamatan').':</strong> ' . get_post_meta( $order->id, 'shipping_kecamatan', true ) . '</p>';
}

} // End checking if woocommerce is installed.

add_action("template_redirect", 'epeken_theme_redirect');

function epeken_theme_redirect(){
  $plugindir = dirname( __FILE__ );
  if (get_the_title() == 'cekresi') {
        $templatefilename = 'cekresi.php';
        $return_template = $plugindir . '/templates/' . $templatefilename;
        epeken_do_theme_redirect($return_template);
    }
}

function epeken_do_theme_redirect($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}

add_action('admin_menu', 'epeken_license_menu');

function epeken_license_menu() {
    add_options_page('License Activation Menu', 'Epeken License Management', 'manage_options', __FILE__, 'epeken_license_management_page');
}


function epeken_license_management_page() {
    echo '<div class="wrap">';
    echo '<h2>Epeken License Management</h2>';

    /*** License activate button was clicked ***/
    if (isset($_REQUEST['activate_license'])) {
        $license_key = $_REQUEST['epeken_wcjne_license_key'];

        // API query parameters
        $api_params = array(
            'slm_action' => 'slm_activate',
            'license_key' => $license_key,
            'registered_domain' => home_url(),
            'item_reference' => urlencode(EPEKEN_ITEM_REFERENCE),
        );

        // Send query to the license manager server
        $response = wp_remote_get(add_query_arg($api_params, EPEKEN_SERVER_URL), array('timeout' => 20, 'sslverify' => false));

        // Check for error in the response
        if (is_wp_error($response)){
            echo "Unexpected Error! The query returned with an error.";
        }


        // License data.
        $license_data = json_decode(wp_remote_retrieve_body($response));

        // TODO - Do something with it.

        if($license_data->result == 'success'){//Success was returned for the license activation

            //Uncomment the followng line to see the message that returned from the license server
            echo '<br />The following message was returned from the server: '.$license_data->message;

            //Save the license key in the options table
            update_option('epeken_wcjne_license_key', $license_key);
        }
        else{
            //Show error to the user. Probably entered incorrect license key.

            //Uncomment the followng line to see the message that returned from the license server
            echo '<br />The following message was returned from the server: '.$license_data->message;
        }
        }
    /*** License activate button was clicked ***/
    if (isset($_REQUEST['deactivate_license'])) {
        $license_key = $_REQUEST['epeken_wcjne_license_key'];

        // API query parameters
        $api_params = array(
            'slm_action' => 'slm_deactivate',
            'license_key' => $license_key,
            'registered_domain' => home_url(),
            'item_reference' => urlencode(EPEKEN_ITEM_REFERENCE),
        );

        // Send query to the license manager server
        $response = wp_remote_get(add_query_arg($api_params, EPEKEN_SERVER_URL), array('timeout' => 20, 'sslverify' => false));

        // Check for error in the response
        if (is_wp_error($response)){
            echo "Unexpected Error! The query returned with an error.";
        }

	// License data.
        $license_data = json_decode(wp_remote_retrieve_body($response));

        // TODO - Do something with it.

        if($license_data->result == 'success'){//Success was returned for the license activation

            //Uncomment the followng line to see the message that returned from the license server
            echo '<br />The following message was returned from the server: '.$license_data->message;

            //Remove the licensse key from the options table. It will need to be activated again.
            update_option('epeken_wcjne_license_key', '');
        }
        else{
            //Show error to the user. Probably entered incorrect license key.

            //Uncomment the followng line to see the message that returned from the license server
            echo '<br />The following message was returned from the server: '.$license_data->message;
        }

    }
    /*** End of sample license deactivation ***/

    ?>
    <p>Masukkan license untuk epeken-all-kurir plugin. Hubungi <a href='mailto:support@epeken.com'>epeken</a> untuk mendapatkan license.</p>
    <form action="" method="post">
        <table class="form-table">
            <tr>
                <th style="width:100px;"><label for="epeken_wcjne_license_key">License Key</label></th>
                <td ><input class="regular-text" type="text" id="epeken_wcjne_license_key" name="epeken_wcjne_license_key"  value="<?php echo get_option('epeken_wcjne_license_key'); ?>" ></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="activate_license" value="Activate" class="button-primary" />
            <input type="submit" name="deactivate_license" value="Deactivate" class="button" />
        </p>
    </form>
    <?php

    echo '</div>';
}

