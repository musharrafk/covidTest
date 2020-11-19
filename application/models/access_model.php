<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class access_model extends parent_model {
   
   
   /**
     *  function     : fetchState
     *  description  : fetch state data
     */
   function fetchCityState(){
         $this->db->select("tmc.*,tms.State_Id,tms.State_Name",False);
         $this->db->from('tbl_mst_city as tmc');
         $this->db->join('tbl_mst_state as tms','tmc.state=tms.State_Id');
         $this->db->where('tmc.status',1);
         $query =  $this->db->get();
         $result = $query->result_array();
		  return $result;         
   }

   /**
     *  function     : fetchState
     *  description  : fetch state wise employee access data
   */
   function fetchStateCityWiseEmpAccess($empId){
      $this->db->select('*');
      $this->db->from('employee_state_wise_permission');
      $this->db->where('empId',$empId);
      $this->db->where('type',1);
      $this->db->where('status',1);
      $query =  $this->db->get();
      $result = $query->result_array();
     return $result; 

   }

   /**
     *  function     : fetchStateWiseAttendanceAccess
     *  description  : fetch emloyee wise attendance access
   */
   function fetchStateCityWiseAttendanceAccess($empId){
      $this->db->select('*');
      $this->db->from('employee_state_wise_permission');
      $this->db->where('empId',$empId);
      $this->db->where('type',2);
      $this->db->where('status',1);
      $query =  $this->db->get();
      $result = $query->result_array();
     return $result; 

   }
   
   /**
     *  function     : deleteStateWiseEmp
     *  description  : delete state wise emp access
   */
   function deleteStateWiseEmp($empId){
      $this->db->where('empId', $empId);
      $this->db->where('type',1);
      $this->db->delete('employee_state_wise_permission'); 
   }

    /**
     *  function     : deleteStateWiseAttendance
     *  description  : delete state wise attendance access data
   */
  function deleteStateWiseAttendance($empId){
   $this->db->where('empId', $empId);
   $this->db->where('type',2);
   $this->db->delete('employee_state_wise_permission'); 
}

/**
  *  function     : fetchViewPermission
  *  description  : fetch view permission
*/
function fetchViewPermission($empId){
  $this->db->select('*');
  $this->db->from('permission_type');
  $this->db->where('empId',$empId);
  $this->db->where('type',1);
  $this->db->where('status',1);
  $query =  $this->db->get();
  $result = $query->row_array();
  return $result; 
}
 
/**
  *  function     : employee_data_for_permission
  *  description  : fetch all employee data to given permission
*/
function employee_data_for_permission($empId=0, $resultType='G',$status=false)
{
  $addsql = '';
 
  if($empId > 0)
  {
    $addsql .= " and e.empId=".$empId;
  }
  if($this->input->post('status')!='')
  {
    $addsql .= " and e.status='".$this->input->post('status')."' ";
  }
  else
  {
      //$addsql .= " and e.status='1' ";
  }
  if($status)
  {
    $addsql .= " and e.status!='".$status."' ";
  }

  if($this->input->post('filters')!='') // search filters
  {
    $addsql .= " and ".self::decodeFilters($this->input->post('filters'));
  }
  $sql = "select e.empMobile,e.joiningFor, e.empDesination, e.regionPermission, e.isActive, e.appointmentLetterSend,DATE_FORMAT(e.appointmentLetterSenddate,'%d-%b-%Y') appointmentLetterSenddate, e.empId,e.empEmailOffice,e.status,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,DATE_FORMAT(e.lastWorkingday,'%d-%b-%Y') lastWorkingday ,  DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,e.candidateId, e.empEmailPersonal, e.empEmailOffice , de.name as desination,em.empId as managerId, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as manager,em.empEmailOffice as manageremail,c.cityName as jobCityName,s.State_Name as jobStateName from ".TABLE_EMP." e
  Left Join ".TABLE_DEPT." d on e.empDept=d.id
  Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		  
  Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
  LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
  LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id 
  where 1=1 ".$addsql." and e.empId > 20000000 ";
  
  if($resultType=='G')
  {
    $result = parent::result_grid_array($sql);
  }
  else
  {
    $result = $this->db->query($sql)->result_array();
  }
  //pre($result);
  return $result;
}
	
}
?>