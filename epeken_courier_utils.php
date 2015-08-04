<?php
  if ( ! defined( 'ABSPATH' ) ) exit;

  include_once('includes/query.php');
  function epeken_get_list_kecamatan() {
	$kotakab = isset($_GET['kota']) ? $_GET['kota'] : '';
	$nextnonce = isset($_GET['nextNonce']) ? $_GET['nextNonce'] : '';
	$kotakab = sanitize_text_field($kotakab);
	$nextnonce = sanitize_text_field($nextnonce);
	
	if(!wp_verify_nonce($nextnonce,'myajax-next-nonce')){
			die('Invalid Invocation');
		}
	$li_kecamatan = array();
	if(!empty($kotakab))
	{
		$li_kecamatan = epeken_get_list_of_kecamatan($kotakab);		
	}

	foreach($li_kecamatan as $value){
		echo trim($value).';';
	} 
    }

   add_action('wp_ajax_get_list_kecamatan','epeken_get_list_kecamatan');
   add_action('wp_ajax_nopriv_get_list_kecamatan','epeken_get_list_kecamatan');
?>
