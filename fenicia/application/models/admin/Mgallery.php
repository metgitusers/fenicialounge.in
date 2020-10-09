<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mgallery extends CI_Model {

	public function get_gallery_list($status ='0'){
        $result=array();
        $query = "select mg.* from master_gallery mg where mg.status = '".$status."'and mg.is_delete = '0' order by mg.gallery_id desc";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
	public function get_gallery_img($gallery_id){
        $result=array();
        $query = "select gi.* from gallery_images gi where gi.gallery_img_id = '".$gallery_id."'";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->result_array();
        return $result;
    }
    public function getGalleryDataByImgId($image_id){
        $result=array();
        $gallery_name ='';
        $query = "select master_gallery.gallery_name from gallery_images left join master_gallery on master_gallery.gallery_id = gallery_images.gallery_id where gallery_images.gallery_img_id = '".$image_id."'";
        //echo $query;exit;
        $query1 = $this->db->query($query);
        $result=$query1->row_array();
        if(!empty($result)){
            $gallery_name   = $result['gallery_name'];
        }
        return $gallery_name;
    }
}