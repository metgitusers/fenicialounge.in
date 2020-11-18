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
		$condition=array('status'=>'1','is_delete'=> '0');
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
		ini_set('display_errors', 1);
		// $cafe_movie_arr=$this->input->post('cafe_movie');
		// echo "<pre>"; print_r($cafe_movie_arr);die;
	    //$this->form_validation->set_rules('message_title','Title','trim|required');
	    //$this->form_validation->set_rules('sub_text','Sub text','trim|required');
	   // $this->form_validation->set_rules('category','Category','trim|required');
	    $this->form_validation->set_rules('offer_text','Offer','trim|required');  
		//$this->form_validation->set_rules('file', '', 'callback_file_check');
		
		if ($this->form_validation->run() == FALSE) {
		//echo "val error";die;
		$this->session->set_flashdata('Movie_error_message','Something went wrong.Please try again');
		//$this->add();
		}
	        else{
				////send push to user
	        	$user_arr=$this->input->post('user_id');	
		 		if(!empty($user_arr))
		 		{
		 			$users=json_encode($user_arr);

		 			/********push notification  ************************/
					// $title="Notification Fenicia";
					// $message   = $this->input->post('offer_text');	
					// $message_data = array('title' => $title,'message' => $message);
					$title = $this->input->post('message_title')?$this->input->post('message_title'):"";
					//$sub_text =  $this->input->post('sub_text');
					$category =  "";
					$message = $this->input->post('offer_text');
					$msg_img = "";
					$image_title = "";
					
					/*----------------*/ 
					if($_FILES['file']['name']){
						$filename = $_FILES['file']['name'];
						$allowed =  array('jpg', 'jpeg', 'JPG', 'JPEG');
						$ext = pathinfo($filename, PATHINFO_EXTENSION);
						//if (in_array($ext, $allowed)) {
							$image_file = time().mt_rand(111, 999)."." . $ext;
							$imgPath = getcwd()."/public/upload_image/".$image_file;
							if(move_uploaded_file($_FILES['file']['tmp_name'], $imgPath)){
								$image_title = $image_file; //$file['result'];
								$msg_img = base_url('public/upload_image/').$image_file; //$file['result'];
							}
						//}
					}

					$push_array = array();
                                
				 	foreach ($user_arr as $member_id) {
				 		$user_row=array();
				 		$user_fcm_token_data  = $this->mcommon->getRow('device_token',array('member_id' => $member_id));
                        //pr($user_fcm_token_data);
                        if(!empty($user_fcm_token_data)){
                          $member_datas  = $this->mcommon->getRow('master_member',array('member_id' => $member_id));
                            if($member_datas['notification_allow_type'] == '1'){								
								if($user_fcm_token_data['device_type'] == 1){
									$push_array = array("to" => 
														//"e50FefdZf0oHjpwyqBeNvr:APA91bHkBY-P_gawF2HgC5_M56nOj689NPa9EBUv2-1wpStu8zrrQwnVaoKDQRX_Q9YMSuJGszTLIwqQEhsOo0jCxE3qxDfTF8NCeDcf5w9-odjxGwFu9uR86zXqPKRTAd6k9P3ZeZ_W",
														$user_fcm_token_data['fcm_token'],
														"mutable_content"=>true,
														"notification" => array(
															"body" => $message,
															"title"=> $title,
															"click_action"=>$category															
														),
														"data"=>array (
																"category"=> $category,
																"urlImageString"=>$msg_img
															)
													);
										
                                  	//$this->pushnotification->send_ios_notification($user_fcm_token_data['fcm_token'], $message_data);
                                  	$this->pushnotification->send_ios_notification($push_array);
                                }
                                else{
									$push_array = array("to" => 
														//"eH9L8bCyA0E:APA91bEQwOLHetzrgMTHmuINb76W5rJGUdUxQtSizbimMbofDsR9XwCNCuCeVvBaioyxyu0nblf3u4N-5uuf4OoQcogRMg23Gz46bVv14MwK2zU8E5pqSIZ_k1DHMIM3Gaji-A6GRO_y",
														$user_fcm_token_data['fcm_token'],
														"collapse_key"=> "type_a",
														"notification" => array(
															"body" => $message,
															"title"=> $title,
															"image"=>$msg_img
														),
														"data"=>array (
																"subText"=> $category
															)
													);				
                                  	//$this->pushnotification->send_android_notification($user_fcm_token_data['fcm_token'], $message_data);
                                  	$this->pushnotification->send_android_notification($push_array);
                                }
                            }
                        }
				 	}
			 	}
			
			$idata = array(
		 		'offer'   => $this->input->post('offer_text'),
				'image'		=> $image_title,
				'category'	=> $category,
				'title'		=> $title,
				'user_id' => $users,
				//'send_log'=> json_encode($push_array)
		       // 'created_on' => date('Y-m-d H:i:s'),
            );

		 	$this->mcommon->insert('push_notification', $idata);
			//echo $this->db->last_query();
		 	$this->session->set_flashdata('success_msg','Notification send successfully.');
		 	redirect('admin/notification/offer');
		 	
	   }
	}
	
	  /*
     * file value and type check during validation
     */
    public function file_check($str){
		$allowed_mime_type_arr = array('image/gif','image/jpeg','image/pjpeg','image/png');
        $mime = $_FILES['file']['type'];
        if(isset($_FILES['file']['name']) && $_FILES['file']['name']!=""){
            if(in_array($mime, $allowed_mime_type_arr)){
                return true;
            }else{
                $this->form_validation->set_message('file_check', 'Please select only pdf/gif/jpg/png file.');
                return false;
            }
        }else{
            $this->form_validation->set_message('file_check', 'Please choose a file to upload.');
            return false;
        }
    }

  private function _load_view($data) {
		   // FUNCTION WRIITTEN IN COMMON HELPER
		$this->load->view('admin/layouts/index', $data);
		
	}
 
}