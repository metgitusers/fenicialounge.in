<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct() {
		parent::__construct();
		//$this->redirect_guest();
		$this->load->model('admin/mdashboard');
		$this->load->model('admin/mreservation');
		$this->load->model('admin/mmember');
		$this->load->model('admin/mzone');
		$this->load->model('admin/Mmembership');
		//pr($this->session->userdata('role_id'));		
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
		else{
			
		}
	}
	public function index() { 
		$data									= array();
		$reservation_request_cnt				= 0;
		$packages_purchased_active_cnt			= 0;
		$member_active_cnt						= 0;
		$data['zone_list'] 						= $this->mzone->get_zone_list('1');
		$condition								= "1";
		$reservation_list  						= $this->mreservation->get_reservation_list($condition);
		if(!empty($reservation_list)){
			$reservation_request_cnt			= count($reservation_list);
		}
		$data['reservation_request_cnt'] 		= $reservation_request_cnt;
		$packages_purchased_list 				= $this->Mmembership->getMembershipDetails(1);
		if(!empty($packages_purchased_list)){
			$packages_purchased_active_cnt		= count($packages_purchased_list);
		}
		$data['packages_purchased_active_cnt'] 	= $packages_purchased_active_cnt;
		$member_active_list						= $this->mmember->get_member_list('1');
		if(!empty($member_active_list)){
			$member_active_cnt					= count($member_active_list);
		}
		$data['member_active_cnt'] 				= $member_active_cnt;

		$data['content'] 						= 'admin/dashboard';			
		$data['title']				 			= 'Dashboard';
		$this->_load_dashboard_view($data);
	}
	/********************* Sreela (14/11/2019) *********************/
	public function reservationRequestList() {
        $responce_arr   = array();
        $data  		 	= array();
        $resv_time      = '';  
        $from_data      = $this->input->post("from_date");
        $to_data        = $this->input->post("to_date");
        $zone_id        = $this->input->post("zone_id");
        if(!empty($this->input->post("reservation_time"))){
        	$resv_time      = DATE('H:i:s',strtotime($this->input->post("reservation_time")));
        }        
        /*echo  $from_data."<br>";
        echo  $to_data."<br>";        
        echo  $zone_id."<br>";
        echo  $resv_time; exit;*/
        if($from_data =='' && $to_data =='' && $zone_id =='' && $resv_time ==''){
        	$condition					= " reservation.status = '1' and reservation.reservation_date = CURDATE()";        
			$data['reservation_list']  	= $this->mreservation->get_reservation_list($condition);
        }
        else{
        	$data    					= reservationFilterSearch($from_data,$to_data,$zone_id,'',$resv_time);
        }        
        $responce_arr['html'] 			= $this->load->view('admin/reports/reservation_report/ajax_reservation_report',$data,true);
        //PR($responce_arr);
        echo json_encode($responce_arr);exit;
    }
    public function membershipPurchasedList() {
        $responce_arr   = array();
        $data  		 	= array();  
        $data['packages_purchased_list'] 	= $this->Mmembership->getMembershipDetails(1);        
        $responce_arr['html'] 				= $this->load->view('admin/reports/membership_packages_purchased/ajax_membership_packages_purchased_report',$data,true);
        //PR($responce_arr);
        echo json_encode($responce_arr);exit;
    }
	private function _load_dashboard_view($data) {
		
		$this->load->view('admin/layouts/index', $data);		
	}
}