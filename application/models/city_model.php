<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class city_model extends parent_model {
	var $base_tbl = TABLE_CITY;
	var $u_column = 'cityId';


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

	
	
	function getCity()
	{
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0'])
		$addsql .=" WHERE  r.id in(".$this->session->userdata('admin_region').")";
		 $sql="select c.cityId, c.cityName,s.State_Name as state,r.name as region from ".TABLE_CITY." c
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		 LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		 ".$addsql." order by c.cityName ASC";
		$result=$this->db->query($sql)->result_array();
		
		return $result;
    }
	

	
	function ajaxCity($sid)
	{
		$sql="select cityId, cityName from ".TABLE_CITY." where State=".$sid."  order by cityName ASC";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function ajaxCityp($sid)
	{
	$sql="select cityId, cityName from ".TABLE_CITY." where State=".$sid." order by cityName ASC";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function cityState(){
		$region = explode(',',$this->session->userdata('admin_region'));		
		if($region['0'])
		$addsql .=" WHERE  r.id in(".$this->session->userdata('admin_region').")";
		 $sql="select c.cityId, c.cityName,s.State_Name as state,r.name as region from ".TABLE_CITY." c
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		 LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		 ".$addsql." order by c.cityName ASC";
		$result=$this->db->query($sql)->result_array();
		
		// echo $this->db->last_query();
		 //exit;
		return $result;
	}
	
	// get city on the basis of state id
	function getcityByStateId($stateId){
	     //$region = explode(',',$this->session->userdata('admin_region'));		
		//if($region['0'])
		 $addsql .= 'WHERE c.state = '.$stateId;  
		 $sql="select c.cityId, c.cityName,s.State_Name as state,r.name as region from ".TABLE_CITY." c
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		 LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		 ".$addsql." order by c.cityName ASC";
		$result=$this->db->query($sql)->result_array();
		
		return $result;
	
	}
	
	//9-feb-18
	function ajaxBranch($sid,$cid)
	{
		$sql="select cityId, cityName from tbl_mst_branch where State=".$sid." and clientId=".$cid." order by cityName ASC";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	//9-feb-18
	
	
}