<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class flexi_model extends parent_model {
	var $base_tbl = TABLE_FLEXI;
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

 
// fetch flex initiate list
function get_flexi_initiate_list($resultType='G'){
	
	$addSql = "  ";
	$uId = $this->session->userdata('admin_id');
	
	if($this->input->post('filters')!='')	{   
			 $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray = get_object_vars($filterResultsJSON);               			 
			 if(!empty($filterArray['rules']))
			 {
			$addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
	}
	
	$sql = "SELECT * from ".TABLE_FLEXI_INITIATE." as fa where 1=1". $addSql;		
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

function get_initiate_flexi_by_id($table, $col='', $colVal=''){

	$addSql = '';
	if($col!='' and $colVal!=''){
			$addSql .= " where ".$col."='".$colVal."'";
	}
	 $sql = "select * from ".$table.$addSql;
	
	 return $this->db->query($sql)->result_array();	

}

// get current flex active detail
function get_flex_initiate_data(){
   
	$this->db->select('id,financial_year');
	$this->db->where('isActive',1);
	$this->db->order_by('id',desc);
	$this->db->limit('1');
	$query = $this->db->get(TABLE_FLEXI_INITIATE);
	 
	if($query->num_rows() > 0){
        $result = $query->row_array(); 
	} else {
        $result = array(); 
	}

	return  $result ;
}


function get_flexi_attributes_data($resultType='G'){      
		
	$addSql = "  ";
	$uId = $this->session->userdata('admin_id');
	
	if($this->input->post('filters')!='')	{   
			 $filterResultsJSON = json_decode($this->input->post('filters'));
			 $filterArray = get_object_vars($filterResultsJSON);               			 
			 if(!empty($filterArray['rules']))
			 {
			$addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			 }
	}
	
	$sql = "SELECT fa.*,concat(empFname,' ',empLname) as empName from ".TABLE_FLEXI." as fa inner join ".TABLE_EMP." as tem on fa.addedBy=tem.empId ". $addSql;		
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

function get_flexi_attr_data_by_id($table, $col='', $colVal=''){
	
	$addSql = '';
	if($col!='' and $colVal!=''){
			$addSql .= " where ".$col."='".$colVal."'";
	}
	$sql = "select * from ".$table.$addSql;
	return $this->db->query($sql)->result_array();	
}

// Get flexi employee budget
function get_flexi_emp_budget($resultType='G'){
	
	$addSql = "  ";
    $uId = $this->session->userdata('admin_id');
		
	if($this->input->post('filters')!='')	{   
		$filterResultsJSON = json_decode($this->input->post('filters'));
	    $filterArray = get_object_vars($filterResultsJSON);               			 
		if(!empty($filterArray['rules']))
		{
		    $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
	    }
	 }
	
	$sql = "select e.empId, concat(e.empFname,' ',e.empLname) as empName,e.empRole,e.empImage, e.empEmailPersonal, e.empEmailOffice, e.empMobile, e.empDOJ, e.empDept, e.empDesination, e.reportingTo, e.jobLocation, e.clients, e.projects, e.status, e.createdBy, e.candidateId, e.lastLogin, e.appointmentLetterSend, e.isCreated,d.name as departmentName,de.name as designationName,c.cityName from ".TABLE_EMP." e 
            Left Join ".TABLE_DEPT." d on e.empDept=d.id 
			Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
			LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
			where 1=1 ". $addSql.'group by e.empId'; 
	
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

// get flexi employee budget detail

function get_flexi_emp_budget_detail($empId){
   
		if($empId !='')
		{			
			$sql = "select * from ".TABLE_EMP_FLEXI_BUDGET." as fem  where fem.empId=$empId";
			return $this->db->query($sql)->result_array();
        }
}

function get_all_flex_attributes(){   
	$sql = "select * from ".TABLE_FLEXI." where status =1";
	return $this->db->query($sql)->result_array();
}

// Get flexi employee budget
function get_my_flexi_attr_budget($resultType='G',$actId){
	
	$addSql = "  ";
    $uId = $this->session->userdata('admin_id');
		
	if($this->input->post('filters')!='')	{   
		$filterResultsJSON = json_decode($this->input->post('filters'));
	    $filterArray = get_object_vars($filterResultsJSON);               			 
		if(!empty($filterArray['rules']))
		{
		    $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
	    }
	 }
	// total_flexi_expense_of_emp(".$uId.",".$actId.");
	$sql = "select e.empId, concat(e.empFname,' ',e.empLname) as empName , GROUP_CONCAT(fa.attributes_name) as flex_attributes,
	        check_emp_flexi_status(".$uId.",".$actId.") as checkStatus,0 as totalExpense
	        from ".TABLE_EMP_FLEXI_BUDGET." efb left join ".TABLE_EMP." e on efb.emp_id = e.empId 		
			left join ".TABLE_FLEXI." fa on efb.flex_id = fa.id 
			where  efb.emp_id =".$uId." ". $addSql. "group by efb.emp_id"; 
  
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


function get_emp_flexi_budget_detail($empId){
   
	if($empId !='')
	{			
		$sql = "select efb.*,fa.id as fid,fa.attributes_name from ".TABLE_FLEXI." as fa left join ".TABLE_EMP_FLEXI_BUDGET." as efb  on  fa.id = efb.flex_id and efb.emp_id=$empId";
     	return $this->db->query($sql)->result_array();
	}
}

function get_emp_flexi_attr_budget_list($empId,$id){
  
	if($empId !='')
	{
		$sql = "select efb.id,efb.set_amount,efb.limit_amount,fa.id as fid,fa.attributes_name,ecfb.status,eac.amount from employee_flex_budget as efb inner join flexi_attributes as fa on efb.flex_id = fa.id 
		left join emp_claim_flexi_budget as ecfb  on efb.emp_id = ecfb.emp_id and ecfb.act_id = ".$id." left join emp_claim_flex_attr_amt eac on ecfb.id=eac.claim_id and efb.flex_id = eac.flex_id 
		where efb.emp_id=".$empId;
	   
		return $this->db->query($sql)->result_array();
	}
}

function get_claim_attr_id($id,$empId){	
	if($id !='' && $empId != '') {			
		$sql = "SELECT ec.id,ec.reject_reason,eca.id as claim_attr_id,eca.flex_id,eca.amount FROM ".TABLE_EMP_CLAIM_BUDGET." as ec inner join ".TABLE_EMP_CLAIM_FLEX_ATTR_BUDGET." as eca on ec.id = eca.claim_id where ec.act_id =".$id." and emp_id = ".$empId;		
	
		return $this->db->query($sql)->result_array();
	}
}

function get_all_emp_flexi_claim_list($resultType='G',$activeId){

	$addSql = "  ";
    $uId = $this->session->userdata('admin_id');
		
	if($this->input->post('filters')!='')	{   
		$filterResultsJSON = json_decode($this->input->post('filters'));
	    $filterArray = get_object_vars($filterResultsJSON);               			 
		if(!empty($filterArray['rules']))
		{
		    $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
	    }
	 }
	

       $sql =  "SELECT ecf.id,tem.empId, concat(tem.empFname,' ',tem.empLname) as empName , '' as flex_attributes,d.name as departmentName,de.name as designationName,fi.financial_year,ecf.act_id,ecf.status ,get_emp_flexi_attributes(tem.empId) as attributes_list 
	           FROM `emp_claim_flexi_budget` ecf left join tbl_emp_master tem on ecf.emp_id = tem.empId left join flexi_initiate fi on ecf.act_id = fi.id  Left Join tbl_mst_dept d on tem.empDept=d.id Left Join tbl_mst_designation de on tem.empDesination=de.id
			   where 1=1 ".$addSql." order by ecf.id desc "; 
	  
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


function get_emp_claim_attr_list($empId,$id){
  
	if($empId !='')
	{
		$sql = "select efb.id,efb.set_amount,efb.limit_amount,fa.id as fid,fa.attributes_name,ecfb.id as claim_id,ecfb.reject_reason,eac.id as claim_attr_id,eac.amount,eac.status as claim_attr_status from employee_flex_budget as efb inner join flexi_attributes as fa on efb.flex_id = fa.id 
		left join emp_claim_flexi_budget as ecfb  on efb.emp_id = ecfb.emp_id  left join emp_claim_flex_attr_amt eac on ecfb.id=eac.claim_id and efb.flex_id = eac.flex_id 
		where efb.emp_id=".$empId." and ecfb.id=".$id;
	 
		return $this->db->query($sql)->result_array();
	}
}


function get_emp_claim_data($id,$empId){	
	if($id !='' && $empId != '') {			
		$sql = "SELECT ec.id,ec.reject_reason FROM ".TABLE_EMP_CLAIM_BUDGET." as ec where ec.id =".$id." and emp_id = ".$empId;		
	
		return $this->db->query($sql)->row_array();
	}
}


function get_emp_claim_data_edit($id,$empId){	
	if($id !='' && $empId != '') {			
		$sql = "SELECT ec.id,ec.reject_reason FROM ".TABLE_EMP_CLAIM_BUDGET." as ec where ec.act_id =".$id." and emp_id = ".$empId;		
	
		return $this->db->query($sql)->row_array();
	}
}


// get current activate flex details
function get_activate_flexi_details(){
   
	$this->db->select('id,financial_year,initiation_start_date,initiation_end_date');
	$this->db->where('isActive',1);
	$this->db->order_by('id',desc);
	$this->db->limit('1');
	$query = $this->db->get(TABLE_FLEXI_INITIATE);
	 
	if($query->num_rows() > 0){
        $result = $query->row_array(); 
	} else {
        $result = array(); 
	}
  
	return  $result ;
}

function get_emp_total_budget($empId){

	$this->db->select("total_flex_budget");
	$this->db->where('emp_id',$empId);	
	$query = $this->db->get('emp_flex_total_budget');
	 
	if($query->num_rows() > 0){
        $result = $query->row_array(); 
	} else {
        $result = array(); 
	}
  
	return  $result ;

}

function fetch_emp_expense_data($cid){

	$this->db->select("attributes_name,amount");
	$this->db->from('emp_claim_flex_attr_amt as ecfa');
	$this->db->join('flexi_attributes as fa', 'ecfa.flex_id = fa.id');
	$this->db->where('ecfa.claim_id',$cid);
	$query = $this->db->get();

	if($query->num_rows() > 0){
        $result = $query->result_array(); 
	} else {
        $result = array(); 
	}  
	return  $result ;
}


function get_flexi_report_data(){

	  $sql = "SELECT tem.empId, concat(tem.empTitle,' ',tem.empFname,' ',tem.empLname) as emp_name,tmd.name as designation_name,dept.name as
	          department_name,fi.financial_year,fa.attributes_name,ecfa.amount FROM `emp_claim_flexi_budget` ecfb left join 
			  tbl_emp_master tem  on  ecfb.emp_id = tem.empId  left join emp_claim_flex_attr_amt ecfa on ecfb.id = ecfa.claim_id
			  left join flexi_attributes fa on  ecfa.flex_id = fa.id left join flexi_initiate fi on ecfb.act_id = fi.id left join 
			  tbl_mst_designation as tmd on tem.empDesination = tmd.id left join tbl_mst_dept as dept on tem.empDept = dept.id";

      return $this->db->query($sql)->result_array();

}


}
?>