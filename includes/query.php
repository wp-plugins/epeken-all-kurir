<?php
  if ( ! defined( 'ABSPATH' ) ) exit;
  function epeken_get_list_of_kota_kabupaten ()
	{
		$kotakabreturn = array();
		$string = file_get_contents(EPEKEN_KOTA_KAB);
		$json = json_decode($string,true);
		$array_kota = $json['listkotakabupaten'];
		$kotakabreturn [''] = 'Pilih Kota/Kabupaten';
		foreach($array_kota as $element){
			$kotakabreturn[$element['kotakab']] = $element['kotakab'];	
		}
		return $kotakabreturn;
	}
  
  function epeken_get_list_of_kecamatan ($kotakab)
	{
		$kotakab = sanitize_text_field(trim($kotakab));
		$kecamatanreturn = array();
		 if ($kotakab === 'init'){
                  $kecamatanreturn [''] = 'Please Select Kecamatan';
                  return $kecamatanreturn;
                }

		$string = file_get_contents(EPEKEN_KOTA_KEC);
		$json = json_decode($string, true);
		$array_kecamatan = $json['listkecamatan'];
		$kecamatanreturn[''] = 'Pilih Kecamatan';
		foreach($array_kecamatan as $element){
			if ($element["kota_kabupaten"] === $kotakab) {
				$kecamatanreturn [$element["kecamatan"]] = $element["kecamatan"];
			}	
		}
		return $kecamatanreturn;
	}

	function writelog($logstr){
                        $logdir = plugin_dir_path( __FILE__ )."log/";
                        $sesid = session_id();
                        $logfile = fopen ($logdir.$sesid.".log","a");
                        $now = date("Y-m-d H:i:s");
                        fwrite($logfile,$now.":".$logstr."\n");
                        fclose($logfile);
                }



  function epeken_get_tarif($kotakab) {		
		$license_key = get_option('epeken_wcjne_license_key');
		$options = get_option('woocommerce_epeken_courier_settings');
                $origin_code = $options['data_kota_asal'];
		$destination_code = "";
		$string = file_get_contents(EPEKEN_KOTA_KAB);
                $json = json_decode($string,true);
		$array_kota = $json['listkotakabupaten'];
                foreach($array_kota as $element){
			if($element['kotakab'] === $kotakab){
				$destination_code = $element["code"];
				break;
			}
                }
		$content = "";	
		if ($destination_code !=="") {	
		  	$url = EPEKEN_API_DIR_URL.$license_key."/".$origin_code."/".$destination_code;
			$ch = curl_init();
	 		curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  		
			$content = curl_exec($ch);
//			writelog($url."\n".$content);
  	 		curl_close($ch);
		}
		return $content;
	}
?>
