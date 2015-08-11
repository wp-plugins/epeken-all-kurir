<?php 

    class WC_Shipping_Tikijne extends WC_Shipping_Method
	{	
		public  $jneclass;
		public  $shipping_cost;
	  	public  $shipping_kecamatan;	
		public  $shipping_city;
		public  $popup_message;
		public  $min_allow_fs;
		public  $total_cart;
		public  $is_free_shipping;
		public  $array_of_tarif;
		public  $destination_province;
		
		public function __construct(){
			$this -> id = 'epeken_courier';
			$this -> method_title = __('Epeken Courier');
			$this -> method_description = __('Shipping Method using Tiki JNE RPX POS Indonesia ESL Express for Indonesia Marketplace');
			$this -> enabled = 'yes';
			$this -> title = 'Epeken Courier';
			$this -> is_free_shipping = false;
			$this -> init();		
			$this -> array_of_tarif = array();	
			$this -> initiate_epeken_options();
		}

 		public function initiate_epeken_options() {
                        if(get_option('epeken_free_pc',false) === false){
                                add_option('epeken_free_pc','','','yes');
                        }
                        if(get_option('epeken_free_pc_q',false) === false) {
                                add_option('epeken_free_pc_1','','','yes');
                        }
                        if(get_option('epeken_enabled_jne',false) === false) {
                                add_option('epeken_enabled_jne','','','yes');
                        }
                        if(get_option('epeken_enabled_tiki',false) === false) {
                                add_option('epeken_enabled_tiki','','','yes');
                        }
                        if(get_option('epeken_enabled_pos',false) === false) {
                                add_option('epeken_enabled_pos','','','yes');
                        }
                        if(get_option('epeken_enabled_rpx',false) === false) {
                                add_option('epeken_enabled_rpx','','','yes');
                        }
                        if(get_option('epeken_enabled_esl',false) === false) {
                                add_option('epeken_enabled_esl','','','yes');
                        }
                }


		public function create_cek_resi_page(){
                        global $user_ID;

                        $pageckresi = get_page_by_title( 'cekresi','page' );
                        if(!is_null($pageckresi))
                          return;

                        $page['post_type']    = 'page';
                        //$page['post_content'] = 'Put your page content here';
                        $page['post_parent']  = 0;
                        $page['post_author']  = $user_ID;
                        $page['post_status']  = 'publish';
                        $page['post_title']   = 'cekresi';
                        $page = apply_filters('epeken_add_new_page', $page, 'teams');

                    $pageid = wp_insert_post ($page);
                    if ($pageid == 0) { /* Add Page Failed */ }

                }

                public function add_cek_resi_page_to_prim_menu(){
                        $menu_name = 'primary';
                        $locations = get_nav_menu_locations();

			if(!isset($locations) || !is_array($locations))
				return;
		
			if(!array_key_exists($menu_name,$locations))
				return;

                        $menu_id = $locations[ $menu_name ] ;
                        $menu_object = wp_get_nav_menu_object($menu_id);

                        if(!$menu_object){
                                return;
                        }
                        $menu_items = wp_get_nav_menu_items($menu_object->term_id);
                        $is_menu_exist = false;
                        foreach ( (array) $menu_items as $key => $menu_item ) {
                                $post_title = $menu_item->post_title;
                                if ($post_title === "Cek Resi"){
                                        $is_menu_exist = true;
                                        break;
                                }
                        }

                        if($is_menu_exist){
                                return;
                        }

                        $url = get_permalink( get_page_by_title( 'cekresi','page' ) );
                        if($url) {
                        wp_update_nav_menu_item($menu_object->term_id, 0, array(
                                'menu-item-title' =>  __('Cek Resi'),
                                'menu-item-url' =>  $url,
                                'menu-item-status' => 'publish')
                                );
                        }

                }

		public function delete_cek_resi(){
                        $menu_name = 'primary';
                        $locations = get_nav_menu_locations();

                        if(!isset($locations) || !is_array($locations))
                                return;

                        if(!array_key_exists($menu_name,$locations))
                                return;

                        $menu_id = $locations[ $menu_name ] ;
                        $menu_object = wp_get_nav_menu_object($menu_id);

                        if(!$menu_object){
                                return;
                        }
                        $menu_items = wp_get_nav_menu_items($menu_object->term_id);
                        $is_menu_exist = false;
                        foreach ( (array) $menu_items as $key => $menu_item ) {
                                $post_title = $menu_item->post_title;
                                if ($post_title === "Cek Resi"){
                                        $is_menu_exist = true;
                                        wp_delete_post($menu_item->ID,true);
                                }
                        }

                        $page = get_page_by_title( 'cekresi','page' ) ;
                        wp_delete_post($page->ID,true);
                }


		public function activate(){
			 global $wpdb;
                        $enable_cekresi = $this -> settings['enable_cekresi_page'];
                        if($enable_cekresi === 'yes') {
                                $this->create_cek_resi_page();
                                $this->add_cek_resi_page_to_prim_menu();
                        }else{
                                $this -> delete_cek_resi();
                        }
		}

		public function div_loading(){
			?>
			<div id="div_load_trf" style='position: fixed; margin: 0 auto; top: 50%; left: 50%; width: 300px; height: 100px; background-color: #FFFFFF; border-radius: 10px;z-index: 9999;border-style: solid; border-color: #F1F1F1;'>
                                        <p style='margin: 10px;'>Message from&nbsp;<a href="http://www.epeken.com" target="_blank">epeken</a><br>
			<?php echo $this->popup_message; ?>
					</p>
                                        <p style='position: relative; float: left; top: -80px; left: 120px; z-index: -1;'><img src='<?php echo plugins_url('assets/load.gif',__FILE__); ?>'</p> 
			<script language='javascript'>
				setTimeout("location.reload(true);",10000);
			</script>
                        </div>
			<?php
		}

		public function writelog($logstr){
			$logdir = plugin_dir_path( __FILE__ )."/log/";
			$sesid = session_id();
			$logfile = fopen ($logdir.$sesid.".log","a");
			$now = date("Y-m-d H:i:s");
			fwrite($logfile,$now.":".$logstr."\n");
			fclose($logfile);
		}

		 public function reset_user_address() {
                                global $current_user;
                                get_currentuserinfo();
                                update_user_meta($current_user -> ID,'billing_city','');
                                 update_user_meta($current_user -> ID,'shipping_city','');
                                update_user_meta($current_user -> ID,'billing_address_1','');
                                 update_user_meta($current_user -> ID,'shipping_address_1','');
                                update_user_meta($current_user -> ID,'billing_address_2','');
                                 update_user_meta($current_user -> ID,'shipping_address_2','');
                }

		public function popup(){

        		do_action('wp_login', "dummytoo");

			?>
			<div  id="div_epeken_popup" style='position: fixed; margin: 0 auto; top: 50%; left: 40%; width: 300px; height: 100px; background-color: #EEEEEE; border-radius: 10px;z-index: 9999;border-style: solid; border-color: #F1F1F1;display: none;'>
                                        <p style='margin: 10px;'>Message from&nbsp;<a href="http://www.epeken.com" target="_blank">epeken</a><br>
                        <?php echo $this->popup_message; ?>
                                        </p>
                                        <p style='position: relative; float: left; top: -50px; left: 120px; z-index: -1;'><img src='<?php echo plugins_url('assets/load.gif',__FILE__); ?>'</p>
                        </div>
			<?php	
		}

		public function load_jne_tariff(){
                                 $ajax_url = admin_url('admin-ajax.php');
				 wp_enqueue_script('ajax_load_jne_tariff',plugins_url('/js/jne_load_tariff.js',__FILE__), array('jquery'));
				 wp_localize_script( 'ajax_load_jne_tariff', 'PT_Ajax', array(
        				'ajaxurl'       => $ajax_url
    				 ));
		}

		public function register_jne_plugin(){
                                 $ajax_url = admin_url('admin-ajax.php');
				 wp_enqueue_script('ajax_epeken_register',plugins_url('/js/register.js',__FILE__), array('jquery'));
				 wp_localize_script( 'ajax_epeken_register', 'PT_Ajax', array(
        				'ajaxurl'       => $ajax_url
    				 ));
		}


		public function init() {
					$this->init_form_fields(); 
					$this->init_settings(); 
					// Save settings in admin if you have any defined, when save button in admin setting screen is clicked
					add_action('woocommerce_update_options_shipping_' . $this->id,array(&$this, 'process_admin_options'));
					// To display new shipping method in woocommerce shipping menu
					add_action('woocommerce_update_options_shipping_methods', array(&$this, 'process_admin_options'));
					$this -> popup_message = "Please wait while loading kecamatan";
       					add_action('woocommerce_before_checkout_billing_form',array(&$this, 'popup'));
					add_action('woocommerce_before_checkout_billing_form',array(&$this, 'reset_user_address'));
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( &$this, 'process_update_data_tarif' ) );
					//add_action('woocommerce_after_checkout_billing_form',array(&$this, 'epeken_js_query_province_billing_form'));
					$this -> activate();
		}

/**
 * Initialise Gateway Settings Form Fields
 */
	public function init_form_fields() {
		 
		 $string = file_get_contents(EPEKEN_KOTA_KAB);
                 $json = json_decode($string,true);
                 $array_kota = $json['listkotakabupaten'];
                 $array_kota_01 = array();
		 $array_kota_01[0] = "None";
                 foreach($array_kota as $element){
                        array_push($array_kota_01,$element['kotakab']);
                 }

     			$this->form_fields = array(
							'enabled' => array(
                                                        'title'                 => __( 'Enable/Disable', 'woocommerce' ),
                                                        'type'                  => 'checkbox',
                                                        'label'                 => __( 'Enable this shipping method', 'woocommerce' ),
                                                        'default'               => 'yes',
                                                	),
                                                 'panel_enable_kurir' => array(
                                                        'type' => 'panel_enable_kurir',
                                                ),
						'data_kota_asal' => array(
                                                        'title' => __('Data Kota Asal','woocommerce'),
                                                        'type' => 'select',
                                                        'options' => $array_kota_01
                                                ),
                                                'freeship' => array(
                                                        'title' => __('Nominal Belanja Minimum (Rupiah), Dapat gratis ongkir (Biarkan 0 jika ingin free shipping disabled.)','woocommerce'),
                                                        'type'  => 'text',
                                                        'default' => '0',
                                                 ),
						 'enable_cekresi_page' => array(
                                                        'title' => __('Enable Cek Resi JNE appears in main Menu'),
                                                        'type' => 'checkbox',
                                                        'label' => __('Enable/Disable Cek Resi JNE Page'),
                                                        'default' => 'no'
                                                ),
     				);
	} // End init_form_fields()


   // Our hooked in function - $fields is passed via the filter!
	public function admin_options() {
 		?>
 		<h2><?php _e('Epeken-All-Kurir Shipping Settings','woocommerce'); ?></h2>
		 <table class="form-table">
		 <?php $this->generate_settings_html(); ?>
		 </table> <?php
 	}

	public function generate_panel_enable_kurir_html() {
                ob_start();
                 ?>
                <tr>
                <th scope="row" class="titledesc">Pilih Kurir Yang di-enable</th>
                <td>
                        <?php $en_jne = get_option('epeken_enabled_jne'); $en_tiki = get_option('epeken_enabled_tiki'); $en_pos = get_option('epeken_enabled_pos'); $en_rpx = get_option('epeken_enabled_rpx'); $en_esl = get_option('epeken_enabled_esl');?>
                        <p><div style="position: relative; float: left; padding: 5px"><input name="enabled_jne" id = "enabled_jne" type="checkbox" <?php if ($en_jne === "on"){echo "checked";} ?>>JNE</input></div></p>
                        <p><div style="position: relative; float: left; padding: 5px"><input name="enabled_tiki" id="enabled_tiki" type="checkbox" <?php if ($en_tiki === "on"){echo "checked";} ?>>TIKI</input></div></p>
                        <p><div style="position: relative; float: left; padding: 5px"><input name="enabled_pos" id = "enabled_pos" type="checkbox" <?php if ($en_pos === "on"){echo "checked";} ?>>POS Indonesia</input></div></p>
                        <p><div style="position: relative; float: left; padding: 5px"><input name="enabled_rpx" id="enabled_rpx" type="checkbox" <?php if ($en_rpx === "on"){echo "checked";} ?>>RPX</input></div></p>
                        <p><div style="position: relative; float: left; padding: 5px"><input name="enabled_esl" id="enabled_esl" type="checkbox" <?php if ($en_esl === "on"){echo "checked";} ?>>ESL</input></div></p>
                </td>
                </tr>
                 <?php
                return ob_get_clean();
        }


	
	public function get_checkout_post_data($itemdata){
		$postdata = explode('&',$_POST['post_data']);
		$post_data_ret = '';
		foreach ($postdata as $value) {
                        if (strpos($value,$itemdata) !== FALSE) {
                                $post_data_ret = $value;
                                $ar = explode('=',$post_data_ret);
                                $post_data_ret = $ar[1];
                                break;
                        }
                }
		$post_data_ret = str_replace('+',' ',$post_data_ret);
		return $post_data_ret;
	}
		
	public function set_shipping_cost() {
				 global $wpdb;
                        $sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "_transient_%"';
                        $wpdb->query($sql);

 			$wooversion = $this -> epeken_get_woo_version_number();
                        $wooversion = substr($wooversion, 0,3);

                        $post_action = '';
                        $val_post_action = '';
                        if ($wooversion > 2.3) {
                          $post_action = isset($_GET['wc-ajax']) ? $_GET['wc-ajax'] : '';
                          $val_post_action = 'update_order_review';
                        } else {
                          $post_action = isset($_POST['action']) ? $_POST['action'] : '';
                          $val_post_action = 'woocommerce_update_order_review';
                        }


                        //$post_action = isset($_POST['action']) ? $_POST['action'] : ''; //obsolete since wc 2.4
                        //  if($post_action === 'woocommerce_update_order_review')      { //obsolete since wc 2.4
                        if ($post_action === $val_post_action)      {
                                $isshippedifadr = $this -> get_checkout_post_data('ship_to_different_address');
                                        if($isshippedifadr === '1'){
                                         $this -> shipping_kecamatan = $this -> get_checkout_post_data('shipping_address_2');
                                         $this -> shipping_city = $this -> get_checkout_post_data('shipping_city');
                                        }else{
                                         $this -> shipping_city = $this -> get_checkout_post_data('billing_city');
                                         $this -> shipping_kecamatan = $this -> get_checkout_post_data('billing_address_2');
                                        }
                           }else{
                                   if(!empty($_POST['shipping_city']))  {
                                     $this -> shipping_city = sanitize_text_field($_POST['shipping_city']);
                                   } else {
                                     $this -> shipping_city = sanitize_text_field($_POST['billing_city']);
                                   }
                                   if(!empty($_POST['shipping_address_2']))  {
                                     $this -> shipping_kecamatan = sanitize_text_field($_POST['shipping_address_2']);
                                   } else {
                                     $this -> shipping_kecamatan = sanitize_text_field($_POST['billing_address_2']);
                                   }
                           }
			     unset($this -> array_of_tarif);
                          $this -> array_of_tarif = array();
                          $content_tarif = epeken_get_tarif($this -> shipping_city);
                          if($content_tarif === "") {
                                        return;
                                }
                          $json = json_decode($content_tarif);
                          $status = $json -> {'status'} -> {'code'};

			  if(empty($status)) {
                                array_push($this -> array_of_tarif, array('id' => 'Epeken-Courier','label' => 'Terjadi kesalahan. Silakan mencoba kembali atau menghubungi <a href="http://www.epeken.com/contact">team support</a>.', 'cost' => '0'));
                                return;
                         }


 			  if ($status != 200){
                                array_push($this -> array_of_tarif, array('id' => 'Epeken-Courier','label' => 'Error '.$status.':'.$json -> {'status'} -> {'description'}.' Silakan mencoba kembali atau menghubungi <a href="http://www.epeken.com/contact">team support</a>.', 'cost' => '0'));
                                return;
                          }

			  $this -> destination_province = $json -> {'destination_details'} -> {'province'};
			  $this -> map_destination_province();

			 if($isshippedifadr === '1'){
			   add_action('woocommerce_review_order_before_cart_contents',array(&$this,'epeken_triger_shipping_province'));
			 } else {
			   add_action('woocommerce_review_order_before_cart_contents',array(&$this,'epeken_triger_billing_province'));
			 }

			  $json_tarrifs= $json->{'results'};//[0]->{'costs'};
                          $services = array();
                          foreach($json_tarrifs as $element){
                                $kurir = $element -> {'code'};
                                $element = $element -> {'costs'};
                                foreach($element as $element_cost) {
                                 $service = $element_cost -> {'service'};
                                 $rate = $element_cost ->{'cost'}[0]->{'value'};
                                 $service_detail = array('kurir' => $kurir, 'service' => $service , 'rate' => $rate);
                                 array_push($services,$service_detail);
                                }
                          }
			
   		       foreach($services as $services_element) {
                         $id = $services_element['kurir'].'_'.$services_element['service'];
                         $label = strtoupper($services_element['kurir'].' '.$services_element['service']);
				if ($this -> is_shipping_exclude($label))
                                continue;
                         $cost = $services_element['rate'];
                         array_push($this -> array_of_tarif, array('id' => $id,'label' => $label, 'cost' => $cost));
                        }
	}

	 public function is_shipping_exclude ($shipping_label) {
                $ret = false;

 if ($shipping_label === 'JNE SPS' || $shipping_label === 'JNE CTCSPS' || $shipping_label === 'TIKI SDS' || $shipping_label === 'RPX SDP' || $shipping_label === 'JNE CTCBDO' || $shipping_label === 'JNE PELIK')  {
                        $ret = true;
                        return $ret;
                }

                $en_jne = get_option('epeken_enabled_jne'); $en_tiki = get_option('epeken_enabled_tiki'); $en_pos = get_option('epeken_enabled_pos');
                $en_rpx = get_option('epeken_enabled_rpx'); $en_esl = get_option('epeken_enabled_esl');
                if (empty($en_jne) && strpos(substr($shipping_label,0,3),"JNE") !== false) {
                        $ret = true;
                }else if(empty($en_tiki) && strpos(substr($shipping_label,0,3),"TIK") !== false) {
                        $ret = true;
                }else if(empty($en_pos) && strpos(substr($shipping_label,0,3),"POS") !== false)  {
                        $ret = true;
                }else if(empty($en_rpx) && strpos(substr($shipping_label,0,3),"RPX") !== false) {
                        $ret = true;
                }else if(empty($en_esl) && strpos(substr($shipping_label,0,3),"ESL") !== false) {
                        $ret = true;
                }
                return $ret;
        }


	public function map_destination_province(){
		if($this->destination_province === "Nanggroe Aceh Darussalam (NAD)"){
			$this -> destination_province = "Daerah Istimewa Aceh";
		}else if($this->destination_province === "DI Yogyakarta"){
			$this -> destination_province = "Daerah Istimewa Yogyakarta";
		}else if($this->destination_province === "Nusa Tenggara Barat (NTB)"){
                        $this -> destination_province = "Nusa Tenggara Barat";
                }else if($this->destination_province === "Nusa Tenggara Timur (NTT)"){
                        $this -> destination_province = "Nusa Tenggara Timur";
                }
	}	

	 public function epeken_triger_billing_province () {
          ?>      <script type="text/javascript">
                        jQuery(document).ready(function($){
					var pro = '<?php echo $this->destination_province; ?>';
					//alert(pro);
					$('#billing_state').attr('disabled',false);
                                        $('#billing_state option').removeAttr('selected');
					$('#billing_state option').each(function(){if($(this).text() == pro){$(this).attr('selected',true);$('#billing_state').change();}});
					$('#billing_state').attr('disabled',true);
                                });
                    </script>
                <?php
          }

	public function epeken_triger_shipping_province () {
          ?>      <script type="text/javascript">
                        jQuery(document).ready(function($){
					$('#billing_state').attr('disabled',false);
                                        var pro = '<?php echo $this->destination_province; ?>';
                                        $('#shipping_state').attr('disabled',false);
                                        $('#shipping_state option').removeAttr('selected');
                                        $('#shipping_state option').each(function(){if($(this).text() == pro){$(this).attr('selected',true);$('#shipping_state').change();}});
                                        $('#shipping_state').attr('disabled',true);
                                });
                    </script>
                <?php
          }



	public function calculate_shipping( $package ) {	
		 $this -> set_shipping_cost();
                $this -> if_total_got_free_shipping();
                if($this -> is_free_shipping){
                         $rate = array(
                        'id' => $this -> id,
                        'label' => 'Bebas Ongkos Kirim',
                        'cost' => 0
                        );
                        $this->add_rate($rate);
                        return;
                }
                if(sizeof($this -> array_of_tarif) > 0) {
                 foreach($this -> array_of_tarif as $rate) {
                        $this -> add_rate ($rate);
                 }
                }	
	}

	public function if_total_got_free_shipping(){
		global $woocommerce;
		$this -> total_cart = floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_cart_total() ) );
		$this -> total_cart = $this->total_cart/100;
		$this -> min_allow_fs  = floatval($this -> settings['freeship']);
		if ($this -> min_allow_fs == 0){
			$this -> min_allow_fs = false;
			return;
		}
                if ($this->total_cart >= $this->min_allow_fs)
                {
                        $this -> is_free_shipping = true;
                }else{
                        $this -> is_free_shipping = false;
                }
	}

	public function process_update_data_tarif() {
                include_once 'tools/update_tarif.php';
        }

        public function admin_error($message) {
        $class = "error";
        echo"<div class=\"$class\"> <p>$message</p></div>";
        }
        public function epeken_get_woo_version_number() {
        // If get_plugins() isn't available, require it
        if ( ! function_exists( 'get_plugins' ) )
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        // Create the plugins folder and file variables
        $plugin_folder = get_plugins( '/' . 'woocommerce' );
        $plugin_file = 'woocommerce.php';

        // If the plugin version number is set, return it 
        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
                return $plugin_folder[$plugin_file]['Version'];

        } else {
        // Otherwise return null
                return NULL;
        }
        }


	}	// End Class WC_Shipping_Tikijne

?>
