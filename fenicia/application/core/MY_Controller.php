<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct() {
		parent::__construct();
		global $notification_data;
		$this->load->database();
		$this->load->model('mcommon');
		//$this->load->model('admin/muser');
		$this->refresh_user();
		$this->role_id  			= $this->session->userdata('role_id');
		$this->user_id  			= $this->session->userdata('user_data');
		if(!empty($this->user_id)){
			$cond_profile			= array('user_id' => $this->user_id);
			$user_profile_data		= $this->mcommon->getRow('user_profile',$cond_profile);
			//pr($user_profile_data);
			if(!empty($user_profile_data)){
				$this->profile_img	= $user_profile_data['profile_photo'];
			}
			else{
				$this->profile_img	= '';
			}
		}
		$this->notification_data 	= $this->notification();
		//pr($this->notification_data);
	}
	protected function is_logged_in() {
		return $this->session->userdata('admin') ? 1 : 0;
	}
	protected function redirect_guest() {
		if (!$this->session->userdata('admin') || $this->session->userdata('project')=='' || $this->session->userdata('project')!='dealsntings'){
			//echo "<pre>"; print_r($this->session->userdata('project')); die;
			//redirect('admin/index', 'refresh');
			redirect('admin/logout');
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

	protected function refresh_user() {
		if ($this->session->userdata('front_end_user')) {
			$loggedin_user_id=$this->session->userdata('front_end_user')['user_id'];
			$loggedin_user_details=$this->muser->get_details($loggedin_user_id);
			$this->session->set_userdata('front_end_user',$loggedin_user_details);
			return 1;
		}
	}

	protected function redirect_merchant() {
		if (!$this->session->userdata('merchant')) {
			redirect('admin/index/merchant_login', 'refresh');
		}
	}

	protected function redirect_redeem() {
		if (!$this->session->userdata('redeem')) {
			redirect('admin/index/redeem_login', 'refresh');
		}
	}
	protected function getUserPermissionList($menu_id,$role_id) {

		$user_permn_data		= array();
		$user_permission_condn	= array('menu_id' =>$menu_id,'role_id' => $role_id);
		$user_permission_data	= $this->mcommon->getRow('user_permission',$user_permission_condn);
		if(!empty($user_permission_data)){
			$user_permn_data 	= $user_permission_data;
		}
		
		return $user_permn_data;
	}
	public function notification()
	{
		$result 			 = array();
		$notification	 	 = array();		
		$notification_cnt 	 = 0;
		$condition			 = array('status' =>'1','notification_title' => 'Reservation Pending');
		$notification_data	 = $this->mcommon->getNotificationList($condition);
		//pr($notification_data);
		if(!empty($notification_data)){
			$notification_cnt = count($notification_data);			
			$notification['details']	= $notification_data;
			$notification['count']		= $notification_cnt;
		}

		return $notification;
	}
}
