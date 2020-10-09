<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}
	protected function is_logged_in() {
		return $this->session->userdata('admin') ? 1 : 0;
	}
	protected function redirect_guest() {
		if (!$this->session->userdata('admin') && $this->session->userdata('project')=='dad') {
			redirect('admin/index', 'refresh');
		}
	}
	protected function is_logged_in_user() {
		return $this->session->userdata('front_end_user') ? 1 : 0;
	}
	protected function redirect_guest_user() {
		if (!$this->session->userdata('front_end_user')) {
			redirect('index', 'refresh');
		}
	}
}
