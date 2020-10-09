<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
		AUTHOR NAME: Soma Nandi Dutta
		DATE: 13/7/20
	    PURPOSE: Commandcard listing and details  
*/
class Commentcard extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('imageupload');
		$this->load->model('admin/mcommentcard');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	
	public function index() { 
		
		$result 						= array();
		$result['content'] 				= 'admin/commentcard/list';
		$result['command_card_list'] 	= $this->mcommentcard->get_comandcard_list();
		$this->_load_view_commentcard($result);		
	}

	public function details($commet_id) {

	    $result 						= array();
		$condition                      = array('comment_card.commet_id'=>$commet_id);
		$result['row'] 	= $this->mcommentcard->get_details('comment_card',$condition);
		//echo "<pre>";
		//print_r($result['command_card_list'] );die;
		$result['content'] 				= 'admin/commentcard/detail';
		$this->_load_view_commentcard($result);		
	}
	
	private function _load_view_commentcard($data) {
		   // FUNCTION WRIITTEN IN COMMON HELPER
		$this->load->view('admin/layouts/index', $data);
		
	}
	

}