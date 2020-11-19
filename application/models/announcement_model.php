<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class announcement_model extends parent_model {
	
    function fetch_announcement_list($resultType='G') {
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
        
    
        /*$sql =  "SELECT ta.id,ta.heading,SUBSTRING( ta.content, 1, 80) as content, ta.type, ta.status, ta.isCreated, concat(emp.empFname,' ',emp.empLname) as empName from ".TABLE_ANNOUNCEMENT." ta inner join 
                ".TABLE_EMP." emp on ta.addedBy = emp.empId  where ta.addedBy= $uId ".$addSql; */
       
        $sql =  "SELECT *
                   FROM (
                    SELECT ta.id,ta.heading,SUBSTRING( ta.content, 1, 80) as content, ta.type, ta.status, ta.isCreated, concat(emp.empFname,' ',emp.empLname) as empName from ".TABLE_ANNOUNCEMENT." ta inner join 
                    ".TABLE_EMP." emp on ta.addedBy = emp.empId  where ta.status !=0 and ta.addedBy= ".$uId."
                    UNION ALL
                    SELECT ta.id,ta.heading,SUBSTRING( ta.content, 1, 80) as content, ta.type, ta.status, ta.isCreated, concat(emp.empFname,' ',emp.empLname) as empName from dept_news ta inner join 
                    ".TABLE_EMP." emp on ta.addedBy = emp.empId  where ta.status !=0 and ta.addedBy=".$uId."
                    ) AS Ann where 1=1 ". $addSql ;
             
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
    

    function get_employee_department_details($empId)
	{
		$sql = "select dept.* from ".TABLE_EMP." e left join ".TABLE_DEPT." dept on e.empDept= dept.id where e.empId=$empId";
      
        $result = $this->db->query($sql)->result_array();
		return $result = $result['0'];
	}

    function get_announcement_details($table, $col='', $colVal='')
	{
		$addSql = '';

		if($col!='' and $colVal!='')
		{
			$addSql .= " where ".$col."='".$colVal."'";
		}
		$sql = "select * from ".$table.$addSql;
		return $this->db->query($sql)->result_array();
	}
    
    // common for api and web both
    function get_news_details($table, $col='', $colVal=''){
        $addSql = '';

		if($col!='' and $colVal!='')
		{
			$addSql .= " where ".$col."='".$colVal."'";
		}
		$sql = "select id as news_id,heading as newsheading,content as newscontent,type,status from ".$table.$addSql;
		return $this->db->query($sql)->result_array();
    }


    function get_current_birthday(){                  
          
        $sql="SELECT tem.empId,concat(tem.empFname,' ',tem.empLname) as empName ,tem.empImage,DATE_FORMAT(tep.empDOBactual, '%d %M') as empDOBactual,tmd.name as departmentName,desig.name as designationName FROM `tbl_emp_master` as tem inner join tbl_emp_personal as tep on tem.empId = tep.empId
              left join tbl_mst_designation as desig on tem.empDesination = desig.id left join tbl_mst_dept as tmd on tem.empDept = tmd.id where  tem.isActive='1' and DATE_FORMAT(tep.empDOBactual,'%m-%d') = DATE_FORMAT(CURDATE(),'%m-%d')
              UNION
              SELECT tem.empId,concat(tem.empFname,' ',tem.empLname) as empName ,tem.empImage,DATE_FORMAT(tem.empDOBactual, '%d %M') as empDOBactual,'' as departmentName,  '' as designationName FROM `tbl_emp_brand` as tem 
              where  tem.isActive='1' and DATE_FORMAT(tem.empDOBactual,'%m-%d') = DATE_FORMAT(CURDATE(),'%m-%d')";
        $result  = $this->db->query($sql)->result_array();		   
        return $result;
       
    }

    // get upcomming birthday details
	 function get_upcomming_birthday($timePeriod){
        $sql=" select * from(
            (SELECT tem.empId,concat(tem.empFname,' ',tem.empLname) as empName,tem.empImage,DATE_FORMAT(tep.empDOBactual, '%d %M') as empDOBactual,tmd.name as departmentName,desig.name as designationName,
             tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR AS currbirthday,
             tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 1 YEAR AS nextbirthday
             FROM tbl_emp_personal as
             tep inner join tbl_emp_master as tem on tem.empId = tep.empId left join tbl_mst_dept as tmd on tem.empDept = tmd.id
             left join tbl_mst_designation as desig on tem.empDesination = desig.id where tep.empDOBactual != '0000-00-00' and tem.isActive = '1'  ORDER BY CASE
             WHEN currbirthday >= CURRENT_TIMESTAMP THEN currbirthday
             ELSE nextbirthday 
             END 
             limit $timePeriod  )             
             
             UNION

             (SELECT tem.empId,concat(tem.empFname,' ',tem.empLname) as empName,tem.empImage,DATE_FORMAT(tem.empDOBactual, '%d %M') as empDOBactual,'' as departmentName,'' as designationName,
             tem.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tem.empDOBactual)) + 0 YEAR AS currbirthday,
             tem.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tem.empDOBactual)) + 1 YEAR AS nextbirthday
             FROM tbl_emp_brand as tem where tem.empDOBactual != '0000-00-00' and tem.isActive = '1'  ORDER BY CASE
             WHEN currbirthday >= CURRENT_TIMESTAMP THEN currbirthday
             ELSE nextbirthday 
             END 
             limit $timePeriod) ) t1
             ORDER BY CASE
             WHEN t1.currbirthday >= CURRENT_TIMESTAMP THEN currbirthday
             ELSE t1.nextbirthday 
             END               
             ";
          //echo  $sql;die;
        $result  = $this->db->query($sql)->result_array();		   
        return $result;
    }



    function get_current_anniversary(){       
       
              $sql=" select tem.empImage, tem.empId, concat(tem.empFname, ' ',tem.empLname) as empName,tem.empEmailOffice,tem.empImage,tem.empDOJ,tmd.name as departmentName,desig.name as designationName,
                     YEAR(CURDATE()) - YEAR(tem.empDOJ) as count_years
                     from tbl_emp_master as tem left join tbl_mst_designation as desig on tem.empDesination = desig.id left join tbl_mst_dept as tmd on tem.empDept = tmd.id
                     where tem.isActive='1' and DATE_FORMAT(tem.empDOJ,'%m-%d') =  DATE_FORMAT(CURDATE(),'%m-%d')
                     UNION
                     select tem.empImage, tem.empId, concat(tem.empFname, ' ',tem.empLname) as empName,tem.empEmailOffice,tem.empImage,tem.empDOJ,'' as departmentName,'' as designationName,
                     YEAR(CURDATE()) - YEAR(tem.empDOJ) as count_years
                     from tbl_emp_brand as tem 
                     where tem.isActive='1' and DATE_FORMAT(tem.empDOJ,'%m-%d') =  DATE_FORMAT(CURDATE(),'%m-%d')";

              $result=$this->db->query($sql)->result_array();
              return $result;
    }

    function get_latest_co_video(){                  
        $result = array();
        $this->db->select('emp_id,video_url,heading,content,status');
        $this->db->from('co_videos as co');
        $query =  $this->db->get();

        if($query->num_rows() > 0){
              $result  = $query->result_array();		  
        } 
        return $result;
    }
            
    function get_latest_announcement(){ 
           $result = array();
           $this->db->select('emp_id,heading,content,video,image,type');
           $this->db->from('announcement as ann');
           $query =  $this->db->get();

           if($query->num_rows() > 0){
                 $result  = $query->result_array();		  
           } 
           return $result;
    }

    function get_anouncement_post($type ='',$postId ='',$offset = ''){
		  $empId = $this->session->userdata('admin_id');
		  $this->db->select("SQL_CALC_FOUND_ROWS ann.id,ann.heading as value,ann.content,ann.type,ann.status,ann.isCreated,ann.image_path,anl.emp_id,anl.is_like,
		  anl.emoji_content,GET_JSON_ARRAY_OF_ANNOUNCEMENT_COMMENTS(ann.id,'".$empId."') as comments,GET_TOTAL_COMMENT_IN_ANNOUNCEMENT(ann.id) as total_comment,GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."') as emp_name,dept.name as departmentName,dept.image",FALSE);
          $this->db->from('announcement as ann');
          $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
          $this->db->join('tbl_mst_dept as dept', "ann.dept_id = dept.id",'left');
		
        if(isset($type) && $type !=''){
            $this->db->where('ann.type',$type);
        }
        if(isset($postId) && $postId !=''){
            $this->db->where('ann.id',$postId);
        }
		$this->db->where('ann.status',1);
        $this->db->order_by('ann.isCreated','desc');
       
        $this->db->limit(10, $offset);
        
         $result = $this->db->get();
        
        $totalrows = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $resultData = $result->result_array();
        if($totalrows > 0){
             $resultData['total_rows'] = $totalrows;
        }
        return $resultData;
    }
    
	function get_anouncement_post_like($annId){        
          
		  $empId = $this->session->userdata('admin_id');
		  $this->db->select("ann.id,ann.heading,ann.content,ann.type,ann.status,ann.isCreated,anl.emp_id,anl.is_like,
		  anl.emoji_content,GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."') as emp_name",FALSE);
          $this->db->from('announcement as ann');
          $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
		
          $this->db->where('ann.id',$annId);

          $result = $this->db->get();
      
          $resultData = $result->row_array();
		  return $resultData;
    }
	
	
	
    function get_co_videos(){        
        $this->db->select('id,heading,content,type,status,isCreated,image_path');       
        $this->db->order_by('id','desc');
        $this->db->where('type',3);
        $this->db->like('content','<video');
        $this->db->limit(1);
        $result = $this->db->get(TABLE_ANNOUNCEMENT); 
        $resultData = $result->row_array();       
		return $resultData;
    }
     
	 
	 function get_co_video_list(){
	    $this->db->select('id,heading,content,type,status,isCreated,image_path');       
        $this->db->order_by('isCreated','desc');
        $this->db->where('type',3);
        $this->db->like('content','<video');
        $result = $this->db->get(TABLE_ANNOUNCEMENT);
      
        $resultData = $result->result_array();
		return $resultData;	  
	 }
     
     // use for api and web both
	 function check_like_exist($empId,$announcetId){
	    $this->db->select('id');       
        $this->db->where('emp_id',$empId);
		 $this->db->where('announcement_id',$announcetId);
        $result = $this->db->get('announcement_like');
      
         $resultData = $result->row_array();
		 return $resultData;	 
	 }
	 
  /*************************** API ******************/	
  
    function getLatestAnnouncement($empId ='',$type = ''){       
  
        $resultData = array();
        $this->db->select("ann.id as announcementId,ann.heading,ann.content,ann.type,ann.status,ann.isCreated,ann.image_path as video_banner,IFNULL(anl.emp_id,'') as emp_id ,IFNULL(anl.is_like,'') as is_like,
        GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_TOTAL_COMMENT_IN_ANNOUNCEMENT(ann.id) as total_comment,IFNULL(GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."'),'')  as like_emp_list,dept.name as departmentName,concat(dept.image,'') as dept_image",FALSE);
        $this->db->from('announcement as ann');
        $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
        $this->db->join('tbl_mst_dept as dept', "ann.dept_id = dept.id",'left');
      
       if(isset($type) && $type !='' &&  $type != 4){
          $this->db->where('ann.type',$type);
        }
       $this->db->where('ann.status',1);
       $this->db->order_by('ann.id','desc');
       $this->db->limit(1);
       
       $result = $this->db->get();
      
       $resultData = $result->row_array();       
       return $resultData; 
    }


    function getAllAnnouncementList($empId,$type,$offset){       
        $offset = $offset - 10;
        $resultData = array();
        $respdata   = array();
        $this->db->select("SQL_CALC_FOUND_ROWS ann.id,ann.heading,ann.content,ann.type,ann.status,ann.isCreated,ann.image_path as video_banner,anl.emp_id,anl.is_like,
        GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_TOTAL_COMMENT_IN_ANNOUNCEMENT(ann.id) as total_comment,GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."') as like_emp_list,dept.name as departmentName,dept.image as dept_image",FALSE);
        $this->db->from('announcement as ann');
        $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
        $this->db->join('tbl_mst_dept as dept', "ann.dept_id = dept.id",'left');
        if($type != 0 && $type != 4){
            $this->db->where('ann.dept_id',$type);  
        } 
        $this->db->where('ann.status',1);  
        $this->db->order_by('ann.isCreated','desc');        
        $this->db->limit(10,$offset);  
        $result = $this->db->get();        
        $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $respdata['resultData'] = $result->result_array();
        return $respdata;
    }
    
	
	function getAllTeamHrAnnouncementList($empId,$type,$offset){
        $offset = $offset - 10;
        $resultData = array();
        $respdata   = array();
        $this->db->select("SQL_CALC_FOUND_ROWS ann.id,ann.heading,ann.content,ann.type,ann.status,ann.isCreated,ann.image_path as video_banner,anl.emp_id,anl.is_like,
        GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_TOTAL_COMMENT_IN_ANNOUNCEMENT(ann.id) as total_comment,GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."') as like_emp_list,dept.name as departmentName,dept.image as dept_image",FALSE);
        $this->db->from('announcement as ann');
        $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
        $this->db->join('tbl_mst_dept as dept', "ann.dept_id = dept.id",'left');      
        $this->db->where('ann.type',$type);  
        $this->db->where('ann.status',1);		
        $this->db->order_by('ann.isCreated','desc');
        $this->db->limit(10,$offset);
        $result = $this->db->get();     
        $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $respdata['resultData'] = $result->result_array();
        return $respdata;
	}
    
    function getAllBrandAnnouncementList($empId,$type,$offset){
        $offset = $offset - 10;
        $resultData = array();
        $respdata   = array();
        $this->db->select("SQL_CALC_FOUND_ROWS ann.id,ann.heading,ann.content,ann.type,ann.status,ann.isCreated,ann.image_path as video_banner,anl.emp_id,anl.is_like,
        GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_TOTAL_COMMENT_IN_ANNOUNCEMENT(ann.id) as total_comment,GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."') as like_emp_list,dept.name as departmentName,dept.image as dept_image",FALSE);
        $this->db->from('announcement as ann');
        $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
        $this->db->join('tbl_mst_dept as dept', "ann.dept_id = dept.id",'left');      
        $this->db->where('ann.dept_id',$type);  
        $this->db->where('ann.status',1);		
        $this->db->order_by('ann.isCreated','desc');
        $this->db->limit(10,$offset);
        $result = $this->db->get();         
        $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $respdata['resultData'] = $result->result_array();
        return $respdata;
	}
	
	function getAllTeamItAnnouncementList($empId,$type,$offset){
        $offset = $offset - 10;
        $resultData = array();
        $respdata   = array();
        $this->db->select("SQL_CALC_FOUND_ROWS ann.id,ann.heading,ann.content,ann.type,ann.status,ann.isCreated,ann.image_path as video_banner,anl.emp_id,anl.is_like,
        GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_TOTAL_COMMENT_IN_ANNOUNCEMENT(ann.id) as total_comment,GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."') as like_emp_list,dept.name as departmentName,dept.image as dept_image",FALSE);
        $this->db->from('announcement as ann');
        $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
        $this->db->join('tbl_mst_dept as dept', "ann.dept_id = dept.id",'left');      
        $this->db->where('ann.type',$type);      
        $this->db->where('ann.status',1);
		$this->db->order_by('ann.isCreated','desc');
        $this->db->limit(10,$offset);
        $result = $this->db->get();     
        $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $respdata['resultData'] = $result->result_array();
        return $respdata;
	}

	function getAllCoVideoList($empId,$type,$offset){
        $offset = $offset - 10;
        $resultData = array();
        $respdata   = array();
        $this->db->select("SQL_CALC_FOUND_ROWS ann.id,ann.heading,ann.content,ann.type,ann.status,ann.isCreated,ann.image_path as video_banner,anl.emp_id,anl.is_like,
        GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_TOTAL_COMMENT_IN_ANNOUNCEMENT(ann.id) as total_comment,GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."') as like_emp_list,dept.name as departmentName,dept.image as dept_image",FALSE);
        $this->db->from('announcement as ann');
        $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
        $this->db->join('tbl_mst_dept as dept', "ann.dept_id = dept.id",'left');      
        $this->db->where('ann.type',$type);  
        $this->db->where('ann.status',1);		
        $this->db->order_by('ann.isCreated','desc');
        $this->db->limit(10,$offset);
        $result = $this->db->get();
        $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $respdata['resultData'] = $result->result_array();
        return $respdata;
    }

	
	function getCurrentbirthdayList($empId){                  
          $currDate =  date("Y");
        $sql="SELECT tem.empId,tem.empFname,tem.empLname,tem.empImage,DATE_FORMAT(tep.empDOBactual, '%d %M') as empDOBactual,tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR  as fullDob,tmd.name as departmentName,desig.name as designationName,bl.is_like,GET_TOTAL_LIKE_IN_BIRTHDAY(tem.empId,tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR,'".$empId."') as total_birthday_like,GET_EMPLOYEE_NAME_LIKE_BIRTHDAY(tem.empId,tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR,'".$empId."') as emp_name  FROM `tbl_emp_master` as tem inner join tbl_emp_personal as tep on tem.empId = tep.empId
              left join tbl_mst_designation as desig on tem.empDesination = desig.id left join tbl_mst_dept as tmd on tem.empDept = tmd.id
              left join birthday_like bl on bl.birthday_emp_id= tep.empId and bl.emp_id = ".$empId." and  bl.birth_date=tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR
              where  tem.isActive='1' and DATE_FORMAT(tep.empDOBactual,'%m-%d') = DATE_FORMAT(CURDATE(),'%m-%d')";
		 
        $result  = $this->db->query($sql)->result_array();		   
        return $result;
       
    }

    // get upcomming birthday details
	 function getUpcommingBirthdayList($empId,$timePeriod){
        $currDate = date("Y");
        $sql="SELECT tem.empId,tem.empFname,tem.empLname,tem.empImage,DATE_FORMAT(tep.empDOBactual, '%d %M') as empDOBactual,tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR  as fullDob,tmd.name as departmentName,desig.name as designationName,
               tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR AS currbirthday,
               tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 1 YEAR AS nextbirthday,
               bl.is_like,GET_TOTAL_LIKE_IN_BIRTHDAY(tem.empId,tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR,'".$empId."') as total_birthday_like,GET_EMPLOYEE_NAME_LIKE_BIRTHDAY(tem.empId,tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR,'".$empId."') as emp_name FROM tbl_emp_personal as
               tep inner join tbl_emp_master as tem on tem.empId = tep.empId   left join birthday_like bl on bl.birthday_emp_id= tep.empId and bl.emp_id = ".$empId." and  bl.birth_date=tep.empDOBactual + INTERVAL(YEAR(CURRENT_TIMESTAMP) - YEAR(tep.empDOBactual)) + 0 YEAR
                left join tbl_mst_dept as tmd on tem.empDept = tmd.id
               left join tbl_mst_designation as desig on tem.empDesination = desig.id where tep.empDOBactual != '0000-00-00' and tem.isActive = '1'  ORDER BY CASE
               WHEN currbirthday >= CURRENT_TIMESTAMP THEN currbirthday
               ELSE nextbirthday 
               END 
               limit $timePeriod";
          //echo  $sql;die;
        $result  = $this->db->query($sql)->result_array();		   
        return $result;
    }
    
	 function getCurrentAnniversaryList(){       
       
              $sql="select tem.empImage, tem.empId,tem.empFname,tem.empLname,tem.empEmailOffice,tem.empImage,DATE_FORMAT(tem.empDOJ, '%d %M') as empDOJ,tmd.name as departmentName,desig.name as designationName,
                 YEAR(CURDATE()) - YEAR(tem.empDOJ) as count_years
                 from tbl_emp_master as tem left join tbl_mst_designation as desig on tem.empDesination = desig.id left join tbl_mst_dept as tmd on tem.empDept = tmd.id
                 where tem.isActive='1' and DATE_FORMAT(tem.empDOJ,'%m-%d') =  DATE_FORMAT(CURDATE(),'%m-%d')";
        
              $result=$this->db->query($sql)->result_array();
              return $result;
    }


    function get_announcement_like_data($annId,$empId){        
        
        $this->db->select("ann.id as announcement_id,IF(anl.emp_id IS NULL, '', anl.emp_id) as emp_id ,IF(anl.is_like IS NULL, '', anl.is_like) as is_like,
        GET_TOTAL_LIKE_IN_ANNOUNCEMENT(ann.id,'".$empId."') as total_like,GET_EMPLOYEE_NAME_LIKE_POST(ann.id,'".$empId."') as emp_name",FALSE);
        $this->db->from('announcement as ann');
        $this->db->join('announcement_like as anl', "ann.id = anl.announcement_id and anl.emp_id = '".$empId."'",'left');
      
        $this->db->where('ann.id',$annId);

        $result = $this->db->get();
    
        $resultData = $result->row_array();
        return $resultData;
  }
  

  function get_birthday_like_data($birthEmpId,$empId,$birthDate){        
        
    $this->db->select("GET_TOTAL_LIKE_IN_BIRTHDAY('".$birthEmpId."','".$birthDate."','".$empId."') as total_like,GET_EMPLOYEE_NAME_LIKE_BIRTHDAY('".$birthEmpId."','".$birthDate."','".$empId."') as emp_name",FALSE);
    $this->db->from('birthday_like as bl');     
    $result = $this->db->get();
    $resultData = $result->row_array();

    $this->db->select("IF(bl.birthday_emp_id IS NULL, '', bl.birthday_emp_id) as birthday_emp_id, IF(bl.emp_id IS NULL, '', bl.emp_id) as emp_id ,IF(bl.is_like IS NULL, '', bl.is_like) as is_like",FALSE);
    $this->db->from('birthday_like as bl');
    $this->db->where('bl.birthday_emp_id',$birthEmpId);
    $this->db->where('bl.birth_date',$birthDate);
    $this->db->where('bl.emp_id',$empId);
    $resData    = $this->db->get();
    $likeData   = $resData->row_array();
    if(!empty($likeData)){
        $resultData['birthday_emp_id']  = $likeData['birthday_emp_id'];
        $resultData['emp_id']           = $likeData['emp_id'];
        $resultData['is_like']          = $likeData['is_like'];
    } else {
        $resultData['birthday_emp_id']  = $birthEmpId;
        $resultData['emp_id']           = '';
        $resultData['is_like']          = '';
    }
    return $resultData;
  }

  
  function get_employee_birthday_like_list($birthEmpId,$empId,$birthDate){
    $this->db->select("SQL_CALC_FOUND_ROWS concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,tem.empImage,bl.birthday_emp_id",FALSE);
    $this->db->from('birthday_like as bl');
    $this->db->join('tbl_emp_master as tem','bl.emp_id = tem.empId');
    $this->db->where('bl.birthday_emp_id',$birthEmpId);
    $this->db->where('bl.birth_date',$birthDate);
    $this->db->where('bl.emp_id != ',$empId);
    $result    = $this->db->get();    
    $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
    $respdata['resultData'] = $result->result_array();
    return $respdata;
  }


   // check birthday like exist
   function check_birthday_like_exist($birthEmpId,$empId,$birthDate){
        $this->db->select('id');       
        $this->db->where('birthday_emp_id',$birthEmpId);
        $this->db->where('emp_id',$empId);
        $this->db->where('birth_date',$birthDate);
        $result = $this->db->get('birthday_like');
    
        $resultData = $result->row_array();
        return $resultData;	 
    }

    // fetch all news of department
   function dept_news($empId){
        $this->db->select("dn.id,dn.heading,dn.content,tmd.name,dn.isCreated");
        $this->db->from('dept_news as dn');
        $this->db->join('tbl_mst_dept as tmd', "dn.dept_id = tmd.id");    
        $this->db->limit(50);
        if($empId){
          $this->db->where('dn.addedBy',$empId);
        }
        $this->db->where('status',1);
        $this->db->order_by('dn.id','desc');       
        $result = $this->db->get();
        $resultData = $result->result_array();
        return $resultData;	 
   }
   
   // fetch group new on the basis of id
   function group_news_detail($id){
        $this->db->select("dn.id,dn.heading,dn.content,tmd.name,dn.isCreated");
        $this->db->from('dept_news as dn');
        $this->db->join('tbl_mst_dept as tmd', "dn.dept_id = tmd.id");    
        $this->db->where('dn.id',$id);  
        $this->db->where('status',1);
        $result = $this->db->get();
        $resultData = $result->row_array();
        return $resultData;
   }

   /*************************** API  END******************/	

  // get All Device id of employee
  function get_all_emp_device_id(){
    $this->db->select("e.empId,tgu.deviceId");
    $this->db->from('tbl_emp_master as e');
    $this->db->join('tbl_gcm_users as tgu', "e.empId = tgu.empId");
    $this->db->where('e.isActive',1);
    $this->db->group_by('e.empId');
    $result = $this->db->get();
    $resultData = $result->result_array();
    return $resultData;	 
  }
  
  function getEmployeeDataDetails($empId){	     
		$this->db->select("empId,concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,tem.empImage",false);
        $this->db->from('tbl_emp_master as tem');        
        $this->db->where('empId',$empId);  
        $result = $this->db->get();
        $resultData = $result->row_array();
        return $resultData;		
  }  

/******************** announcement like and comment data ********************/
  function get_announcement_like_list($annId,$empId){        
        $resultData = array();
        $respdata   = array();
		$baseurl = base_url();
        $this->db->select("SQL_CALC_FOUND_ROWS anl.id,anl.announcement_id as announcementId,IF(anl.emp_id IS NULL, '', anl.emp_id) as emp_id ,IF(anl.is_like IS NULL, '', anl.is_like) as is_like,
         GET_TOTAL_LIKE_IN_ANNOUNCEMENT(anl.announcement_id,'".$empId."') as totalLike,concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,IF(tem.empImage != '',CONCAT('".$baseurl."','uploads/candidateDocument/empImage/',tem.empImage),CONCAT('".$baseurl."','ark_assets/images/default.jpg')) AS empImage",false);
        $this->db->from('announcement_like as anl'); 
        $this->db->join('tbl_emp_master as tem', "anl.emp_id = tem.empId",'left');		
        $this->db->where('anl.announcement_id',$annId);
        $this->db->where('anl.emp_id !=',$empId);
        $this->db->where('anl.emp_id !=',0);
        $result = $this->db->get();
     
        $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $respdata['resultData'] = $result->result_array();
		return $respdata;
  } 
  
  function get_announcement_comment_list($annId,$empId){
	    $resultData = array();
        $respdata   = array(); 
	    $baseurl = base_url();
        $this->db->select("SQL_CALC_FOUND_ROWS anc.id,anc.ann_id as announcementId,anc.comments,anc.createdDate,IF(cl.is_like IS NULL, '0', cl.is_like) as is_like,GET_TOTAL_LIKE_IN_COMMENT(anc.id,$empId) as total_like,
         concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,IF(tem.empImage != '',CONCAT('".$baseurl."','uploads/candidateDocument/empImage/',tem.empImage),CONCAT('".$baseurl."','ark_assets/images/default.jpg')) AS empImage",false);
        $this->db->from('announcement_comments as anc');
        $this->db->join('comment_like as cl', "anc.id=cl.comment_id and cl.emp_id = $empId",'left');	
		$this->db->join('tbl_emp_master as tem', "anc.emp_id = tem.empId",'left');		
        $this->db->where('anc.ann_id',$annId);
		$this->db->order_by('anc.id','desc');
		//$this->db->where('anc.emp_id !=',$empId);
        $result = $this->db->get();
		//echo $this->db->last_query();
        $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $respdata['resultData'] = $result->result_array();
        return $respdata;
  }
  
   function feth_all_announcement_comment($annId,$empId){
	   
	    $baseurl = base_url();
        $this->db->select("SQL_CALC_FOUND_ROWS anc.id,anc.ann_id as announcementId,anc.comments,anc.createdDate,IF(cl.is_like IS NULL, '3', cl.is_like) as is_like,
         concat(tem.empFname,' ',tem.empMname,'',tem.empLname) as empName,IF(tem.empImage != '',CONCAT('".$baseurl."','uploads/candidateDocument/empImage/',tem.empImage),CONCAT('".$baseurl."','ark_assets/images/default.jpg')) AS empImage",false);
        $this->db->from('announcement_comments as anc');
		$this->db->join('comment_like as cl', "anc.id=cl.comment_id and cl.emp_id = $empId",'left');
        $this->db->join('tbl_emp_master as tem', "anc.emp_id = tem.empId",'left');		
        $this->db->where('anc.ann_id',$annId);
	    $this->db->order_by('anc.id','asc');
		//$this->db->where('anc.emp_id !=',$empId);
        $result = $this->db->get();
     
        $resultData = $result->result_array();
        return $resultData;
  }
   
    // use for api and web both
	function check_comment_like_exist($empId,$commentId){
	    $this->db->select('id');       
        $this->db->where('emp_id',$empId);
		 $this->db->where('comment_id',$commentId);
        $result = $this->db->get('comment_like');
      
        $resultData = $result->row_array();
		return $resultData;	 
	 }
     
  
	function get_announcement_comment_like_data($commentId,$empId){        
       
        $this->db->select("ann.id,ann.ann_id as announcement_id,IF(anl.emp_id IS NULL, '', anl.emp_id) as emp_id ,IF(anl.is_like IS NULL, '0', anl.is_like) as is_like,
        GET_TOTAL_LIKE_IN_COMMENT(ann.id,'".$empId."') as total_like",FALSE);
        $this->db->from('announcement_comments as ann');
        $this->db->join('comment_like as anl', "ann.id = anl.comment_id and anl.emp_id = '".$empId."'",'left');      
        $this->db->where('ann.id',$commentId);

        $result = $this->db->get();
 
        $resultData = $result->row_array();
        return $resultData;
    }
     
	 // fetch all announcement of employee
	function getEmpAnnouncementList($deptId){ 
        $resultData = array();
        $respdata   = array();
        $this->db->select("SQL_CALC_FOUND_ROWS ann.id,ann.heading,ann.content,ann.type,ann.status,ann.isCreated,ann.image_path as video_banner,ann.emp_id,
        dept.name as departmentName,dept.image as dept_image",FALSE);
        $this->db->from('announcement as ann');
        $this->db->join('tbl_mst_dept as dept', "ann.dept_id = dept.id",'left');   
        $this->db->where('dept.id',$deptId);
        $this->db->where('ann.status',1);		
        $this->db->order_by('ann.isCreated','desc'); 
        $result = $this->db->get();    
		
        $respdata['totalrows']  = $this->db->query ( 'SELECT FOUND_ROWS() count;' )->row ()->count;
        $respdata['resultData'] = $result->result_array();
        return $respdata;
    }
	 
	function deleteAnnouncementData($id){
        $this->db->where('id', $id);
		$checkStatus = $this->db->update('announcement', array('status' => 0));
        return $checkStatus;
    }

    function deleteNewsData($id){
        $this->db->where('id', $id);
		$checkStatus = $this->db->update('dept_news', array('status' => 0));
        return $checkStatus;
    }
/********************* announcement like and comment data ************************/

 function changeStatusAnnNews($id,$type,$status){       
        if($type == 5){
            $this->db->where('id', $id);
            $checkStatus = $this->db->update('dept_news', array('status' => $status));
        } else {
            $this->db->where('id', $id);
            $checkStatus = $this->db->update('announcement', array('status' => $status));
        }    
        return $checkStatus;
    }


    function getAllTimeLineMenuList(){        
        
		$sql = "SELECT `tm`.*
				FROM (`timeline_menu` as tm)
				JOIN `announcement` as ann ON `tm`.`type` = `ann`.`dept_id` or tm.type = 0
				WHERE `tm`.`status` =  1
				GROUP BY `tm`.`type`
				ORDER BY CASE
							  WHEN tm.orderId = 1 THEN 1
							  WHEN tm.orderId = 2 THEN 2
							  WHEN tm.orderId = 3 THEN 3
							  WHEN tm.orderId = 4 THEN 4 
							   ELSE 5 END ASC,
					   tm.orderId desc";
		
		$resultData = $this->db->query($sql)->result_array();        
		return $resultData;	 
    }
}


?>