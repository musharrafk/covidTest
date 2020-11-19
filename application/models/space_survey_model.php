<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class space_survey_model extends parent_model {
	
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
        /**
         * created By suraj
         * @var [type]
         */
         // space survey list Create 
        $sql = "SELECT survey_name,id,DATE_FORMAT(endDate,'%d-%m-%Y') as endDate,DATE_FORMAT(startDate,'%d-%m-%Y') as startDate FROM space_survey_list WHERE 1=1".$addSql;
        // echo $sql;
        // die();
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
    /**
     * created By suraj
     * @var [type]
    */
    function get_feedback_list($resultType='G') {
        $addSql = "  ";
        $uId = $this->session->userdata('admin_id');
            
        if($this->input->post('filters')!='')   {   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);                          
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
        }     
        $sql = "SELECT space_survey_feedback.s_id,space_survey_feedback.emp_id,space_survey_feedback.id,
                space_survey_feedback.isCreated,
                concat(tbl_emp_master.empFname,' ',tbl_emp_master.empLname) as emp_name,
                space_survey_list.survey_name,space_survey_list.startDate,space_survey_list.endDate
                FROM space_survey_feedback
                LEFT JOIN tbl_emp_master ON tbl_emp_master.empId = space_survey_feedback.emp_id
                LEFT JOIN space_survey_list ON space_survey_list.id = space_survey_feedback.s_id
                WHERE 1=1".$addSql;
       
        if($resultType=='G')
        {
            $result = parent::result_grid_array($sql);
        }
        else
        {
            $result = $this->db->query($sql)->result_array();
        }
        return $result;
        
        // return $result;
    }
    // fetch the survey details
    function get_survey_list_details($id){
        $this->db->select("*",False);
        $this->db->from("space_survey_list");
        $this->db->where('id',$id);
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
        $query = $this->db->get("assign_users_to_survey");      
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
        $query = $this->db->get("space_survey_question_list");
    
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
        $query = $this->db->get("map_question_to_survey_space");
    
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            $result     =  array_column($result, 'q_id');
        } else {
            $result = array(); 
        }
      
        return $result;
    }  
    
    public function fetch_survey($id='')
    {
        $currDate = date("Y-m-d");
        
        $this->db->select("space_survey_list.*");
        $this->db->from("space_survey_list");
        if ($id !='') {
        $this->db->where('id',$id);
        }
        $this->db->where('startDate <=',$currDate);
        $this->db->where('endDate  >=',$currDate);        
        $this->db->order_by('id','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
       
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }else{
            return $result = array();
        }
        // return $result;
    }
    // fetch survey question
    function fetch_survey_question_list($sid){

        $this->db->select("sq.id,sq.question,sq.question_type,sqo.id as oid,sqo.option_value,sqo.answer");       
        $this->db->from("map_question_to_survey_space as mqts");
        $this->db->join("space_survey_question_list as sq","mqts.q_id = sq.id");
        $this->db->join("space_survey_question_option as sqo","sq.id = sqo.q_id",'LEFT');
        $this->db->where("mqts.s_id",$sid); 
        $query = $this->db->get();
      
        if($query->num_rows() > 0){       
            $result     =  $query->result_array();         
        } else {
            $result = array(); 
        }     
        return $result;



       /* $questionInfoList = [];
        $this->db->select("space_survey_list.*");
        $this->db->from("space_survey_list");
        $this->db->order_by('id','DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        // return $result;
        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $questionInfoList[] = [
                    'id'             => $value['id'],
                    'survey_name'    => $value['survey_name'],
                    'startDate'      => $value['startDate'],
                    'endDate'        => $value['endDate'],
                    'addedBy'        => $value['addedBy'],
                    'status'         => $value['status'],
                    'map_lists'      => count($this->map_lists($value['id'])) > 0 ? $this->map_lists($value['id']) : '',
                ];
            }
        }
        
        return $questionInfoList;*/
        /*$this->db->select("map_question_to_survey_space.*");
        $this->db->select("space_survey_question_list.*");
        $this->db->select("space_survey_question_option.*");*/
    }

    public function map_lists($s_id)
    {
        // return $s_id;
        $map_lists = [];
        $this->db->select('map_question_to_survey_space.*');
        $this->db->from('map_question_to_survey_space');
        $this->db->where('s_id',$s_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();

        }else{
            $result = array();
        }
        if (count($result) > 0) {
               foreach ($result as $key => $value) {
                $map_lists[] = [
                    'id'             => $value['id'],
                    's_id'           => $value['s_id'],
                    'q_id'           => $value['q_id'],
                    'question_lists' => count($this->question_lists($value['q_id'])) > 0 ? $this->question_lists($value['q_id']) : '',
                ];
            }
        }
        return $map_lists;
    }
    public function question_lists($q_id)
    {
        // return $q_id;
        $question_lists = [];
        $this->db->select('space_survey_question_list.*');
        $this->db->from('space_survey_question_list');
        $this->db->where('id',$q_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $question_lists[] = [
                    'id'             => $value['id'],
                    'question'       => $value['question'],
                    'question_type'  => $value['question_type'],
                    'option_lists'   => count($this->option_lists($value['id'])) > 0 ? $this->option_lists($value['id']) : '',
                ];
            }
        }
        return $question_lists;
    }

    public function option_lists($q_id)
    {
        $option_lists = [];
        $this->db->select('space_survey_question_option.*');
        $this->db->from('space_survey_question_option');
        $this->db->where('q_id',$q_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                $option_lists[] = [
                    'id'            => $value['id'],
                    'q_id'          => $value['q_id'],
                    'option_value'  => $value['option_value'],
                    'answer'        => $value['answer'],
                ];
            }
        }
        return $option_lists;
    } 

     // delete all survey question
     function delete_survey_question($id){       
        $this->db->where('s_id', $id); 
        $query = $this->db->delete("map_question_to_survey_space");        
        return 1;
    } 

    // fetch assign survey employee
    function delete_employee_of_survey($id){      
        $this->db->where('s_id', $id);
        $query = $this->db->delete("assign_users_to_survey");
        return 1;      
    }


    function assign_survey_list($resultType='G') {
        $addSql = "  ";
        $uId = $this->session->userdata('admin_id');
            
        if($this->input->post('filters')!='')   {   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);                          
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
         }       
        /**
         * created By suraj
         * @var [type]
         */
         // space survey list Create 
        $sql = "SELECT * FROM space_survey_list WHERE 1=1".$addSql;
        // echo $sql;
        // die();
        if($resultType=='G')
        {
            $result = parent::result_grid_array($sql);
        }
        else
        {
            $result = $this->db->query($sql)->result_array();
        }
        return $result;
        /*$addSql = "  ";
        $uId = $this->session->userdata('admin_id');
            
        if($this->input->post('filters')!='')	{   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);               			 
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
         }
        
        $sql = "SELECT  est.id,est.emp_id,est.survey_name,est.isCreated,est.addedBy,concat(emp.empFname,' ',emp.empLname) as empName,emp.empImage,d.name as department,de.name as designation,sf.id as f_id from assign_users_to_survey auts inner join employee_survey_list est 
                 on auts.s_id = est.id  left join  survey_feedback  sf  on est.id = sf.s_id inner join
                ".TABLE_EMP." emp on est.emp_id = emp.empId Left Join ".TABLE_DEPT." d on emp.empDept=d.id
                Left Join ".TABLE_MASTER_DESIGNATION." de on emp.empDesination=de.id
                where auts.emp_id = $uId". $addSql;
        
        if($resultType=='G')
        {
            $result = parent::result_grid_array($sql);
        }
        else
        {
            $result = $this->db->query($sql)->result_array();
        }
        return $result;*/
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
            
        $sql = "SELECT id,question,question_type,createdDate,status from space_survey_question_list
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
       $query = $this->db->get("space_survey_question_list");
    
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
        $query = $this->db->get("space_survey_question_option");
     
        if($query->num_rows() > 0){       
             $result     =  $query->result_array();
         } else {
             $result = array(); 
         }
         return $result;
    }

    function delete_question_option($id){
        $this->db->where('q_id', $id); 
        $query = $this->db->delete("space_survey_question_option");        
        return 1;
    }


    public function empFilledSurvey($empId)
    {
        $surveyData  = $this->fetch_survey();
       
        if (!empty($surveyData) && !empty($empId)) {
        $currentDate = date('Y-m-d');
        // $currentDate =date('Y-m-d', strtotime($currentDate));
        // return $currentDate;
        //echo $currentDate; // echos today! 
        $surveyDateBegin = date('Y-m-d', strtotime($surveyData['startDate']));
        // return $surveyDateBegin;
        $surveyDateEnd   = date('Y-m-d', strtotime($surveyData['endDate'])); 
        // return $surveyDateEnd;
        $activeSurvey    = $surveyData['status']; 
        // return $activeSurvey;
        // return $surveyData['id'];
       
        if (($currentDate >= $surveyDateBegin) && ($currentDate <= $surveyDateEnd) && ($activeSurvey == 1)){
            $this->db->where('emp_id', $empId); 
            $this->db->where('s_id',$surveyData['id']);
            $query = $this->db->get("space_survey_feedback");
        
            if($query->num_rows() > 0){  
                $loginID = $this->session->userdata('admin_id');
                $existsResponse = $this->existsStartRating($loginID,$surveyData['id']);
                if ($existsResponse == 200) {
                    return 203;     
                }
                return 201;
                 // $result     =  $query->result_array();
            } else {
                // return "500";
                return 200;
                 // $result = array(); 
            }
        }else{
            return "No found Space survey.";
        } 
        }else{
            return 500;
        }         // return $result;
    }

    public function insertRatingFeedBack($starFeedBack)
    {
        $res = $this->db->insert('space_star_survey_feedback',$starFeedBack);
        if ($res) {
            return $res;
        }else{
            return false;
        }
    }

    public function space_survey_feedback_answer_list($sid,$empId,$questionidlist)
    {
		$this->db->select("sfal.id,sfal.question_id,sfal.answer_id,sfal.answer_text");       
        $this->db->from("space_survey_feedback as sf");
        $this->db->join("space_survey_feedback_answer_list as sfal","sf.id = sfal.sf_id",'LEFT');     
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
    public function get_questions($question_id,$question_type)
    {
        $this->db->select("space_survey_list.*");
        $this->db->from("space_survey_list");
        $this->db->where("id",$question_id);
        $this->db->order_by('id','DESC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return $result[0];
        }else{
            return $result = array();
        }
    }

    public function existsStartRating($empId,$s_id)
    {
        $surveyData  = $this->fetch_survey();
        if (!empty($surveyData) && !empty($empId)) {
        $currentDate = date('Y-m-d');
        // $currentDate =date('Y-m-d', strtotime($currentDate));
        // return $currentDate;
        //echo $currentDate; // echos today! 
        $surveyDateBegin = date('Y-m-d', strtotime($surveyData['startDate']));
        // return $surveyDateBegin;
        $surveyDateEnd   = date('Y-m-d', strtotime($surveyData['endDate'])); 
        // return $surveyDateEnd;
        $activeSurvey    = $surveyData['status']; 
        // return $activeSurvey;
        // return $surveyData['id'];
        if (($currentDate >= $surveyDateBegin) && ($currentDate <= $surveyDateEnd) && ($activeSurvey == 1)){
            $this->db->where('emp_id', $empId); 
            $this->db->where('s_id',$surveyData['id']);
            $query = $this->db->get("space_star_survey_feedback");
         
            if($query->num_rows() > 0){  
                return 201;     
            } else {
                return 200; 
            }
        }else{
            return 202;
        } 
        }else{
            return 500;
        }  
    }

    public function existsfeedBack($emp_id,$s_id)
    {
        $this->db->where('emp_id', $emp_id); 
        $this->db->where('s_id',$s_id);
        $query = $this->db->get("space_survey_feedback");
    
        if($query->num_rows() > 0){ 
            return 1;
        }
            return 2;
    } 

    // fetch survey question
    function fetch_filled_survey_question_list_answer($sid){
        $this->db->select("sq.id,sq.question,sq.question_type,sqo.id as oid,sqo.option_value,sqo.answer");       
        $this->db->from("map_question_to_survey_space as mqts");
        $this->db->join("space_survey_question_list as sq","mqts.q_id = sq.id");
        $this->db->join("space_survey_question_option as sqo","sq.id = sqo.q_id",'LEFT');
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
        $this->db->from("space_survey_feedback as sf");
        $this->db->join("space_survey_feedback_answer_list as sfal","sf.id = sfal.sf_id",'LEFT');     
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
    
    function getEmployeeDataDetails($empId){	     
		$this->db->select("empId,concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,tem.empImage,tmd.name as department_name",false);
        $this->db->from('tbl_emp_master as tem');
        $this->db->join("tbl_mst_designation as tmd","tem.empDesination = tmd.id","LEFT");        
        $this->db->where('tem.empId',$empId);  
        $result = $this->db->get();
        $resultData = $result->row_array();
        return $resultData;		
    }

    function checkSurveySaveByEmployee($empId,$sid){
        $this->db->select("space_survey_feedback.*");
        $this->db->from("space_survey_feedback");
        $this->db->where("emp_id",$empId);
        $this->db->where('s_id',$sid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            return 0;
        } else {
            return 1;
        }
    }
   
    function fetchUserStarRatingToSurvey($empId,$sid){
        $this->db->select("space_survey_feedback.*");
        $this->db->from("space_survey_feedback");
        $this->db->where("emp_id",$empId);
        $this->db->where('s_id',$sid);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return result;
        } else {
            return array();
        }
    } 


}

?>