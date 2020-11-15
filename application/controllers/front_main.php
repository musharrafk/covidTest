<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class front_main extends CI_Controller
{
	public function front_main()
	{
		parent::__construct();
		//$url = explode(',',$this->session->userdata('emp_url'));
		//pre($url);
		//pre($this->session->userdata);	die;
		/* if(!$this->session->userdata('candidate'))
		{
			if($this->session->userdata('admin_id')!='10000')
			{
				$url = $this->modules_model->access_url($this->session->userdata('emp_access'));
				$a=0;
				for($i=0;$i < count($url);$i++)
				{
				//echo  current_url().'=='.$this->session->userdata('emp_url')[$i].'<br>';
					if(strstr(current_url(),$url[$i]))
					{
					//echo 'ddd';
						$a=1;
					}
				}
				if($a==0)
				{
					if($_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest')
					{
						redirect('login/logout');
					}
				}
			}
		}
		else
		{
			if((strstr(current_url(),'candidate/uploadDocument')) or (strstr(current_url(),'candidate/insertDoucment')))
			{
				$a=1;
			}
			if($a==0)
			{
				if($_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest')
				{
					redirect('login/candidate_logout');
				}
			}
		} */
	}
	function isLoggedIn()
	{	

		$currentUrl           =  current_url();	
     	$isLoggedIn           =  $this->session->userdata('empId');
		$moduleUrlId          =  explode(',',$this->session->userdata('emp_access'));	
		$urlarray             =  explode(base_url(),$currentUrl);	
		$countUrlSegment      =  count($urlarray);  
		 
		// for candidate page check	
		$candidatePages = isset($urlarray[1])?explode('/',$urlarray[1]):'';

		if(!isset($isLoggedIn) || $isLoggedIn != true)
		{
			 redirect('login');
		}  else  {
			  $notCheckUrl = array('dashboard','company-policy','generalMaster','company-handbook');
			  $adminUrl    = array('home');
		  if (!$this->input->is_ajax_request()) {
		 if($isLoggedIn !='10000' || $candidatePages != 'candidate') {	
			 	if($isLoggedIn !='10000' ) {				
					if(in_array($urlarray[1],$notCheckUrl)){
					    
					}
					else {																		
						$routesList         =  $this->router->routes;
						$routeDataList      =  array_keys($routesList);
						$secRoutesegments   =  isset($urlarray[1])?explode('/',$urlarray[1]):'';
						$countUrlSegment    =  count($secRoutesegments); 
						if($countUrlSegment == 2){							
							$urlData  =   $this->modules_model->check_module_permission($urlarray[1]);							
							if(!empty($urlData) && count($urlData) > 0){
								if (in_array($urlData['id'], $moduleUrlId)){
																							
								} else {							
									redirect('permission_auth');			   
								}
							} 
						} else if($countUrlSegment == 1){	
							$urlData  =   $this->modules_model->check_module_permission($secRoutesegments[0]);						
							if(!empty($urlData) && count($urlData) > 0){
								if (in_array($urlData['id'], $moduleUrlId)){															
								} else {							
									redirect('permission_auth');			   
								}
							} else {
								/*if(in_array('home',$adminUrl)){
									redirect('permission_auth');	
								}*/
							}

						}
					} 			 }  
		      }	
	       }
		}
	}
	function head($data)
	{
		$data['content'] = "";
		return $this->load->view('front/includes/head', $data, true);
	}
	/*function header($data)
	{
	$data['content'] = "";
	$holiday = $this->attendance_model->holidayList(1,1,'A',date('Y-m-d'));
	$data['holiday'] = $holiday;
	$this->load->view('front/includes/header', $data);
	}
	*/
	//display holiday on top page
	function pageLoadpolicy($page, $data)
	{
		self::header();
		$this->load->view($page, $data);
		self::footer();
	}
	function header($data)
	{
		$data['content'] = "";
		$holiday = $this->attendance_model->holidayList(1,1,'A',date('Y-m-d'));
		$data['holiday'] = $holiday;
//pre($holiday);die;
		$this->load->view('front/includes/header', $data);
	}
	function footer($data)
	{
		$data['content'] = "";
		$this->load->view('front/includes/footer', $data);
	}
	function page_top($data)
	{
		$data['content'] = "";
		return $this->load->view('front/includes/page_top', $data, true);
	}
	function page_bottom($data)
	{
		$data['content'] = "";
		return $this->load->view('front/includes/page_bottom', $data, true);
	}
	function left($data)
	{
		$data['content'] = "";
		$data['getProductList'] = $this->product_model->getProductList();
		return $this->load->view('front/includes/right', $data, true);
	}
	function slider($data)
	{
		$data['content'] = "";
		return $this->load->view('front/includes/slider', $data, true);
	}
	function pageLoad($page, $data)
	{
	
		$data['userMenuList'] = $this->employee_model->userMenuList();
		//pre($_GET['rel']);die;
		if($_GET['rel']=='tab')
		{
		   /* echo $page; */
			$this->load->view($page, $data);
		}
		else
		{
			self::header($data);
			//	self::menu();
			$this->load->view($page, $data);
			self::footer($data);
		}
	}
	function newActivity($table, $data)
	{
		$data['dateofActivity'] =date('Y-m-d H:i:s',time());
		$data['activityBy'] = $this->session->userdata('admin_id');
		$this->parent_model->query_insert($table, $data);
	}
	//candidate page load ARK start
	function candidate_pageLoad($page, $data)
	{
		$data['userMenuList'] = $this->candidate_model->userMenuList();
		/* echo $this->db->last_query();
		die; */
		//pre($_GET['rel']);die;
		if($_GET['rel']=='tab')
		{
			$this->load->view($page, $data);
		}
		else
		{
			self::candidate_header($data);
			//	self::menu();
			$this->load->view($page, $data);
			self::candidate_footer($data);
		}
	}
	function candidate_header($data)
	{
		$data['content'] = "";
		$data['result'] = $this->candidate_model->getCandiate($this->session->userdata('id'));
		
		$holiday = $this->attendance_model->holidayList(1,1,'A',date('Y-m-d'));
		$data['holiday'] = $holiday;
//pre($holiday);die;
		$this->load->view('candidate/include/header', $data);
	}
	function candidate_footer($data)
	{
		$data['content'] = "";
		$this->load->view('front/includes/footer', $data);
	}
	//candidate page load ARK end
}