<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class api extends CI_Controller {


	function __construct()
	{
		parent::__construct();
		if($this->input->post('auth')!='trans#sfa')
		{
			$arr = array('msg'=>'auth failed');
			echo json_encode($arr);
			exit;
		}
	}

	function login()
	{
		$len = strlen($_POST['empId']);
		if($len==3){
		$row = $this->db->query("select * tbl_guest_master where empId='".$_POST['empId']."' and empPassword='".md5($_POST['password'])."' ")->row();
		}else{
		$row = $this->db->query("select * from tbl_emp_master where empId='".$_POST['empId']."' and empPassword='".md5($_POST['password'])."' ")->row();
		}
		$arr = array();
		if($row==0)
		{
		$arr['response'] = 'fail';
		$arr['msg'] = 'Invalid username or password';
		}
		else
		{
		$arr['response'] = 'success';
		$arr['msg'] = 'Login successfully';
		$msg = $this->db->query("select message from tbl_message where messageId='1'")->row();
		$arr['welcome_message'] = $msg->message;

		}
		echo json_encode($arr);
	}

	function get_stock_category()
	{
		echo json_encode($this->stock_model->getStockCategory());
	}

	function get_emp()
	{
		echo json_encode($this->user_model->getEmp());
	}

	function get_outlet()
	{
		echo json_encode($this->user_model->getOutlet());
	}

	function get_stock_status()
	{
		echo json_encode($this->user_model->getStockStatus());
	}

	function add_stock()
	{
		echo $this->stock_model->perform_stock();
	}

	function add_sale()
	{
		echo $this->stock_model->add_sale();
	}

	function get_available_sku()
	{
		echo json_encode($this->stock_model->get_available_sku());
	}

	function api_for_sale()
	{
		$arr = array();
		$arr = $this->stock_model->get_available_sku_imei();
		//$arr['imei'] = $this->stock_model->get_available_imei();
		
		//$arr['category'] = $this->stock_model->getStockCategory();
		echo json_encode($arr);
	}
	
	function add_escalation()
	{
		$arr=array();
		$arr['response'] = ($this->user_model->add_escalation());
		echo json_encode($arr);
	}
	function add_grievance()
	{
		$arr=array();
		$arr['response'] = ($this->user_model->add_grievance());
		echo json_encode($arr);
	}
	function add_posm()
	{
		$arr=array();
		$arr['response'] = ($this->posm_model->add_posm());
		echo json_encode($arr);
	}
	function cms($id)
	{
		$arr=array();
		$arr['response'] = $this->db->query("select * from tbl_cms where cmsId='".$id."'")->row();
		echo json_encode($arr);
	}

	function competition()
	{
		$num = $this->db->query("select * from tbl_competition where insertDate='".date('Y-m-d')."' and empId='".$this->input->post('empId')."'")->num_rows();

$arr = array();
		if($num==0)
		{
		$this->db->insert('tbl_competition',array('empId'=>$this->input->post('empId'),
													'insertDate'=>date('Y-m-d'),
													'price'=>$this->input->post('price'),
													'samsung'=>$this->input->post('samsung'),
													'micromax'=>$this->input->post('micromax'),
													'htc'=>$this->input->post('htc'),
													'sony'=>$this->input->post('sony')));
		
		$arr['response'] = 'success';
		$arr['msg'] = 'Added successfully';
		
		}
		else
		{
			$arr['response'] = 'Competition details already submitted for today.';
		}
		echo json_encode($arr);
	}

	function get_mis()
	{
		$empId = trim($this->input->post('empId'));
		$fromDate = date('Y-m').'-01';
		$toDate = date('Y-m-d');

		//echo "select * from tbl_sale where empId='".$empId."' where saleDate>='".$fromDate."' and saleDate<='".$toDate."' ";
		$qty = $this->db->query("select * from tbl_sale where empId='".$empId."' and saleDate>='".$fromDate."' and saleDate<='".$toDate."' ")->num_rows();
		$price =  $this->db->query("select sum(st.price) as p from tbl_sale sa inner join tbl_sku_master st on st.skuNo=sa.skuNo where sa.empId='".$empId."' and sa.saleDate>='".$fromDate."' and sa.saleDate<='".$toDate."' ")->row();

		$res = array();
		$res['response'] = 'success';
		$res['fromDate'] = $fromDate;
		$res['toDate'] = $toDate;
		$res['totQtySold'] = $qty;
		$res['totSale'] = $price->p;

		echo json_encode($res);
	}
	
	function add_mis()
	{
		$stock = array();
		$stock['empId'] = trim($this->input->post('empId'));

	}

	function get_escalation_type()
	{
		echo json_encode($this->stock_model->get_escalation_type());
	}
}

