<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PrivacyPolicy extends MY_Controller {

	public function __construct() {
		parent::__construct();
		
	}
	public function index(){	
		$this->load->view('privacy_policy.html');
	}	
	
}