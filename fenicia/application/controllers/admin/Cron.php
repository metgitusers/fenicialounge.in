<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends MY_Controller {

	public function __construct() {
		parent::__construct();
		$this->admin=$this->session->userdata('admin');
		$this->load->model('admin/Mreservation');
		// if($this->session->userdata('role_id') == '')
		// {
		// 	redirect('admin');
		// 	die();
		// }
	}
	
	public function dailyReservationReportOnSameDay() { 
		//echo $this->session->userdata('email');die;
		$data_arr 	= array();
		$cancellation_reason = '';
		$status = '';
		$reservation_data  = $this->Mreservation->getReservationData('current');
		//pr($reservation_data);

		$attach="";
		$msg="No booking found";

		if(!empty($reservation_data)){
			$delimiter = ",";
		    $filename = "report/".rand()."_booking_today_" . date('Y-m-d') . ".csv";
		    
		     //set headers to download file rather than displayed
		    //header('Content-Type: text/csv');
		    //header('Content-Disposition: attachment; filename="' . $filename . '";');
		    //create a file pointer
		    $f = fopen($filename,"w");
		    //$f = fopen('php://output', 'wb');
		    
		    //set column headers
		    $fields = array('Reservation No', 'Reservation Type', 'Member Name','Email','Phone', 'Reservation Date','Reservation Time','Zone','Cover Charge','No. Of Guests','Status','Reason For Cancellation');
		    fputcsv($f, $fields, $delimiter);
		    
		    //output each row of the data, format line as csv and write to file pointer
		    foreach($reservation_data as $row){
		    	$cancellation_reason = "";
		        if($row['status'] == '1'){
		        	$status = 'Pending';

		        }
		        elseif($row['status'] == '2'){
		        	$status = 'Confirmed';
		        }
		        elseif($row['status'] == '3'){
		        	$status = 'NO-show';
		        }
		        elseif($row['status'] == '0'){
		        	$status = 'Cancelled';
		        	$cancellation_reason = $row['cancellation_reason'];
		        }
		        $phone_no="'".$row['country_code']."".$row['member_mobile']."'";
		        $lineData = array($row['reservation_id'], $row['reservation_type'],$row['full_name'], $row['email'],$phone_no,date('l d M Y', strtotime($row['reservation_date'])),date('h:i A',strtotime($row['reservation_time'])), $row['zone_name'],$row['zone_price'],$row['no_of_guests'], $status,$cancellation_reason);
		        fputcsv($f, $lineData, $delimiter);
		    }
		    
		fclose($f);

		$attach=$filename;
		$msg="Please find the attached doc for booking report of today.";
		}

		/////////////////////////////send mail//////////////////////////////////
		$logo                     = base_url('public/images/logo.png');
		$email                    ="gm@fenicialounge.in,marketing@fenicialounge.in,grm@fenicialounge.in";  
		//$email                    ="ishani.banerjee@met-technologies.com";  
		$mail['to']               = $email;    
		$mail['subject']          = 'Club Fenicia - Booking report of today';	
		$mail['name']             = 'Fenicia administrator';
		$mail_temp                = file_get_contents('./global/mail/booking_report_template.html');
		$mail_temp                = str_replace("{web_url}", base_url(), $mail_temp);
		$mail_temp                = str_replace("{logo}", $logo, $mail_temp);
		$mail_temp                = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
		$mail_temp                = str_replace("{name}", $mail['name'], $mail_temp); 
		$mail_temp                = str_replace("{msg}", $msg, $mail_temp);         
		$mail_temp                = str_replace("{current_year}", date('Y'), $mail_temp);           
		$mail['message']          = $mail_temp;
		$response 				  = attachment_mail($mail,$attach);
		if($response==1)
		{
			echo "Success";
		}	
		else
		{
			echo "Please try again.";
		}
	}
	public function dailyReservationReportOnPreviousDay() { 
		//echo $this->session->userdata('email');die;
		$data_arr 	= array();
		$reservation_data = $this->Mreservation->getReservationData('previous');
		//pr($reservation_data);	
		$attach="";
		$msg="No booking found";
		if(!empty($reservation_data)){
			$delimiter = ",";
		    $filename = "report/".rand()."_booking_previousday_" . date('Y-m-d') . ".csv";
		    
		     //set headers to download file rather than displayed
		    //header('Content-Type: text/csv');
		    //header('Content-Disposition: attachment; filename="' . $filename . '";');
		    //create a file pointer
		    $f = fopen($filename,"w");
		    //$f = fopen('php://output', 'wb');
		    
		    //set column headers
		    $fields = array('Reservation No', 'Reservation Type', 'Member Name','Email','Phone', 'Reservation Date','Reservation Time','Zone','Cover Charge','No. Of Guests','Status','Reason For Cancellation');
		    fputcsv($f, $fields, $delimiter);
		    
		    //output each row of the data, format line as csv and write to file pointer
		    foreach($reservation_data as $row){
		    	$cancellation_reason = "";
		        if($row['status'] == '1'){
		        	$status = 'Pending';

		        }
		        elseif($row['status'] == '2'){
		        	$status = 'Confirmed';
		        }
		        elseif($row['status'] == '3'){
		        	$status = 'NO-show';
		        }
		        elseif($row['status'] == '0'){
		        	$status = 'Cancelled';
		        	$cancellation_reason = $row['cancellation_reason'];
		        }
		        $phone_no="'".$row['country_code']."".$row['member_mobile']."'";
		        $lineData = array($row['reservation_id'], $row['reservation_type'],$row['full_name'], $row['email'],$phone_no,date('l d M Y', strtotime($row['reservation_date'])),date('h:i A',strtotime($row['reservation_time'])), $row['zone_name'],$row['zone_price'],$row['no_of_guests'], $status,$cancellation_reason);
		        fputcsv($f, $lineData, $delimiter);
		    }
		    
		fclose($f);

		$attach=$filename;
		$msg="Please find the attached doc for booking report of previous day.";
		}	

		/////////////////////////////send mail//////////////////////////////////
		$logo                     = base_url('public/images/logo.png');
		//$email                    ="ishani.banerjee@met-technologies.com";  
		$email                  ="md@met-technologies.com,gm@fenicialounge.in";  
		$mail['to']               = $email;    
		$mail['subject']          = 'Club Fenicia - Booking report of previous day';	
		$mail['name']             = 'Fenicia administrator';
		$mail_temp                = file_get_contents('./global/mail/booking_report_template.html');
		$mail_temp                = str_replace("{web_url}", base_url(), $mail_temp);
		$mail_temp                = str_replace("{logo}", $logo, $mail_temp);
		$mail_temp                = str_replace("{shop_name}", 'Club Fenicia', $mail_temp);  
		$mail_temp                = str_replace("{name}", $mail['name'], $mail_temp); 
		$mail_temp                = str_replace("{msg}", $msg, $mail_temp);         
		$mail_temp                = str_replace("{current_year}", date('Y'), $mail_temp);           
		$mail['message']          = $mail_temp;
		$response 				  = attachment_mail($mail,$attach);
		if($response==1)
		{
			echo "Success";
		}	
		else
		{
			echo "Please try again.";
		}
	}
}