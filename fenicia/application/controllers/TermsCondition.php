<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TermsCondition extends MY_Controller {

	public function __construct() {
		parent::__construct();
		
	}
	public function index(){	
		$this->load->view('terms_condition.html');
	}	
	public function menu_non_veg(){	
		$this->load->view('finicia-menu-non-veg.html');
	}
	public function menu_veg(){	
		$this->load->view('finicia-menu-veg.html');
	}
	public function menu_beverages(){	
	    //echo"sdhfks";exit;
		$this->load->view('menu-beverages.html');
	}
	
	//////alcohol
	public function alcohol(){	
	    //echo"sdhfks";exit;
		$this->load->view('alcohol.html');
	}
	
	//////for contact us////////////////////
	public function contactus(){	
		$this->load->view('contactus.html');
	}	
}