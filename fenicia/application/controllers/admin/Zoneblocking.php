<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Zoneblocking extends MY_Controller {
	public function __construct() {
		parent::__construct();
		//$this->redirect_guest();
		$this->admin=$this->session->userdata('admin');
		$this->load->library('imageupload');	
		$this->load->model('admin/mzone');
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

	public function index() { 
		$result 				= array();		
		$result['content'] 		= 'admin/blocking/blocking_list_search';
		$result['zone_club_list'] 	= $this->mzone->get_zone_club_list('1');
		
		$this->_load_view($result);	
				
	}
	public function SearchBlockingList() { 
		$data = array();
		$response_arr = array();
		$zone_id_list = array();
		$cond   = '1';
		$from_time 	= '';
		$to_time    = '';
		$from_dt 	= DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("blocking_from_date"))));
		$to_dt 		= DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("blocking_to_date"))));
		if(!empty($this->input->post("blocking_from_time"))){
			$from_time 	= DATE('H:i:s',strtotime($this->input->post("blocking_from_time")));
		}
		if(!empty($this->input->post("blocking_to_time"))){
			$to_time    = DATE('H:i:s',strtotime($this->input->post("blocking_to_time")));
		}		
		$club_zone 	= $this->input->post('club_zone');
		$zone_list 	= $this->mcommon->getDetails('master_zone',array('status'=>'1','club_zone_name'=>$club_zone));
		if($from_dt !='' && $to_dt !=''){
	      $cond .=  " and zone_blocking.blocking_date between '".$from_dt."' and '".$to_dt."'";      
	    }
	    if($from_time !='' && $to_time !=''){
	      $cond .= " and zone_blocking.blocking_time between '".$from_time."' and '".$to_time."'";
	    }
	    if(!empty($zone_list)){
	    	foreach($zone_list as $val){
	    		$zone_id_list[] = $val['zone_id'];
	    	}
	    	if(count($zone_id_list) > 1){
	    		$zone_id	= implode(',', $zone_id_list);
	    		$cond .= " and zone_blocking.zone_id in(".$zone_id.")";	    		
	    	}
	    	else{
	    		$cond .= " and zone_blocking.zone_id ='".$zone_id_list[0]."'";
	    	}
	    }
	         
	    $blocking_list           	= $this->mcommon->get_blocking_list($cond);
	    $data['blocking_list']		= $blocking_list;
	    
	    echo $this->load->view('admin/blocking/ajax_blocking_list_search',$data,true);exit;
	}
	public function zoneBlocking(){
		$result 				= array();		
		$result['content'] 		= 'admin/blocking/blocking';
		$result['zone_club_list'] 	= $this->mzone->get_zone_club_list('1');
		
		$this->_load_view($result);		
	}
	public function  blockZone(){
		//pr($_POST);
		$data 				= array();
		$response_arr 		= array();
		$from_dt 			= DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("blocking_from_date"))));
		//$to_dt 		= DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("blocking_to_date"))));
		//$from_time 	= DATE('H:i:s',strtotime($this->input->post("blocking_from_time")));
		$start_time_range   = date('H:i:s',strtotime("-90 minutes", strtotime($this->input->post("blocking_from_time"))));
	   	$end_time_range     = date('H:i:s',strtotime("+90 minutes", strtotime($this->input->post("blocking_from_time"))));
		//$to_time    = strtotime($this->input->post("blocking_to_time")));
		$club_zone 	= $this->input->post('club_zone');
		$zone_list 	= $this->mcommon->getDetails('master_zone',array('status'=>'1','club_zone_name'=>$club_zone));
		//pr($zone_list);
		if(!empty($zone_list)){
			foreach($zone_list as $val){
				$reserv_cond 	= "reservation_date ='".$from_dt."' and zone_id = '".$val['zone_id']."' and status ='2' and reservation_time between '".$start_time_range."' and '".$end_time_range."'";
				$block_cond 	= "blocking_date ='".$from_dt."' and zone_id = '".$val['zone_id']."' and status ='Blocked' and blocking_time between '".$start_time_range."' and '".$end_time_range."'";
				$reservation_data[$val['zone_name']] = $this->mcommon->getRow('reservation',$reserv_cond);
				$reservation_data[$val['zone_name']] = $this->mcommon->getRow('zone_blocking',$block_cond);
			}
		}
		//pr($reservation_data);		
		$data['reservation_data'] = $reservation_data;
		echo $this->load->view('admin/blocking/ajax_zone_blocking',$data,true);exit;
       
	}
	public function doBlockZone(){
		$zone_id 	= '';
		$from_dt 	= DATE('Y-m-d',strtotime(str_replace('/','-',$this->input->post("blocking_from_date"))));
		$from_time 	= DATE('H:i:s',strtotime($this->input->post("blocking_from_time")));
		$zone_name 	= $this->input->post('zone_name');
		$zone_list 	= $this->mcommon->getRow('master_zone',array('zone_name'=>$zone_name));
		if(!empty($zone_list)){
			$zone_id 	= $zone_list['zone_id'];
		}
		$data = array('blocking_date' 	=> $from_dt,
					  'blocking_time' 	=> $from_time,
					  'zone_id'		  	=> $zone_id,
					  'status'		  	=> 'Blocked',
					  'created_on'		=> date('Y-m-d')
					);	
		$insert_id 	= $this->mcommon->insert('zone_blocking',$data);
		if($insert_id){
			echo '1';exit;
		}
		else{
			echo '0';exit;
		}

	}
}