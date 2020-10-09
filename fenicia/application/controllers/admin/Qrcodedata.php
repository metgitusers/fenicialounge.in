<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
		AUTHOR NAME: Soma Nandi Dutta
		DATE: 13/7/20
	    PURPOSE: Commandcard listing and details  
*/
class Qrcodedata extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		
		$this->load->model('admin/mqrcode');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	
	public function index() { 
		
		$result 						= array();
		$result['content'] 				= 'admin/qrcode/list';
		$result['list'] 	= $this->mqrcode->get_data_list();
		$this->load->view('admin/layouts/index', $result);	
	}

	// public function details($commet_id) {

	//     $result 						= array();
	// 	$condition                      = array('comment_card.commet_id'=>$commet_id);
	// 	$result['row'] 	= $this->mcommentcard->get_details('comment_card',$condition);
	// 	//echo "<pre>";
	// 	//print_r($result['command_card_list'] );die;
	// 	$result['content'] 				= 'admin/commentcard/detail';
	// 	$this->_load_view_commentcard($result);		
	// }
	
	
	

}