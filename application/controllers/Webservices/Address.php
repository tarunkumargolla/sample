<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Address extends REST_Controller {

	function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');
		$this->load->helper('common_helper');
	}
function address_post()
{
   $this->load->library('form_validation');
       $this->form_validation->set_rules('name','Name');
       $this->form_validation->set_rules('deviceId','DeviceId','trim|required');
        $this->form_validation->set_rules('lat','Lat');
     $this->form_validation->set_rules('lon','Lon');
        if($this->form_validation->run()!=false) 
              {
			$name = trim($this->post('name'));
                       
			$deviceId = trim($this->post('deviceId'));
                        $lat =trim($this->post('lat'));
                        $lon =trim($this->post('lon'));
                        $token=generateRandNumber(30);
                        $userData['name'] = $name; 
                        $userData['deviceId'] = $deviceId; 
                        $userData['lat'] = $lat; 
                        $userData['lon'] = $lon; 
                        $userData['token']=$token;
                        //print_r($userData);die();
			   	$where = array(); 
					$result = $this->insertOrUpdate(TBL_USERS,$where,$userData);
                                        $from_userId = $result;
                                       // $userdata['token'] = $token;
                                       // $userdata['from_userId'] =$from_userId;
                                       // print_r($userdata);die();
                                       // $where = array(); 
                                       // $res = $this->insertOrUpdate(TBL_USERSHARE,$where,$userdata);
						if($result){
						 //$this->updateLatLon_post($deviceId);
						   
						   $message = array('status' => 1,'message' =>'data inserted','userId'=>$result,'token'=>$token);
				           $this->response(array($message), 200);
						   
				   }else{
					 $message = array('status' => 0,'message' => 'data not inserted','result' =>"");
					 $this->response(array($message), 200);
				   }
					
		
	}
        else
        {
            $message = array('status' => 0,'message' =>strip_tags(validation_errors()));
			  $this->response(array($message), 200);
        }
}
        function shareLocation_post()
        {
            $fromToken = trim($this->post('token'));
            $name = trim($this->post('name'));
            $deviceId = trim($this->post('deviceId'));
            $toToken= generateRandNumber(30);
            $userData['name'] =$name;
            $userData['deviceId'] =$deviceId;
            $userData['token'] =$toToken;
            $where = array();
           $res = $this->insertOrUpdate(TBL_USERS,$where,$userData);
           $to_userId =$deviceId;
            $where =array('token'=>$fromToken,'isActive'=>1,'isDelete'=>0);
            $result = $this->getSingleRecord(TBL_USERS, $where);//print_r($result);die();
            if($result){
                $from_userId =$result['deviceId'];
                $shareToken =generateRandNumber(30);
                $sldata['from_userId']=$from_userId;
                $sldata['shareToken']=$shareToken;
                $sldata['to_userId']=$to_userId;
                $where =array();
                $result = $this->insertOrUpdate(TBL_LOCATION_SHARE,$where,$sldata);
            $message = array('status' => 1,'message' =>'token is valid','userId'=>$res,'ShareToken'=>$shareToken);
				           $this->response(array($message), 200);
						   
				   }else{
					 $message = array('status' => 0,'message' => 'Invalid Token','result' =>"");
					 $this->response(array($message), 200);
				   }
                        /*$name = trim($this->post('name'));
			$deviceId = trim($this->post('deviceId'));
                        $token =trim($this->post('token'));
                        $lat =trim($this->post('lat'));
                        $lon =trim($this->post('lon'));
                        $userData['name'] = $name; 
                        $userData['deviceId'] = $deviceId; 
                         $userData['lat'] = $lat; 
                        $userData['lon'] = $lon; 
			   	$where = array(); 
					$result = $this->insertOrUpdate(TBL_USERS,$where,$userData);
                                        $to_userId = $result;
                                        $userdata['to_userId'] = $to_userId; 
                                        $where = array('token'=>$token); 
                                        $res =$this->insertOrUpdate(TBL_USERSHARE,$where,$userdata);
						if($result &&$res){

						   $message = array('status' => 1,'message' =>'data inserted','userId'=>$result,'token'=>$token);
				           $this->response(array($message), 200);
						   
				   }else{
					 $message = array('status' => 0,'message' => 'data not inserted','result' =>"");
					 $this->response(array($message), 200);
				   }*/
        }
        function locationGet_post()
        {
            $shareToken =trim($this->post('shareToken'));
            $where =array('shareToken'=>$shareToken,'isActive'=>1,'isDelete'=>0);
            $result =$this->getSingleRecord(TBL_LOCATION_SHARE, $where);
            $from_userId =$result['from_userId'];
          // print($from_userId);die();
            
            $where =array('deviceId'=>$from_userId,'isActive'=>1,'isDelete'=>0);
            $res =$this->getRecords(TBL_USERS,$where);//print_r($res);die();
            $lat =$res[0]->lat;
           // print($lat);die();
            $lon =$res[0]->lon;
            if($result){
            $message = array('status' => 1,'message' =>'token is valid','lat'=>$lat,'lon'=>$lon);
				           $this->response(array($message), 200);
            }
            else{
                $message = array('status' => 0,'message' => 'Invalid ShareToken','result' =>"");
					 $this->response(array($message), 200);
            }
        }
        function updateLatLon_post()
        {
            $deviceId = trim($this->post('deviceId'));
            $lat = trim($this->post('lat'));
            $lon  =trim($this->post('lon'));
            $userdata['deviceId'] =$deviceId;
            $userdata['lat'] =$lat;
            $userdata['lon'] =$lon;
            $where =array('deviceId'=>$deviceId,'isActive'=>1,'isDelete'=>0);
            $result = $this->insertOrUpdate(TBL_USERS,$where,$userdata);//print_r($res);die();
                        if($result ){
                         $message = array('status' => 1,'message' =>'Data Updated','lat'=>$lat,'lon'=>$lon);
				           $this->response(array($message), 200);
            }
            else{
                $message = array('status' => 0,'message' => 'Data Not Updated','result' =>"");
					 $this->response(array($message), 200);
            }
        }
}
?>