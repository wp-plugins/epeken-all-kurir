<?php

 $enable_cekresi = $this -> settings['enable_cekresi_page'];
 if($enable_cekresi === 'yes') {
       $this->create_cek_resi_page();
       $this->add_cek_resi_page_to_prim_menu();
 }else{
       $this -> delete_cek_resi();
 }
?>

