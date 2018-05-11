<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Helper method for send emails.
 */
 if(!function_exists('sendSmtpEmail'))
{

	function sendSmtpEmail($from,$to,$subject,$messages)
	{
		try {
				
		    $CI =&get_instance();
			$CI->load->library('email', config_item('smtpEmailSettings'));
            $CI->email->set_newline("\r\n");
            $CI->email->from($from, 'BPlus');
			$list = $to;
			$CI->email->to($list);
			$CI->email->subject($subject);
			$CI->email->message($messages);
			if ($CI->email->send())
			{
				return true;
			}
			else
			{
				//neatPrintAndDie($CI->email->print_debugger());
			}
		}
		catch (Exception $e)
		{
			//neatPrintAndDie('Caught exception: ',  $e->getMessage(), "\n");
		}
			
	}
	
}
if(!function_exists('send_notification')){
	
	function send_notification($from="",$to,$content='',$subject='')
	{
		if($to !=''){
			$CI  = & get_instance();
			$CI->load->library('email');
			$config = Array(
					    'protocol' => 'smtp',
					    'smtp_host' => 'ssl://smtp.gmail.com',
					    'smtp_port' => 465,
					    'smtp_user' => 'testokler@gmail.com',
					    'smtp_pass' => 'a1sdfghjkl',
					    'mailtype'  => 'html', 
					    //'charset'   => 'iso-8859-1'
					);
			$CI->email->initialize($config);
			$CI->email->set_newline("\r\n");
			$CI->email->from($from);
			$CI->email->to($to);
			if($subject != '')
				$CI->email->subject($subject);
			if($content != '')
				$CI->email->message($content);
			//echo $content."<br/>";
			if($CI->email->send())
			{
				$CI->email->clear();
				return true;
			}
			else {
				echo show_error($CI->email->print_debugger());
				return false;
			}
			
		}

	}
}

if(!function_exists('otpgeneration')){
	function otpgeneration(){
		
	}
}

if(!function_exists('send_sms')){

	function send_sms($phoneNo="",$messageText)
	{
	
		 //1) verification code needs to sent for mobile
			 
			//Your authentication key
			$authKey = "37734AfiN59oTS557ae3d9";
			 
			//Multiple mobiles numbers separated by comma
			$mobileNumber = $phoneNo;
			 
			//Sender ID,While using route4 sender id should be 6 characters long.
			$senderId = "EOKLER";
			 
			//Your message to send, Add URL encoding here.
			$message = urlencode($messageText);
			 
			//Define route
			$route = "2";
			//Prepare you post parameters
			$postData = array(
					'authkey' => $authKey,
					'mobiles' => $mobileNumber,
					'message' => $message,
					'sender' => $senderId,
					'route' => $route
			);
			 
			//API URL
			$url="https://control.msg91.com/sendhttp.php";
			 
			// init the resource
			$ch = curl_init();
			curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postData
			//,CURLOPT_FOLLOWLOCATION => true
			));
			 
			//Ignore SSL certificate verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			//get response
			$output = curl_exec($ch);
			 
			//Print error if any
			if(curl_errno($ch))
			{
				echo 'error:' . curl_error($ch);
			}
			 
			curl_close($ch);
			return $output;
		}

	
}

/**
 * Helper method to fetch address from latitude and longitude.
 */
if(!function_exists('getAddressFromLatLong')){
	
	function getAddressFromLatLong($latitude,$longitude){
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
		$json = @file_get_contents($url);
		$data=json_decode($json);
		$status = $data->status;
		if($status=="OK")
			return $data->results[0]->formatted_address;
		else
			return false;
	}
}

/**
 * This method is used to convert the base64 encoded string to Image file
 */
if(!function_exists('base64decodeToImage')){
	
	function base64decodeToImage($base64Data,$imgtype,$flag=""){
		$milliseconds = round(microtime(true) * 1000);
		$data = base64_decode($base64Data);
		//$new_file_name = time().".".$imgtype;
		$new_file_name = $milliseconds.".".$imgtype;
		if($flag == "prescription"){
			$result = file_put_contents(PRESCRIPTION_UPLOAD_PATH.$new_file_name, $data);
		}elseif($flag == "emr"){
			$result = file_put_contents(EMR_UPLOAD_PATH.$new_file_name, $data);
		}else{
			$result = file_put_contents(PROFILE_PIC_UPLOAD_PATH.$new_file_name, $data);
		}
		if($result > 0)
			return $new_file_name;
		else
			return $result;
	}
	
}

/**
 * This method is used to generate a random number for password generation.
 */
if(!function_exists('generateRandNumber')){
	
	function generateRandNumber($length=0){
		$characters = '0123456789';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return strtolower($randomString);
	}
}

if(!function_exists('get_data')){
	function get_data($table,$where=FALSE,$select=FALSE,$limit=FALSE)
	{
		$ci =& get_instance();
		if($where)
			$ci->db->where($where);
		if($select)
			$ci->db->select($select);
		if($limit)
			$ci->db->limit($limit);
	
		return $ci->db->get($table);
	}
}

if(!function_exists('get_mail_template')){
	function get_mail_template($id)
	{
		return get_data('Email_Templates',array( 'id'=>$id))->row_array();
	}
	
}

if(!function_exists('time_ago')){
	function time_ago($date_time)
	{
		$date2= date_create(date('Y-m-d H:i:s'));
		$date1= date_create($date_time);
		$diff=date_diff($date1,$date2);
		$left='0 sec ago';
		if($date1 < $date2)
		{
			if($diff->s != 0)
				$left = $diff->s.' sec ago';
			if($diff->i != 0)
				$left = $diff->i.' mins ago';
			if($diff->h != 0)
				$left = $diff->h.' hours ago';
			if($diff->d != 0)
				$left = $diff->d.' days ago';
			if($diff->m != 0)
				$left = $diff->m.' months ago';
			if($diff->y != 0)
				$left = $diff->y.' years ago';
		}
	
		return $left;
	}
}
if(!function_exists('mail_section')){
	function mail_section($mode,$email,$user_name="",$password="")
	{
		$status="false";
		switch ($mode) {
			case 'forgot_password':
				$get_email_info	=	get_mail_template('1');
				$email_subject		=	$get_email_info['subject'];
				$email_content		=	$get_email_info['message'];
				$replace			=	array(	 '###USERNAME###'=>$user_name
						,'###LOGINNAME###'=>$user_name
						,'###PASSWORD###'=>$password
						,'###EMAIL###'=>$email
						,'###site_url###'=>base_url());
				$email_content		=	strtr($email_content,$replace);
				if(send_notification(NOREPLY_EMAIL,$email,$email_content,$email_subject))
					$status=true;
				else
					$status=false;
				break;
			case 'signup':
				$get_email_info	=	get_mail_template('6');
				$email_subject		=	$get_email_info['subject'];
				$email_content		=	$get_email_info['message'];
				$replace			=	array(
						'###USERNAME###'=>$user_name
						,'###SITENAME###'=>admin_config()->company_name
						,'###LOGINNAME###'=>$user_name
						,'###PASSWORD###'=>$password
						,'###SITE_URL###'=>base_url().'user');
				$email_content		=	strtr($email_content,$replace);
				if(send_notification(NOREPLY_EMAIL,$email,$email_content,$email_subject))
					$status=true;
				else
					$status=false;
				break	;
			case 'doctor':
				$get_email_info		=	get_mail_template('7');
				$subject  	=   str_replace('{site_name}', COMPANY_NAME, $get_email_info['subject']);
				$email_content		=	$get_email_info['message'];
				$replace			=	array(
						'{customer_name}'=>$user_name
						,'{site_name}'=>COMPANY_NAME
				);
				$email_content		=	strtr($email_content,$replace);
				if(send_notification(NOREPLY_EMAIL,$email,$email_content,$subject))
					$status=true;
				else
					$status=false;
				break	;
			case 'doctor_approve':
				$get_email_info		=	get_mail_template('5');
				$email_subject		=	$get_email_info['subject'];
				$email_content		=	$get_email_info['message'];
				$replace			=	array(
						'###USERNAME###'=>$user_name
						,'###EMAIL###'=>$email
						,'###PASSWORD###'=>$password
				);
				$email_content		=	strtr($email_content,$replace);
				if(send_notification(NOREPLY_EMAIL,$email,$email_content,$email_subject))
					$status=true;
				else
					$status=false;
				break	;
			default:
				# code...
				break;
		}
		return $status;
	
	}
}

function get_lat_long($address)
{
    $address = str_replace(" ", "+", $address);
	$address =  urlencode( $address);
    $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=US&key=AIzaSyA4mW9bS8UJ8xHJAMFxSOctIt5f_Rj6jJM");
    $json = json_decode($json);
	if(isset($json) && count($json->{'results'}) > 0)
	{
		$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
		$long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
		$formatted_address = $json->{'results'}[0]->formatted_address;
		return array('lat' => $lat, 'long' => $long, 'formatted_address' => $formatted_address);
	}
	else
	{ 
		return '';
	}
}

function calculateDistance($userLat,$userLon,$lat,$lon)
{
	
          $theta = $userLon - $lon;
		  $dist = sin(deg2rad($userLat)) * sin(deg2rad($lat)) +  cos(deg2rad($userLat)) * cos(deg2rad($lat)) * cos(deg2rad($theta));
		  $dist = acos($dist);
		  $dist = rad2deg($dist);
		  $miles = $dist * 60 * 1.1515;
		  $distance= ($miles* 1.609344);
          $distance=round($distance,3);
		  return $distance;
           //if ($unit == "K") {
		   //print_r($miles  * 1.609344);die();
			  
			//  } else if ($unit == "N") {
			//	  return ($miles * 0.8684);
			//	} else {
			//	return $miles;
			//}
}


function sendFCMNoti($json){
$cookie_jar = 'cookies.txt';
$ch = curl_init("https://fcm.googleapis.com/fcm/send");
$header=array('Content-Type: application/json',
"Authorization: key=AIzaSyCx22pcyw6bRl82hn3aSES_CGhVau3tASc");
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar);
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "$json");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

 $store =  curl_exec($ch);
 $result = curl_getinfo($ch);

 curl_close($ch);
 
 return $store;
}	

function sendOTP($jsondata)
{
  $api_url ='http://map-alerts.smsalerts.biz/api/v3/?method=sms.json&api_key=Ac0553dac69370e10187eab73f66201c8&json='.urlencode($jsondata);
  $response = file_get_contents($api_url);
  return  $response;
}
?>