<?php
 class PushNotification { 
  private $CI;
  function __construct() {
        $this->CI =& get_instance();
     }

  //public function send_android_notification($fcmtoken, $data) { 
  public function send_android_notification($push_array) { 
    //pr($data);
    $url = "https://fcm.googleapis.com/fcm/send";
            //$token = $fcmtoken; 
           // $serverKey = 'AAAA5hwBGCw:APA91bHEJFIHAomMiZmgpdWJ72pMVyE8RH-Z5fu7okAuYJBsNTSgPGirdQWZfLOvjuQ2dvWCYiwQp_5Kz664U7mSVFghVhnwCmNnA4VrfJLm7ZpU8ppln--mvD7pVSa-R6JlvUs1aXkL';   
            $serverKey = 'AAAAUdO59Sg:APA91bFE8bXNMWk8v5rLIXCfIj07Jl_YGoAdpAS0AZyQMOim-QH947UFN17MJbMLhbSv8qQggCswd3_CV5pNZCtHgCmQ0JnfGrSWmIrbdS3eRnuTeObehAKa9w_ymHRCPjtBpIohh6C-';
            // $title = $data['title'];
            // $body = $data['message'];
            // $notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => '1','data'=>array());
            // $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority'=>'high');
            $json = json_encode($push_array);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key='. $serverKey;
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
   
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarly
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
   
            //Send the request
            $response = curl_exec($ch);
            //Close request
            if ($response === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
  }
  //public function send_ios_notification($fcmtoken,$data){
  public function send_ios_notification($push_array){
            $url = "https://fcm.googleapis.com/fcm/send";
            //$token = $fcmtoken; 
            $serverKey = 'AAAAUdO59Sg:APA91bFE8bXNMWk8v5rLIXCfIj07Jl_YGoAdpAS0AZyQMOim-QH947UFN17MJbMLhbSv8qQggCswd3_CV5pNZCtHgCmQ0JnfGrSWmIrbdS3eRnuTeObehAKa9w_ymHRCPjtBpIohh6C-';
            //$serverKey = 'AAAA5hwBGCw:APA91bHEJFIHAomMiZmgpdWJ72pMVyE8RH-Z5fu7okAuYJBsNTSgPGirdQWZfLOvjuQ2dvWCYiwQp_5Kz664U7mSVFghVhnwCmNnA4VrfJLm7ZpU8ppln--mvD7pVSa-R6JlvUs1aXkL';
            // $title = $data['title'];
            // $body = $data['message'];
            // $notification = array('title' =>$title , 'text' => $body, 'sound' => 'default', 'badge' => '1','data'=>array());
            // $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority'=>'high');
            $json = json_encode($push_array);
            //print_r($json);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: key='. $serverKey;
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
   
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           // Disabling SSL Certificate support temporarly
           curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
   
            //Send the request
            $response = curl_exec($ch);
            //Close request
            if ($response === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
            
        }
  public function getAllDeviceList(){
   $query=$this->CI->db->get('devices');
   return $query->result_array();   
  } 
 }
?>