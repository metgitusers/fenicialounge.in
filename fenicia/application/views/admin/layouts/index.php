<?php
$this->load->view('admin/layouts/header');
$url_link 					= current_url();
$tmp_url_link				= explode('/', $url_link);
//pr($tmp_url_link);
if(count($tmp_url_link) == 7){
	$last_string_url_link		= 'admin/'.$tmp_url_link[count($tmp_url_link)-2].'/'.end($tmp_url_link);
}
if(count($tmp_url_link) > 7){
	$last_string_url_link		= 'admin/'.$tmp_url_link[count($tmp_url_link)-3].'/'.$tmp_url_link[count($tmp_url_link)-2];
}
if(count($tmp_url_link) == 6){
	$last_string_url_link		= 'admin/'.end($tmp_url_link);
}
//echo $last_string_url_link;exit;
$permition					= checPkermission($last_string_url_link,$this->session->userdata('role_id'));
if((empty($permition) || $permition == '0') && $this->session->userdata('role_id') !='1'){
	//echo '<div class="main-content"><div class="content-wrapper"><div class="container-fluid"><section id="basic-form-layouts"><div class="row"><div class="col-md-12"><div class="card"><div class="card-header"><div class="page-title-wrap"><h3>You do not have access to this page.</h3></div></div></div></div></div></section></div></div></div>';exit;
	$content		= 'admin/no_permission';
	$this->load->view($content);
}
else{
	$this->load->view($content);
}

$this->load->view('admin/layouts/footer');