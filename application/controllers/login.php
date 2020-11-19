<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class login extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		    $this->load->model('user_model');
			header('Access-Control-Allow-Origin: *');
			header("Access-Control-Allow-Methods: POST,GET, OPTIONS");
			header("Access-Control-Allow-Headers: *");
			header('Content-Type: application/json');
			$postdata = file_get_contents("php://input");
            $this->request = json_decode($postdata);
			
			//print_r($this->request);
		//$this->load->helper('cookie');
		///print_r($this->input->post());die;
		/*$result=$this->user_model->getToken($this->input->post('AuthKey'));
		
		if($result)
		{ 

			echo json_encode($result);die;
		}  */
		
	}
	function index()
	{	
		 //echo "test";die;
			
	
	    $data= $this->user_model->login($this->request);
	
		if($data)
		{ 
	
	        $result['status'] = 1;
            $result['message'] = "Success";
            $result['result'] = $data;
			
		}else{
			
			$result['status'] = 0;
            $result['message'] = "failed";
            
			
		}
		echo json_encode($result);die;
			
	}
	
	function itemDetails()
	{
	  $data= $this->user_model->getItemList($this->input->post());
	  
	  if($data)
		{ 
	
	        $result['status'] = 1;
            $result['message'] = "Success";
            $result['result'] = $data;
			
		}else{
			
			$result['status'] = 0;
            $result['message'] = "failed";
            
			
		}
		echo json_encode($result);die;
	}
	
	function AddtoCart()
{
	
	 $insertArr= array(
				"itemId"=>$this->request->data->id,
				"user_id"=>$this->request->user_id,
				"status"=>1,	 
				"order_status"=>0,
                "created"=>date('Y-m-d H:i:s') 				
				);
				
	 $data= $this->user_model->AddtoCart($insertArr);
	
}


function getuserOrder()
{  
     	$data= $this->user_model->getorders($this->request);
		
	
	  if($data)
		{ 
	
	        $result['status'] = 1;
            $result['message'] = "Success";
            $result['result'] = $data;
			
		}else{
			
			$result['status'] = 0;
            $result['message'] = "failed";
            
			
		}
		echo json_encode($result);die;
}

function deleteItem()

{ //echo $this->request->id;
	$data= $this->user_model->deleteItem($this->request->id);  
}

function placeOrder()
{
	//print_r(json_decode($this->request->order));die;
	
	$data= $this->user_model->palceOrder($this->request);
	
}
	

}