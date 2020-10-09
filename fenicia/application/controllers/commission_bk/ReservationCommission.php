<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReservationCommission extends MY_Controller {

	public function __construct() {
		parent::__construct();
		
		/*$this->load->library('PushNotification');
		$this->load->model('admin/mreservation');*/
		$this->load->model('admin/mzone');	
		$this->load->model('commission/Mreservationcomission');
		if($this->session->userdata('user_data') == '')
		{
			redirect('commission/Login');
			die();
		}	
	}		
	
	private function _load_view($data) {
		   // FUNCTION WRIITTEN IN COMMON HELPER
		$this->load->view('admin/layouts/index', $data);
		
	}
	public function index(){
		//echo $this->session->userdata('role_id');exit;
		$result 		= array();
		$result['content'] 				= 'admin/reservation/list';
		$result['zone_list'] 			= $this->mzone->get_zone_list('1');
		$this->load->view('commission/layouts/reservation_commission_header');			
		$this->load->view('commission/reservation_commission/reservation_commission_list',$result);
		$this->load->view('commission/layouts/footer');
		
	}
	public function filterSearchReservationCommission()
	{
		$data   			= array();
		$final_resv_commission_list   = array();		
        $cond   			= '1';
        $commission_charge 	= 100;
        $total_commission	= 0;   
        $from_dt      = $this->input->post("from_date");
        $to_dt        = $this->input->post("to_date");
        $zone_id      = $this->input->post("zone_id");
        if($from_dt !='' && $to_dt !=''){
          $from_date  = date('Y-m-d',strtotime(str_replace('/','-',$from_dt)));
          $to_date    = date('Y-m-d',strtotime(str_replace('/','-',$to_dt)));
          $cond .=  " and rev.reservation_date between '".$from_date."' and '".$to_date."'";      
        }
        if($zone_id !=''){
      		$cond .= " and rev.zone_id ='".$zone_id."'";
    	}
        $resv_commission_list    = $this->Mreservationcomission->reservationCommissionFilterSearch($cond,$zone_id);
        if(!empty($resv_commission_list)){
        	foreach($resv_commission_list as $val){
        		foreach($val as $val_list){
        			$resv_commisn_list['reservation_date'] 	= $val_list['reservation_date'];
        			$resv_commisn_list['no_of_reservation'] = $val_list['no_of_reservation'];
        			$resv_commisn_list['zone_id'] 			= $val_list['zone_id'];
        			$resv_commisn_list['zone_name'] 		= $val_list['zone_name'];

        			$final_resv_commission_list[] = $resv_commisn_list;
        		}
        	}
        	$data['reservation_commission_list']  = $final_resv_commission_list;
        }
        //pr($data['reservation_commission_list']);
        if(!empty($data['reservation_commission_list'])){
        	foreach($data['reservation_commission_list'] as $list){
        		$total_commission	= $total_commission + ($list['no_of_reservation']*$commission_charge);
        	}
        } 
        $data['total_reservation_commission']    =  $total_commission;
        $responce_arr['html'] = $this->load->view('commission/reservation_commission/ajax_reservation_commission_list',$data,true);
        echo json_encode($responce_arr);exit;
	}
	public function viewReservationDetails($reservation_date = null,$zone_id = null)
	{
		//echo $reservation_date;exit;
		$data   	= array();		
        $cond   	= '1';
		if(!empty($reservation_date)){
			$cond .=  " and reservation.reservation_date = '".$reservation_date."' and reservation.zone_id = '".$zone_id."'";
			$reservation_details		= $this->Mreservationcomission->get_reservation_list($cond);			
			$data['reservation_data']	= $reservation_details;
			$this->load->view('commission/layouts/reservation_commission_header');			
			$this->load->view('commission/reservation_commission/reservation_details_list',$data);
			$this->load->view('commission/layouts/footer');
		}
		else{
			redirect('commission/reservation_commission');
		}
	}	
}