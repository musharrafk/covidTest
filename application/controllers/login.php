<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
class login extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		header('Content-Type: application/json');
		//$this->load->helper('cookie');
		
		//$result=$this->user_model->getToken($this->input->post('AuthKey'));
		
		if($result)
		{ 
			echo json_encode($result);die;
		}
		
		
		
	}
	function index()
	{	
	
	    $result= $this->user_model->login($this->input->post());
		 echo json_encode($result);die;
		
			
	}
	function insertDataSet()
	{ 
	
	
	
		$url="https://5f1a8228610bde0016fd2a74.mockapi.io/getTestList";
	   $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
        CURLOPT_ENCODING       => "",     // handle compressed
        CURLOPT_USERAGENT      => "test", // name of client
        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
        CURLOPT_TIMEOUT        => 120,    // time-out on response
    ); 

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);

    $content  = curl_exec($ch);
	foreach(json_decode($content)  as $key=>$data)
	{
		//print_r($data);die;
		$insertArr[$key]=array(
	                "sr_no"=>$data->{'S.no'},
					"itemId"=>$data->itemId,
					"itemName"=>$data->itemName,
					"type"=>$data->type,
					"Keyword"=>$data->Keyword,
					"best_sellers"=>$data->{'Best-sellers'},
					"testCount"=>$data->testCount,
					//"Included_Tests"=>$data->{'Included-Tests'}, 
					"url"=>$data->url, 
					"minPrice"=>$data->minPrice,
					"labName"=>$data->labName,
					"fasting"=>$data->fasting,
					"availableAt"=>$data->availableAt,
					"popular"=>$data->popular,
					"category"=>$data->category,
					"objectID"=>$data->objectID
					
					
		  
		);
	}
	$this->user_model->insertDataSet($insertArr);
	//print_r($content);
	//json_decode($content,, true); 
	
	}
	
	function itemDetails()
	{
	  $result= $this->user_model->getItemList($this->input->post());
	}

}