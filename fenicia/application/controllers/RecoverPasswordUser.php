<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RecoverPasswordUser extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Mcommon');
	}
	public function index($key){	
		$this->forgotpassword(base64_decode($key));
	}
	public function forgotpassword($key) {
		$data['key'] = $key;
		$data['title'] = 'Club Fenicia';
		$this->load->view('forgotpassword_user', $data);
	}

	public function recoverAccount() {
		$new_password = $this->input->post('newpassword1');
		$recovery_key = $this->input->post('recovery_key');
		$result=$this->mcommon->check_recovery_key($recovery_key);
		if($result){
			$condition=array('member_id'=>$result['member_id']);
			$data=array('password'=>sha1($new_password),'original_password'=>$new_password,'recovery_key'=>'');
			$update_result = $this->mcommon->update('master_member',$condition,$data);
			if($update_result){
				$response = array("status" => true, "message" => "Password Updated Successfully");
			}
			else{
				$response = array("status" => true, "message" => "Some thing went wrong. Please try again");
			}
		}
		else{
			$response = array("status" => true, "message" => "Reset link Expired. Please try again.");
		}
		echo json_encode($response);
	}

	// public function error_404() {
	// 	$data['title'] = 'DAD';
	// 	$this->load->view('error_404');
	// }
	
}