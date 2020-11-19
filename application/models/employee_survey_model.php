<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class employee_survey_model extends parent_model {
	
    function fetch_survey_list($resultType='G') {
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
        
        
        $sql = "SELECT est.id,est.emp_id,est.survey_name,est.isCreated,est.addedBy,est.startDate,est.endDate,est.status,GROUP_CONCAT(concat(emp.empFname,' ',emp.empLname)) as empName,d.name as department,de.name as desination,GET_TOTAL_EMPLOYEE_OF_SURVEY(est.id) as total_emp,GET_TOTAL_PARTICIPANTS_OF_SURVEY(est.id) as total_participant,GET_TOTAL_COMPLETED_SURVEY(est.id) as total_completed,GET_TOTAL_QUESTION_IN_SURVEY(est.id) as total_question from employee_survey_list est left join 
                survey_of_employee  as soe on est.id= soe.s_id  left join ".TABLE_EMP." emp on soe.emp_id = emp.empId Left Join ".TABLE_DEPT." d on emp.empDept=d.id
                Left Join ".TABLE_MASTER_DESIGNATION." de on emp.empDesination=de.id
                where 1=1 group by soe.s_id ".$addSql;
       
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
    
    // fetch the survey details
    function get_survey_list_details($id){
        $this->db->select("esl.*,tem.empImage,concat(tem.empFname,' ',tem.empLname) as empName,tmd.name as designation",False);
        $this->db->from("employee_survey_list as esl");
        $this->db->join("tbl_emp_master as tem",'esl.emp_id = tem.empId','LEFT');       
        $this->db->join("tbl_mst_designation as tmd",'tem.empDesination = tmd.id','LEFT'); 
        $this->db->where('esl.id',$id);
        $query = $this->db->get();	 
        if($query->num_rows() > 0){       
            $result = $query->row_array(); 
        } else {
            $result = array(); 
        }
        return $result;
    }

    // fetch all employee list
    function fetch_all_employee_list(){
        $this->db->select("empId,concat(tem.empFname,' ',tem.empLname) as empName,empImage",false);
        $this->db->where('isActive',1);
        $query = $this->db->get("tbl_emp_master as tem");      
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
        } else {
            $result = array(); 
        }
        return $result;
    }
    
    // fetch all question list
    function fetch_all_question_list(){
        $this->db->select("question,type");
        $this->db->where('status',1);
        $query = $this->db->get("tbl_survey_question as tsq");      
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
        } else {
            $result = array(); 
        }
        return $result;
    }

    // fetch assign survey employee
    function fetch_all_assigned_employee_to_survey($id){
        $this->db->select("emp_id");
        $this->db->where('s_id', $id);
        $query = $this->db->get("survey_of_employee");      
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            $result     =  array_column($result, 'emp_id');
        } else {
            $result = array(); 
        }
        return $result; 
    }

    // fetch survey question
    function fetch_all_survey_question(){
        $this->db->select("id,question");
        $this->db->where('status',1); 
        $query = $this->db->get("survey_question_list");
    
        if($query->num_rows() > 0){       
            $result     =  $query->result_array();
        } else {
            $result = array(); 
        }
      
        return $result;
    } 

    // fetch survey question
    function fetch_selected_survey_question($sid){
        $this->db->select("q_id");
        $this->db->where('s_id',$sid); 
        $query = $this->db->get("map_question_to_survey");
    
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            $result     =  array_column($result, 'q_id');
        } else {
            $result = array(); 
        }
      
        return $result;
    }  
    
    // fetch survey question
    function fetch_survey_question_list($sid){
        $this->db->select("sq.id,sq.question,sq.question_type,sqo.id as oid,sqo.option_value,sqo.answer");       
        $this->db->from("map_question_to_survey as mqts");
        $this->db->join("survey_question_list as sq","mqts.q_id = sq.id");
        $this->db->join("survey_question_option as sqo","sq.id = sqo.q_id",'LEFT');
        $this->db->where("mqts.s_id",$sid); 
        $query = $this->db->get();
      
        if($query->num_rows() > 0){       
            $result     =  $query->result_array();         
        } else {
            $result = array(); 
        }     
        return $result;
    } 
    
	
	// fetch survey question
    function fetch_filled_survey_question_list_answer($sid){
        $this->db->select("sq.id,sq.question,sq.question_type,sqo.id as oid,sqo.option_value,sqo.answer");       
        $this->db->from("map_question_to_survey as mqts");
        $this->db->join("survey_question_list as sq","mqts.q_id = sq.id");
        $this->db->join("survey_question_option as sqo","sq.id = sqo.q_id",'LEFT');
        $this->db->where("mqts.s_id",$sid); 
        $query = $this->db->get();
       
        if($query->num_rows() > 0){       
            $result     =  $query->result_array();         
        } else {
            $result = array(); 
        }     
        return $result;
    } 
	 
	
	// fetch question answer survey
	function fetch_question_answer_list($sid,$empId,$questionidlist){
		//$questionidlist = implode(',',$questionidlist);
		$this->db->select("sfal.id,sfal.question_id,sfal.answer_id,sfal.answer_text");       
        $this->db->from("survey_feedback as sf");
        $this->db->join("survey_feedback_answer_list as sfal","sf.id = sfal.sf_id",'LEFT');     
        $this->db->where("sf.emp_id",$empId);
        $this->db->where("sf.s_id",$sid); 
        $this->db->where_in("sfal.question_id",$questionidlist); 		
        $query = $this->db->get();
      
        if($query->num_rows() > 0){       
            $result     =  $query->result_array();         
        } else {
            $result = array(); 
        }     
        return $result;
		
	}
	
	
     // delete all survey question
     function delete_survey_question($id){       
        $this->db->where('s_id', $id); 
        $query = $this->db->delete("map_question_to_survey");        
        return 1;
    } 

    // fetch assign survey employee
    function delete_employee_of_survey($id){      
        $this->db->where('s_id', $id);
        $query = $this->db->delete("assign_users_to_survey");
        return 1;      
    }

    // fetch assign survey employee
    function delete_employee_survey_data($id){      
        $this->db->where('s_id', $id);
        $query = $this->db->delete("survey_of_employee");
        return 1;      
    }
	
    function assign_survey_list($resultType='G') {
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
        
        $sql = "select auts.id,soe.id as se_id,soe.s_id,est.emp_id,est.survey_name,est.isCreated,est.addedBy,est.startDate,est.endDate,emp.empId as emp_id,concat(emp.empFname,' ',emp.empLname) as empName,emp.empImage,d.name as department,de.name as designation,sf.id as f_id
               from survey_of_employee soe 
               inner join assign_users_to_survey auts on  soe.emp_id = auts.survey_emp_id  inner join employee_survey_list est on soe.s_id = est.id
               left join ".TABLE_EMP." emp on soe.emp_id = emp.empId  Left Join ".TABLE_DEPT." d on emp.empDept=d.id
               left Join ".TABLE_MASTER_DESIGNATION." de on emp.empDesination=de.id left join survey_feedback sf on auts.emp_id = sf.emp_id and soe.id = sf.se_id 
               where auts.emp_id = $uId and est.status = 1 ". $addSql;
     
       /* $sql = "SELECT est.id,est.emp_id,est.survey_name,est.isCreated,est.addedBy,concat(emp.empFname,' ',emp.empLname) as empName,emp.empImage,d.name as department,de.name as designation,sf.id as f_id from assign_users_to_survey auts inner join employee_survey_list est 
                 on auts.s_id = est.id  left join  survey_feedback  sf  on est.id = sf.s_id and sf.emp_id =$uId inner join
                ".TABLE_EMP." emp on est.emp_id = emp.empId Left Join ".TABLE_DEPT." d on emp.empDept=d.id
                Left Join ".TABLE_MASTER_DESIGNATION." de on emp.empDesination=de.id
                where auts.emp_id = $uId". $addSql; */
        
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


    function fetch_question_list($resultType ='G'){
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
            
        $sql = "SELECT id,question,question_type,createdDate,status from survey_question_list
                where 1=1 ". $addSql ;
             
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

    function fetch_question_detail($id){
       $this->db->select("id,question,question_type,createdDate");   
       $this->db->where('id', $id); 
       $query = $this->db->get("survey_question_list");
    
       if($query->num_rows() > 0){       
            $result     =  $query->row_array();
        } else {
            $result = array(); 
        }
        return $result;
    }
   
    function fetch_question_option($id){
        $this->db->select("id,option_value,answer");   
        $this->db->where('q_id', $id); 
        $query = $this->db->get("survey_question_option");
     
        if($query->num_rows() > 0){       
             $result     =  $query->result_array();
         } else {
             $result = array(); 
         }
         return $result;
     }

    function delete_question_option($id){
        $this->db->where('q_id', $id); 
        $query = $this->db->delete("survey_question_option");        
        return 1;
    }

    function survey_fill_employee_list($id,$status){
        $this->db->select("sf.id as sf_id,soe.s_id as survey_id,tem.empId,tems.empId as survey_emp_id,concat(tem.empFname,' ',tem.empLname) as empName,concat(tems.empFname,' ',tems.empLname) as surveyEmpName",false);  
        $this->db->from("survey_of_employee as soe");  
        // $this->db->from("assign_users_to_survey as auts");  
        $this->db->join("assign_users_to_survey as auts","soe.emp_id = auts.survey_emp_id","LEFT"); 
        $this->db->join("tbl_emp_master as tems","soe.emp_id = tems.empId","LEFT");   
        $this->db->join("survey_feedback as sf","auts.emp_id = sf.emp_id and soe.id = sf.se_id","LEFT");
        $this->db->join("tbl_emp_master as tem","auts.emp_id = tem.empId","LEFT");       
        $this->db->where("soe.s_id",$id);
        if($status == 2){
            $this->db->where('sf.id IS NULL', null, false);
        } 
        $query = $this->db->get();
   
        if($query->num_rows() > 0){       
            $result     =  $query->result_array();         
        } else {
            $result = array(); 
        }     
        return $result; 
    }

    function getEmployeeDataDetails($empId){	     
		$this->db->select("empId,concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,tem.empImage,tmd.name as department_name",false);
        $this->db->from('tbl_emp_master as tem');
        $this->db->join("tbl_mst_designation as tmd","tem.empDesination = tmd.id","LEFT");        
        $this->db->where('tem.empId',$empId);  
        $result = $this->db->get();
        $resultData = $result->row_array();
        return $resultData;		
    }  

  function check_count_assign_emp_survey($sid){        
    $this->db->select("count(auts.id) as countEmp");
    $this->db->from("survey_of_employee as soe");    
    $this->db->join("assign_users_to_survey as auts","soe.emp_id = auts.survey_emp_id","LEFT");
    $this->db->where('soe.s_id',$sid);
    $result = $this->db->get();        
    $resultData = $result->row_array();
    return $resultData['countEmp'];	
  }
   
  function fetch_all_assign_employees_of_survey($sid){

    $this->db->select("tem.empId,esl.survey_name,esl.endDate,concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,tem.empEmailOffice,concat(tem1.empFname,' ',tem1.empMname,'',tem1.empLname) as surveyEmpName,tem1.empEmailOffice as surveyEmpEmailOffice",False);
    $this->db->from("survey_of_employee as soe");    
    $this->db->join("assign_users_to_survey as auts","soe.emp_id = auts.survey_emp_id","LEFT");
    $this->db->join("employee_survey_list as esl"," soe.s_id = esl.id","LEFT");  
    $this->db->join("tbl_emp_master as tem"," auts.emp_id = tem.empId","LEFT");
    $this->db->join("tbl_emp_master as tem1"," soe.emp_id = tem1.empId","LEFT"); 
    $this->db->where('soe.s_id',$sid);
    $result = $this->db->get();    
    $resultData = $result->result_array();
    return $resultData;		
  }
  function fetch_all_employee_survey_list($sid){
    $this->db->select("tem.empId,esl.survey_name,soe.s_id,soe.id,soe.emp_id as empSurveyId,esl.endDate,concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,
    tem.empEmailOffice,concat(tem1.empFname,' ',tem1.empMname,'',tem1.empLname) as surveyEmpName,count(auts.emp_id) as totalAssignEmp,
    tem1.empEmailOffice as surveyEmpEmailOffice,GET_TOTAL_COMPLETED_SURVEY_EMPLOYEE_WISE(soe.s_id,soe.emp_id) as totalCompleted",False);
    $this->db->from("survey_of_employee as soe");    
    $this->db->join("assign_users_to_survey as auts","soe.emp_id = auts.survey_emp_id","LEFT");
    $this->db->join("employee_survey_list as esl"," soe.s_id = esl.id","LEFT");  
    $this->db->join("tbl_emp_master as tem"," auts.emp_id = tem.empId","LEFT");
    $this->db->join("tbl_emp_master as tem1"," soe.emp_id = tem1.empId","LEFT");
    $this->db->where('soe.s_id',$sid);
    $this->db->group_by('soe.emp_id');
    $result = $this->db->get();    
    $resultData = $result->result_array();
    return $resultData;	
 }

 function fetch_report_data($sid){    
    $this->db->select("ql.id,ql.question,avg(sqo.option_value) as avgAnswer");       
    $this->db->from("survey_feedback_answer_list as sfal");
    $this->db->join("survey_question_option as sqo","sfal.question_id = sqo.q_id and sfal.answer_id = sqo.id");
    $this->db->join("survey_question_list as ql","sfal.question_id = ql.id");
    $this->db->where("sfal.se_id",$sid); 
    $this->db->group_by("sfal.question_id"); 
    $query = $this->db->get();
    
    if($query->num_rows() > 0){       
      $result     =  $query->result_array();         
    } else {
      $result = array(); 
    }     
    return $result;
 }
 
  // fetch all question list
function totalSurveyInQuestion($sid){
    $this->db->select("count(id) AS totalCount");    
    $this->db->where('s_id',$sid);
    $query = $this->db->get("map_question_to_survey as tsq");      
    if($query->num_rows() > 0){  
        $result = $query->row_array(); 
        $count  = $result['totalCount'];
    } else {
        $count = 0; 
    }
    return $count;
} 
 
function get_survey_taken_employees($sid){
    $this->db->select("soe.id,soe.emp_id,esl.survey_name,esl.startDate,esl.endDate,eg.grade,temrep.empId as reportingId,GROUP_CONCAT(Distinct concat(tem.empFname,' ',tem.empLname)) as surveyEmpName,GROUP_CONCAT(Distinct concat(temrep.empFname,' ',temrep.empLname)) as reportingManager,d.name as department,de.name as desination,c.cityName,
    count(auts.emp_id) as totalAssignEmp,GET_TOTAL_COMPLETED_SURVEY_EMPLOYEE_WISE(soe.s_id,soe.emp_id) as totalEmpCompleteSurvey,tem.empDOJ,tem.isActive",false);
    $this->db->from("survey_of_employee as soe");
    $this->db->join("assign_users_to_survey as auts","soe.emp_id = auts.survey_emp_id","LEFT");  
    $this->db->join("employee_survey_list as esl","soe.s_id = esl.id"); 
    $this->db->join("tbl_emp_master as tem","tem.empId = soe.emp_id");
    $this->db->join("employees_grade as eg","tem.empId = eg.empId");
    $this->db->join("tbl_emp_master as temrep","tem.reportingTo = temrep.empId");
    $this->db->join('tbl_mst_dept as d', "tem.empDept = d.id",'left');  
    $this->db->join('tbl_mst_designation as de', "tem.empDesination = de.id",'left');  
    $this->db->join('tbl_mst_city as c', " tem.jobLocation =  c.cityId",'left'); 
    $this->db->where('soe.s_id',$sid);
    $this->db->where('soe.status',1);
    $this->db->group_by('soe.emp_id');    
    $query = $this->db->get();     
    if($query->num_rows() > 0){  
        $result = $query->result_array(); 
     
    } else {
        $result = [];
    }
    return $result;
}
 
function get_survey_report($sid){
    $this->db->select("soe.emp_id,ql.id,ql.question,avg(sqo.option_value) as avgAnswer");       
    $this->db->from("survey_feedback_answer_list as sfal");
    $this->db->join("survey_question_option as sqo","sfal.question_id = sqo.q_id and sfal.answer_id = sqo.id");
    $this->db->join("survey_question_list as ql","sfal.question_id = ql.id");
    $this->db->from("survey_of_employee as soe",'soe.s_id = sf.id');
    $this->db->where("sfal.se_id",$sid); 
    $this->db->group_by("sfal.question_id"); 
    $query = $this->db->get();
    
    if($query->num_rows() > 0){       
      $result     =  $query->result_array();         
    } else {
      $result = array(); 
    }     
    return $result;
 }
 

}

?>