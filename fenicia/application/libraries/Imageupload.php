<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Imageupload{
	public $ci;
	function __construct(){
		$this->ci=& get_instance();
	}
	public function image_upload($image_path){ 
		$img='imgInp';			
		$config['upload_path'] = '.'.$image_path;
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		//$config['min_width']  = '200';
		//$config['min_height']  = '200';
		//$config['max_size']	= '100';
		//$config['max_width']  = '1024';
		//$config['max_height']  = '768';
		$config['encrypt_name']  = true;
		$this->ci->load->library('upload', $config);
		if ( ! $this->ci->upload->do_upload($img)){
			$message = array('result' => $this->ci->upload->display_errors(),'status'=>0);
		}else{ 
			$data = array('upload_data' => $this->ci->upload->data());
			$message = array('result' => $data['upload_data']['file_name'],'status'=>1);
		}
		return $message;
	}

	public function image_upload2($image_path,$img){ 			
		$config['upload_path'] = '.'.$image_path;
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		//$config['min_width']  = '200';
		//$config['min_height']  = '200';
		//$config['max_size']	= '100';
		//$config['max_width']  = '1024';
		//$config['max_height']  = '768';
		$config['encrypt_name']  = true;
		$this->ci->load->library('upload', $config);
		if ( ! $this->ci->upload->do_upload($img)){
			$message = array('result' => $this->ci->upload->display_errors(),'status'=>0);
		}else{ 
			$data = array('upload_data' => $this->ci->upload->data());
			$message = array('result' => $data['upload_data']['file_name'],'status'=>1);
		}
		return $message;
	}
}