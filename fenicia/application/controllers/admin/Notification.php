<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->model('mcommon');
		
		$this->load->library('imageupload');
		$this->load->library('PushNotification');
		if($this->session->userdata('role_id') == '')
		{
			redirect('admin');
			die();
		}
	}
	public function index()
{
		
		$data['list'] =$this->mcommon->getDetails('notification');
		
		$data['title']='Notification List';
		$data['content']='admin/notification/list';
		$this->_load_view($data);
		//$this->load->view('admin/layouts/index', $data);
	}
	public function offer()
	{
		$condition=array('status'=>1,'is_delete'=> '0');
		$data['user_list'] =$this->mcommon->getDetails('master_member',$condition);
		//echo $this->db->last_query();
		//print_r($data['user_list']); die;
		
		$data['title']='Offer';
		$data['content']='admin/notification/add';
		$this->_load_view($data);
		//$this->load->view('admin/layouts/index', $data);
	}
	
		
	public function add_content()
	{  
		// $cafe_movie_arr=$this->input->post('cafe_movie');
		// echo "<pre>"; print_r($cafe_movie_arr);die;
	    $this->form_validation->set_rules('offer_text','Offer','trim|required');
	    
	    
	
		if ($this->form_validation->run() == FALSE) {
		//echo "val error";die;
		$this->session->set_flashdata('Movie_error_message','Something went wrong.Please try again');
		$this->add();
		} 
		
	        else{

			////send push to user
	        	$user_arr=$this->input->post('user_id');	
		 		if(!empty($user_arr))
		 		{
		 			$users=json_encode($user_arr);

		 			/********push notification  ************************/
                        $title="Notification Fenicia";
                        $message   = $this->input->post('offer_text');	
                        $message_data = array('title' => $title,'message' => $message);
                        
                                
				 	foreach ($user_arr as $member_id) {
				 		$user_row=array();
				 		$user_fcm_token_data  = $this->mcommon->getRow('device_token',array('member_id' => $member_id));
                        //pr($user_fcm_token_data);
                        if(!empty($user_fcm_token_data)){
                          $member_datas  = $this->mcommon->getRow('master_member',array('member_id' => $member_id));
                            if($member_datas['notification_allow_type'] == '1'){
                                if($user_fcm_token_data['device_type'] == 1){
                                  $this->pushnotification->send_ios_notification($user_fcm_token_data['fcm_token'], $message_data);
                                }
                                else{
                                  $this->pushnotification->send_android_notification($user_fcm_token_data['fcm_token'], $message_data);
                                }
                            }

                          }
				 	}
			 	}

          
			$idata = array(
		 		'offer'   => $this->input->post('offer_text'),
		       
		        'user_id' => $users,
		        
		       // 'created_on' => date('Y-m-d H:i:s'),
            );

		 	$this->mcommon->insert('push_notification', $idata);
		 	
		 	$this->session->set_flashdata('success_msg','Notification send successfully.');
		 	redirect('admin/notification/offer');
		 	
	   }
    }

  private function _load_view($data) {
		   // FUNCTION WRIITTEN IN COMMON HELPER
		$this->load->view('admin/layouts/index', $data);
		
	}
 
}