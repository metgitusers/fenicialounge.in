<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends MY_Controller {
	public function __construct() {
		parent::__construct();
		//$this->redirect_guest();
		$this->admin=$this->session->userdata('admin');		
		$this->load->model('admin/mlog');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	// Default load function for header and footer inculded
	private function _load_view($data) {
		$this->load->view('admin/layouts/index',$data);
	}
	/*
	author: sreela
	purpose: Log Listing
	date: 03-03-2020
	*/
	public function index() { 
		$result = array();
		$result['log_lists'] 	= $this->mlog->get_log_list('1');
		//pr($result);	
		//$result['log_lists'] 	= "sdjflksjl";
		$result['content']		= 'admin/log/log_list';
		$this->_load_view($result);
				
	}
	public function logList(){
		$responce_arr			= array();
		$result['log_lists'] 	= $this->mlog->get_log_list('1');
		$responce_arr['html'] 	= $this->load->view('admin/log/ajax_log_list',$result,true);
        echo json_encode($responce_arr);exit;
	}
	public function getLogData(){
		$responce_arr 			= array();
		$id 					= $this->input->post('id');
		$data['db_title']		= $this->input->post('title');
		//$db_columns 			= $this->input->post('columns');
		$type		= $this->input->post('title');
		$data['log_lists'] 		= $this->mlog->getLogListById($id,$type);
		//pr($data);
		$responce_arr['html'] 	= $this->load->view('admin/log/ajax_log_details',$data,true); 
		echo json_encode($responce_arr);exit;
	}
}