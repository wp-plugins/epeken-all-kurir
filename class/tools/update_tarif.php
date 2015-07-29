<?php

  update_option('epeken_enabled_jne', $_POST['enabled_jne']);
 update_option('epeken_enabled_tiki', $_POST['enabled_tiki']);
 update_option('epeken_enabled_pos', $_POST['enabled_pos']);
 update_option('epeken_enabled_rpx', $_POST['enabled_rpx']);
 update_option('epeken_enabled_esl', $_POST['enabled_esl']);

 $enable_cekresi = $this -> settings['enable_cekresi_page'];
 if($enable_cekresi === 'yes') {
       $this->create_cek_resi_page();
       $this->add_cek_resi_page_to_prim_menu();
 }else{
       $this -> delete_cek_resi();
 }
?>

