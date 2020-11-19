<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class recruitment_model extends parent_model {
 
    function __construct() {
        parent::__construct();
 
    }
	function clientsList()
	{

		$sql = "select * from ".TABLECLIENTS."   where 1=1 and status='1' Order By name ASC";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	function getState($id=FALSE)
	{
		if($id)
		{
		$addsql .=" AND region='".$id."'";
		}
		$sql="select State_Id, State_Name from ".TABLE_STATE." where status='1' ".$addsql." Order By State_Name ASC";
		return $result=$this->db->query($sql)->result_array();
		
	}
	function ajaxCity($sid=FALSE)
	{
		$sql="select cityId, cityName from ".TABLE_CITY." where State=".$sid." order by cityName ASC";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function ajaxBranch($sid=FALSE,$cid=FALSE)
	{
		$sql="select cityId, cityName from ".TABLE_BRANCH_MASTER." where state=".$sid." and clientId=".$cid." order by cityName ASC";
		$result=$this->db->query($sql)->result_array();
		return $result;
	}
	function get_recruitment_details($jobAllocationId='', $resultType='G')
	{
	// pre($this->session->all_userdata()); die;
		
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select *,tr.jobAllocationId as id,c.cityName as City,tbr.cityName as Branch,s.State_Name as State,r.name as Region,cl.name as Client ,des.name as Designation , (select count(*) from recruit_uploadmobile where jobCode = tr.jobCode) as invitationCount ,  (select count(*) from recruit_uploadmobile where isCandidateFill=1 and jobCode = tr.jobCode) as dataFill , (select count(*) from recruitment_assessment ra left join recruit_uploadmobile ru on ra.recruitment_id = ru.id where ru.jobCode = tr.jobCode and ra.interviewerDest=".HR_DESG." and ra.is_selected='Y' ) as hrShortlistCount , (select count(*) from recruitment_assessment ra left join recruit_uploadmobile ru on ra.recruitment_id = ru.id where ru.jobCode = tr.jobCode and ra.interviewerDest=".PM_DESG." and ra.is_selected='Y' ) as pmShortlistCount   from ".TABLE_RECRUITMENT." tr
		LEFT JOIN ".TABLE_CITY." c on tr.CityId=c.cityId
		LEFT JOIN ".TABLE_BRANCH_MASTER." tbr on tr.branchId=tbr.cityId
		LEFT JOIN ".TABLE_STATE." s on tr.state_id=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on tr.region_Id=r.id
		LEFT JOIN ".TABLECLIENTS." cl on tr.clientId=cl.id
		LEFT JOIN tbl_mst_designation des on des.id=tr.designationId
		where 1=1  ".$addsql."  ";
  		
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		//pre($result);die;
		return $result;
	}



	function rate() {
        return array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5');
    }
}