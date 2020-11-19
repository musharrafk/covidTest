<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class state_model extends parent_model {
	var $base_tbl = TABLE_STATE;
	var $u_column = 'State_Id';


	function get_details($resultType='G')
	{

		//$addSql = " and parent_id='0' ";
		$addSql = "";

		if($this->input->post('status')!='')
		{
			$addSql .= " and status='".$this->input->post('status')."' ";
		}
		else
		{
			$addSql .= " and status='1' ";
		}
		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		$sql = "select * from ".$this->base_tbl." where 1=1 ".$addSql;
		//echo $sql; exit;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}

		return $result;
	}

	
	function getState()
	{
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0'])
		{
		$addsql .=" AND region in(".$this->session->userdata('admin_region').")";
		}
		$sql="select State_Id, State_Name from ".TABLE_STATE." where (status='0' or status='1')  ".$addsql." Order By State_Name ASC";
		
		return $result=$this->db->query($sql)->result_array();
		
		}
	
	function getStateHolidayList()
	{
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0'])
		{
		$addsql .=" AND region in(".$this->session->userdata('admin_region').")";
		}
		$sql="select State_Id, State_Name from ".TABLE_STATE." where office_exist = 1 and (status='0' or status='1')  ".$addsql." Order By State_Name ASC";			
		return $result=$this->db->query($sql)->result_array();
			
	}

	function ajaxState($cid)
	{
	$sql="select State_Id, State_Name from ".TABLE_STATE." where Country=".$cid."";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function ajaxStateregeionwise($rid)
	{
	$sql="select State_Id, State_Name from ".TABLE_STATE." where region=".$rid."";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	
}