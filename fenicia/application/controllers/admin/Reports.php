<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {
	public function __construct() {
		parent::__construct();
		//$this->redirect_guest();
		$this->admin=$this->session->userdata('admin');		
		$this->load->model('admin/mreport');
		$this->load->model('admin/mzone');
        $this->load->model('admin/mreservation');
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
	public function membershipPackagesPurchased() { 
		$result = array();
		
		$result['content']='admin/reports/membership_packages_purchased/membership_packages_purchased_report_list';
		$this->_load_view($result);				
	}
	public function membershipPackagesPurchasedGenerate() {
        $data           = array();
        $responce_arr   = array();
        //echo $this->input->post("driver_id");exit;
        $from_date	= '';
        $to_date	= '';
        if($this->input->post("from_date") && $this->input->post("to_date")){
            $from_date    = date('Y-m-d',strtotime(str_replace('/','-',$this->input->post("from_date"))));
            $to_date      = date('Y-m-d',strtotime(str_replace('/','-',$this->input->post("to_date"))));
        }
        
        $data       = array(); 
        $cond 		='';
       
        if($from_date && $to_date){
            $cond =" where pmm.buy_on between '".$from_date."' and '".$to_date."'";
        }
        $packages_purchased_list 	= $this->mreport->getMembershipDetails($cond);
        //pr($packages_purchased_list);
        if(!empty($packages_purchased_list)){
            
            $data['packages_purchased_list']     = $packages_purchased_list;
        }
        else{
            $data['packages_purchased_list']     = '';
        }
        $responce_arr['html'] = $this->load->view('admin/reports/membership_packages_purchased/ajax_membership_packages_purchased_report',$data,true);
        echo json_encode($responce_arr);exit;
    }
    public function reservationReport() { 
        $result = array();        
		$result['zone_list'] 	= $this->mzone->get_zone_list('1');

        $result['content']		= 'admin/reports/reservation_report/reservation_report';
        $this->_load_view($result);             
    }
    public function reservationReportGenerate() {
        $responce_arr   = array(); 
        $guest_cnt 		= 0;  
        $reservation_cnt= 0; 
        $from_data      = $this->input->post("from_date");
        $to_data        = $this->input->post("to_date");
        $zone_id        = $this->input->post("zone_id");
        $status_id      = $this->input->post("status_id");
        $result_data    = reservationfilterSearch($from_data,$to_data,$zone_id,$status_id);
        //pr($result_data['reservation_list']);
        if(!empty($result_data) && !empty($result_data['reservation_list'])){
        	foreach($result_data['reservation_list'] as $list){
        		$guest_cnt 		= $guest_cnt+$list['no_of_guests'];
        	}
        	$reservation_cnt = count($result_data['reservation_list']);
        }
        $responce_arr['reservation_cnt'] 	= $reservation_cnt;
        $responce_arr['guest_cnt']  		= $guest_cnt;
        $responce_arr['html'] = $this->load->view('admin/reports/reservation_report/ajax_reservation_report',$result_data,true);
        echo json_encode($responce_arr);exit;
    }
}