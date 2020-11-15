<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class parentClass extends CI_Controller {

	public function parentClass(){
		parent::__construct();
		
		if($this->session->userdata('admin_id')!='10000')
		{
		$a=0;
		for($i=0;$i<count($this->session->userdata('emp_url'));$i++)
		{//echo  current_url().'=='.$this->session->userdata('emp_url')[$i].'<br>';
				if(strstr(current_url(),$this->session->userdata('emp_url')[$i]))
				{
			//echo 'ddd';		
			$a=1;
				}
		}
		if($a==0)
		{
				redirect();
		}
}
		//$this->isLoggedIn();
		$this->currentUser = array("email"=>$this->session->userdata('email'),
									"name"=>$this->session->userdata('firstName') . ' ' . $this->session->userdata('lastName'),
									"groupId"=>$this->session->userdata('groupId'),
									"groupName"=>$this->session->userdata('groupName'));

	}

	function isLoggedIn()
	{
		$isLoggedIn = $this->session->userdata('isLoggedIn');
		if(!isset($isLoggedIn) || $isLoggedIn != true)
		{
			redirect('login');
		}
	}



	function header()
	{
		$data = array();
		$holiday = $this->attendance_model->holidayList(1,1,date('Y-m-d'));
		$data['holiday'] = $holiday;
		//pre($holiday);die;
		
		$this->load->view('front/includes/header', $data);
	}
	
	function script()
	{
		$data['content'] = "";
		$this->load->view('includes/script', $data);
	}


	function menu()
	{
		$data['content'] = "";
		$role = $this->session->userdata('admin_role_id');
		$data['employeeId'] = $this->session->userdata('employeeId');
		$this->load->view('includes/left-menu', $data);
	}

	function footer()
	{
		$data['content'] = "";
		$this->load->view('front/includes/footer', $data);
	}
	function right()
	{
	$data['content'] = "";
	$this->load->view('includes/right-sidebar', $data);
	}
	
	function page_heading(){
	$data['content'] = "";
	$this->load->view('includes/page-heading', $data);
	}
	
	function employee_heading(){
	$data['content'] = "";
	$data['employeeId']= $this->session->userdata('employeeId');
	$data['result'] = $this->employee_model->employeeListing($data['employeeId']);
	//pre($result);
	$this->load->view('includes/employee-heading', $data);
	}
	
	function general_master_left(){
		$data['content'] = "";
		//$role = $this->session->userdata('admin_role_id');
		$data['employeeId'] = $this->session->userdata('employeeId');
		$data['Module']= $this->parent_model->getModule(1);
	$this->load->view('includes/master-left', $data);
	}
	
	function employee_left(){
	$data['content'] = "";
	$data['userMenuList'] = $this->employee_model->userMenuList();
	$this->load->view('includes/employee-left', $data);
	}
	
	function pageLoad_login($page, $data)
	{
		self::script();
		$this->load->view($page, $data);
		
	}
	
	function pageLoad($page, $data)
	{
		self::script();
		self::header();
		self::menu();
		self::page_heading();
		$this->load->view($page, $data);
		self::footer();
	}
	
	function pageLoadpolicy($page, $data)
	{
//		echo 'hell';die;
		self::header();
		$this->load->view($page, $data);
		self::footer();
	}
	function pageLoademployee($page, $data)
	{
		self::script();
		self::header();
		self::menu();
		self::employee_heading();
		self::employee_left();
		$this->load->view($page, $data);
		self::footer();
	}
	function masterpageLoad($page, $data)
	{
		self::script();
		self::header();
		self::menu();
		self::page_heading();
		//self::master_left();
		$this->load->view($page, $data);
		self::footer();
	}
	
	

	function createDDArray($array, $key, $val)
	{
		$arr = array();
		$arr[''] = 'Select';
		foreach($array as $array)
		{
			$arr[$array[$key]] = $array[$val];
		}
		return $arr;
	}

	function gridSearchArray($array, $key, $val)
	{
		$arr = '';

		foreach($array as $array)
		{
			$arr .= $array[$key].':'.$array[$val].';';
		}
		return rtrim($arr,';');
	}
	
	function newActivity($table, $data)

    {

             $data['dateofActivity'] =date('Y-m-d H:i:s',time());

             $data['activityBy'] = $this->session->userdata('admin_id');

             $this->parent_model->query_insert($table, $data);

             

    }
	
function getTimeDiff($dtime,$atime)
{
    $nextDay=$dtime>$atime?1:0;
    $dep=explode(':',$dtime);
    $arr=explode(':',$atime);


    $diff=abs(mktime($dep[0],$dep[1],0,date('n'),date('j'),date('y'))-mktime($arr[0],$arr[1],0,date('n'),date('j')+$nextDay,date('y')));

    //Hour

    $hours=floor($diff/(60*60));

    //Minute 

    $mins=floor(($diff-($hours*60*60))/(60));

    //Second

    $secs=floor(($diff-(($hours*60*60)+($mins*60))));

    if(strlen($hours)<2)
    {
        $hours="0".$hours;
    }

    if(strlen($mins)<2)
    {
        $mins="0".$mins;
    }

    if(strlen($secs)<2)
    {
        $secs="0".$secs;
    }

    //return $hours.':'.$mins;
	return $hours.':'.$mins.':'.$secs;

}
	
	
}

