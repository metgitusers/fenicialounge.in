<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Custom404 extends CI_Controller
{
  public function __construct()
    {
        parent::__construct();
    }

  public function index()
  {
      $this->output->set_status_header('404');
      $this->data['content'] = 'custom404view'; // View name
      $this->load->view('admin/layouts/login',$this->data);
  }
}
