<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class advancesalary_model extends parent_model {
    
    
    function decodeFilters($filters)
	{	
	
		$sql = ' (';
		$objJson = json_decode($filters);
		
		foreach($objJson->{'rules'} as $key=>$rules)
		{	
			
			if($rules->{'field'}!="")
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
			
			if($rules->{'field'}=='empFname')
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


					$sql  .= " tem.empTitle like '%".$expKey[$k]."%'";
					$sql  .= " or tem.empFname like '%".$expKey[$k]."%'";
					$sql  .= " or tem.empMname like '%".$expKey[$k]."%'";
					$sql  .= " or tem.empLname like '%".$expKey[$k]."%'";

                            //$addSql .= " or ";
				}

				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
                                //////////
								
			}
            
            if($rules->{'field'}=='de.name')
			{				
			    $sql .= ' ( ';		
			
				$sql  .= "de.name like '%".filter_values($rules->{'data'})."%'";
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 

		    if($rules->{'field'}=='cityName')
			{
				
			$sql .= ' ( ';		
			
				$sql  .= "cityName like '%".filter_values($rules->{'data'})."%'";
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 
			

			if($rules->{'field'}!='empFname')
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
  
}

    function emp_apply_salary_list($resultType='G') {
        
    }

    function check_eligible_for_advance_salary($empId) {
         $this->db->select('empId,empDoj');
         $this->db->from('tbl_emp_master');
         $this->db->where('empId',$empId);
         $result = $this->db->get();
         $resultData = $result->row_array();
         return $resultData;
    }
   
    function fetch_employee_apply_advance_salary_list($empId) {
        $this->db->select('ads.*,tem.empDOJ',false);
        $this->db->from('advance_salary as ads');
        $this->db->join('tbl_emp_master as tem','ads.empId = tem.empId','left');
        $this->db->where('ads.empId',$empId);
        $this->db->where('tem.isActive',1);
        $this->db->order_by('id','desc');
        $result = $this->db->get();
        $resultData = $result->result_array();
        return $resultData;
   }

   function fetch_emp_advance_salary_to_manager($resultType='G',$filterType = '',$type = '') {
   
    $addSql = "  ";
    $uId    = $this->session->userdata('admin_id');     
    
    if($uId == 10000){
        $addSql .= " ";
    }
    else if($filterType == 'M'){          
        $addSql .= " and tem.reportingTo = ".$uId;
    }

    if($type == 3){
        $addSql .= " and ad.manager_status = 2 and teamhr_status = 2";
    }

    if($type == 2){
        $addSql .= " and ad.manager_status = 2";
    }

    if($this->input->post('filters')!=''){   
        $filterResultsJSON = json_decode($this->input->post('filters'));
        $filterArray = get_object_vars($filterResultsJSON);               			 
        if(!empty($filterArray['rules']))
        {
            $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
        }
     }    
   
      $sql =  "SELECT ad.id,ad.empId,ad.amount,ad.payment_method,ad.no_of_emi,remarks,ad.amount,ad.emp_status,ad.manager_status,ad.closing_balance,ad.closing_status,teamhr_status,payroll_status,concat(tem.empFname,' ',tem.empLname) as empName,
                CONCAT(UCASE(LEFT(c.cityName, 1)),LCASE(SUBSTRING(c.cityName, 2))) as cityName,d.name as departmentName,de.name as designationName,lr.reason,DATE_FORMAT(ast.created_date,'%d-%b-%Y') as sanctioned_date from advance_salary ad inner join tbl_emp_master tem on ad.empId = tem.empId 
                LEFT JOIN ".TABLE_CITY." c on tem.jobLocation=c.cityId
                Left Join ".TABLE_DEPT." d on tem.empDept=d.id
                Left Join ".TABLE_MASTER_DESIGNATION." de on tem.empDesination=de.id 
                Left Join advance_salary_transaction ast on ad.id = ast.adv_id and ast.team_status = 3
                Left Join loan_reason lr on ad.loan_purpose=lr.id                   
                where ad.status != 0 ".$addSql;
        

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

  function fetchEmpAdvanceSalaryDetails($id){
        $this->db->select('ads.*,concat(tem.empFname," ",tem.empLname) as empName,tem.empEmailOffice,tem.empDOJ,
        CONCAT(UCASE(LEFT(c.cityName, 1)),LCASE(SUBSTRING(c.cityName, 2))) as cityName,d.name as departmentName,de.name as designationName',false);
        $this->db->from('advance_salary as ads');
        $this->db->join('tbl_emp_master as tem','ads.empId = tem.empId','left');
        $this->db->join('tbl_mst_city as c','tem.jobLocation=c.cityId','left');
        $this->db->join('tbl_mst_dept as d','tem.empDept=d.id','left');
        $this->db->join('tbl_mst_designation as de','tem.empDesination=de.id','left');
        $this->db->where('ads.id',$id);     
        $this->db->where('ads.status',1);  
        $result = $this->db->get();
        $resultData = $result->row_array();
        return $resultData;
  }

  function fetchCompleteEmpAdvanceSalaryDetails($id){
    $this->db->select("ads.*,concat(tem.empFname,' ',tem.empLname) as empName,tem.empEmailOffice,tem.empDOJ,
    CONCAT(UCASE(LEFT(c.cityName, 1)),LCASE(SUBSTRING(c.cityName, 2))) as cityName,d.name as departmentName,de.name as designationName,DATE_FORMAT(ast.created_date,'%d-%b-%Y') as approval_date",false);
    $this->db->from('advance_salary as ads');
    $this->db->join('advance_salary_transaction as ast','ads.id = ast.adv_id and ast.team_status = 3','left');
    $this->db->join('tbl_emp_master as tem','ads.empId = tem.empId','left');
    $this->db->join('tbl_mst_city as c','tem.jobLocation=c.cityId','left');
    $this->db->join('tbl_mst_dept as d','tem.empDept=d.id','left');
    $this->db->join('tbl_mst_designation as de','tem.empDesination=de.id','left');
    $this->db->where('ads.id',$id);     
    $this->db->where('ads.status',1);  
    $result = $this->db->get();
    $resultData = $result->row_array();
    return $resultData;
}
  
  function getEmpExpData($empId){
      $this->db->select('tem.empDOJ');
      $this->db->from('tbl_emp_master as tem');
      $this->db->where('empId',$empId);
      $result = $this->db->get();
      $resultData = $result->row_array();
      return $resultData;
  }

   function check_last_apply_advance_salary($empId){
       
       $where = "((ads.manager_status  = 0 or ads.teamhr_status  = 0  or ads.payroll_status = 0 ) or (ads.manager_status  = 3 or ads.teamhr_status  = 3  or ads.payroll_status = 3)
                or (ads.manager_status  = 2 or ads.teamhr_status  = 2  or ads.payroll_status = 2))";
        $this->db->select('ads.id,ads.manager_status,ads.teamhr_status, ads.payroll_status,ast.created_date',false);
        $this->db->from('advance_salary as ads');
        $this->db->join('advance_salary_transaction as ast','ads.id = ast.adv_id','left');        
        $this->db->where('ads.empId',$empId);     
        $this->db->where('ads.status',1);
        //$this->db->where('ast.team_status',3); 
        $this->db->where($where); 
        $this->db->order_by('ast.id','desc');    
        $result = $this->db->get();
        $resultData = $result->row_array();
         
        return $resultData;
   }

   function checkEmployeeProbation($empId){
     $this->db->select('leaveGroup');
     $this->db->from('tbl_emp_service as tes');
     $this->db->where('empId',$empId);
     $result = $this->db->get();
     $resultData = $result->row_array();
  
     if(!empty($resultData)){
        return $resultData['leaveGroup'];
     } else {
        return 0;
     }     
   }

   function fetchEmpAdvanceSalaryHistory($empId){
    $this->db->select("ads.*,concat(tem.empFname,' ',tem.empLname) as empName,tem.empEmailOffice,tem.empDOJ,
    de.name as designationName,DATE_FORMAT(ast.created_date,'%d-%b-%Y') as approval_date,ast.status as payrollstatus",false);
    $this->db->from('advance_salary as ads');
    $this->db->join('advance_salary_transaction as ast','ads.id = ast.adv_id and ast.team_status = 3','left');
    $this->db->join('tbl_emp_master as tem','ads.empId = tem.empId','left');
    $this->db->join('tbl_mst_designation as de','tem.empDesination=de.id','left');
    $this->db->where('ads.empId',$empId);     
    $this->db->where('ads.payroll_status',2);  
    $result = $this->db->get();    
    $resultData = $result->result_array();
    return $resultData;
}

function getLoanReason(){
    $this->db->select('*');
    $this->db->from('loan_reason as lr');
    $this->db->where('status',1);
    $result = $this->db->get();
    $resultData = $result->result_array();
    return $resultData;
}

function getReportingBhuData($eid)
{
	$sql ="select e.empId , m.empId,concat(m.empTitle,' ',m.empFname,' ',m.empLname) as reportingTo,m.empEmailOffice from ".TABLE_EMP." e
	Left Join ".TABLE_EMP." m on e.reportingTo= m.empId
	WHERE e.empId=".$eid." ";	
	return $result = $this->db->query($sql)->row_array();
}

}

?>