<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class jail_model extends parent_model {
	var $base_tbl = TABLE_GET_OUT_JAIL;
	var $u_column = 'id';

	function decodeFilters($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);
              //  pre($objJson);
		foreach($objJson->{'rules'} as $key=>$rules)
		{
		
			if($rules->{'field'}=='empName')
			{
                        //$sql .= 
                                /////////////
				$sql .= ' ( ';
				$expKey = explode(' ',$rules->{'data'});
				for($k=0; $k<count($expKey); $k++)
				{
					if($k>0)
					{
						$sql .= " or ";

					}


					$sql  .= "  o.empTitle like '%".$expKey[$k]."%'";
					$sql  .= " or o.empFname like '%".$expKey[$k]."%'";
					$sql  .= " or o.empLname like '%".$expKey[$k]."%'";

                            //$addSql .= " or ";
				}

				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
                                //////////
								
			}

			
			else
			{
			
			    if($rules->{'field'}=='o.joiningDate')
			   {		
				$start="";
				$end="";
				
				$sql .= ' ( ';
					$expKey = explode('-',$rules->{'data'});
					
					$start=(date('Y-m-d',strtotime($expKey[0])));
					$end=(date('Y-m-d',strtotime($expKey[1])));
					 $sql  .= "DATE_FORMAT(o.joiningDate,'%Y-%m-%d') >= '".$start."'" ;
					$sql  .= "and  DATE_FORMAT(o.joiningDate,'%Y-%m-%d') <= '".$end."'"; $sql .= ' ) ';
					
					$sql .= $objJson->{'groupOp'}.' ';
					unset($objJson->{'rules'}[$key]);
					
			     }
			  if($rules->{'field'}!='o.joiningDate')
			   {		
				
					
			    
			$sql .= $rules->{'field'}.' '; // field name
			$sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' '; // op, val
			$sql .= $objJson->{'groupOp'}.' '; // and, or
			 }
		}
	}
 
	$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
	return $sql.') ';
 }

 
function get_emp_jail_data($empId,$quarter,$year){
    $this->db->select('*');
	$this->db->where('emp_id',$empId);
	$this->db->where('quarter',$quarter);
	$this->db->where('year',$year);
	$query = $this->db->get(TABLE_GET_OUT_JAIL);
	 
	if($query->num_rows() > 0){
        $result = $query->result_array(); 
	} else {
        $result = array(); 
	}
	return $result;
  }

  function get_emp_jail_details($id){
    $this->db->select('*');
	$this->db->where('id',$id);
	$query = $this->db->get(TABLE_GET_OUT_JAIL);	 
	if($query->num_rows() > 0){       
        $result = $query->row_array(); 
	} else {
        $result = array(); 
	}
	return $result;
  }


  function get_emp_jail_list($empId){
    $this->db->select("gj.*,concat(tem.empFname,' ',tem.empLname) as empName,ted.name as designationName,tem.empImage",False);
    $this->db->from('get_out_jail as gj');
    $this->db->join('tbl_emp_master as tem','gj.emp_id  = tem.empId','LEFT');
    $this->db->join('tbl_mst_designation as ted','tem.empDesination  = ted.id','LEFT');
    $this->db->where('gj.emp_id',$empId);
	$query = $this->db->get();
	 
	if($query->num_rows() > 0){
        $result = $query->result_array(); 
	} else {
        $result = array(); 
	}
	return $result;
  }
   
  function get_emp_jail_list_for_co($resultType='G'){

    $addSql = "";
	$uId = $this->session->userdata('admin_id');
	
	if($this->input->post('filters')!='')	{   
			 $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray = get_object_vars($filterResultsJSON);               			 
			 if(!empty($filterArray['rules']))
			 {
			$addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
	}
	
	$sql = "SELECT gj.*,tem.empId,concat(tem.empFname, ' ', tem.empLname) as empName, ted.name as designationName, tem.empImage,tmd.name as departmentName FROM (`get_out_jail` as gj) LEFT JOIN `tbl_emp_master` as tem ON `gj`.`emp_id` = `tem`.`empId` LEFT JOIN `tbl_mst_designation` as ted ON `tem`.`empDesination` = `ted`.`id` LEFT JOIN tbl_mst_dept as tmd on tmd.id=tem.empDept where 1=1 ".$addSql;		
    
    
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
    

}
?>