<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Logout extends MY_Controller {



	public function __construct() {

		parent::__construct();

		$this->load->model('admin/muser');

	}

    /*
		author: soma
		purpose: logout
		date: 17/9/2019
	*/

	public function index() {

		$this->admin=$this->session->userdata('admin');		

		//$udata['login_status'] = 0;		

		//$condition = array('user_id'=>$this->admin['user_id']);

		//$this->madmin->update($condition,$udata);

		$this->session->sess_destroy();

		redirect('admin/index','refresh');

	}




}