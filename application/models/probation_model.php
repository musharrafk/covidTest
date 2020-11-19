<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class probation_model extends parent_model {
   

  function decodeFilters($filters)
  {
    $sql = ' (';
    $objJson = json_decode($filters);
               
    foreach($objJson->{'rules'} as $rules)
    {
       if($rules->{'field'}!="")
       {
      if($rules->{'field'}=='e.empManagerName')
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


          $sql  .= "  e.empTitle like '%".$expKey[$k]."%'";
          $sql  .= " or e.empFname like '%".$expKey[$k]."%'";
          $sql  .= " or e.empLname like '%".$expKey[$k]."%'";

                            //$addSql .= " or ";
        }

        $sql .= ' ) ';
        $sql .= $objJson->{'groupOp'}.' ';
                                //////////
      }
      else
      {
      if ((count($objJson->{rules})>=2))
      {
      $addand=4;
      }
      else
      {
      $addand=5;
      }
      $flag="";
      //new  test 18-Aug-18
        foreach ($objJson->{'rules'} as $i=>$rules)
           {
        if($rules->{'field'}=='emp.empFname')
        {
          
          $sql .= ' ( ';
            $expKey = explode(' ',$rules->{'data'});
                for($k=0; $k<count($expKey); $k++)
                {
                  if($k>0)
                  {
                    $sql .= " or ";

                  }


                  $sql  .= "  emp.empTitle like '%".$expKey[$k]."%'";
                  $sql  .= " or emp.empFname like '%".$expKey[$k]."%'";
                  $sql  .= " or emp.empLname like '%".$expKey[$k]."%'";

                                    //$addSql .= " or ";
                }

              

           $sql .= ' ) ';
          $sql .= $objJson->{'groupOp'}.' ';
           unset($objJson->{'rules'}[$i]);
        }

        if($rules->{'field'} == 'emp_probation_confirmation.status')
        {
            if($rules->{'data'} == 'confirmed') {
          
            
             $sql  .= " ( emp_probation_confirmation.status = '".$rules->{'data'}."' or  es.empType = 1)";
             
            }
            else if($rules->{'data'} == 'pending') {
           
                // $sql  .= " (emp_probation_confirmation.status = '".$rules->{'data'}."' or es.empType = 2)";   
                $sql  .= " (emp_probation_confirmation.status = '".$rules->{'data'}."')  or (es.empType = 2 and es.probation_manager_status = 'pending' )";     
            }
             else {
           
                 $sql  .= " emp_probation_confirmation.status = '".$rules->{'data'}."'";        
            }

             
            $sql .= $objJson->{'groupOp'}.' ';
           
            unset($objJson->{'rules'}[$i]);
        }

        if($rules->{'field'} == 'epc.status')
        {
            if($rules->{'data'} == 'confirmed') {
          
            
             $sql  .= " ( epc.status = '".$rules->{'data'}."' or  es.empType = 1)";
             
            }
            else if($rules->{'data'} == 'pending') {
           
                 // $sql  .= " (epc.status = '".$rules->{'data'}."' and es.empType = 2) and (es.status != 'extend' or es.status != 'confirmed' or  es.empType = 2)";  
             // $sql  .= " (epc.status = '".$rules->{'data'}."')  or (es.empType = 2 and es.probation_manager_status!= 'confirmed' ) or (es.empType = 2 and es.probation_manager_status!= 'extend' )";
              $sql  .= " (epc.status = '".$rules->{'data'}."')  or (es.empType = 2 and es.probation_manager_status = 'pending' )";
                 // $sql  .= " (epc.status = '' or es.empType = 2 )  ";       
            }
             else {
           
                 $sql  .= " epc.status = '".$rules->{'data'}."'";        
            }

             
            $sql .= $objJson->{'groupOp'}.' ';
           
            unset($objJson->{'rules'}[$i]);
        }
 
      
      }
      
      //new  test 18-Aug-18
      //$aFilters[$rules->field] = $rules->data;
      foreach ($objJson->{'rules'} as $rules)
           {
         
        $sql .= $rules->{'field'}.' '; // field name
        $sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' '; // op, val
        $sql .= $objJson->{'groupOp'}.' '; // and, or 
        
        }
      }
      
    

    $sql = rtrim($sql, $objJson->{'groupOp'}.' ');
    return $sql.') ';
    }
    }
  }

	function get_probation_employees($resultType='G')
	{
		$empId = $this->session->userdata('admin_id'); 
		$today_date = date('Y-m-d');

    $addSql = "  "; 

        if($this->input->post('filters')!=''){   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);                      
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
         } 

		$sql = "SELECT DATE_ADD(emp.empDOJ, INTERVAL 6 MONTH) as probation_Date, DATE_FORMAT(emp.empDOJ,'%d-%b-%Y') as empDOJ, emp.empId as emp_id, emp.status as emp_status,CONCAT(emp.empFname, ' ', emp.empLname) as emp_name , emp.ReportingTo,DATE_FORMAT(emp_probation_confirmation.confirmation_date,'%d-%b-%Y') as confirmation_date ,emp_probation_confirmation.remarks,emp_probation_confirmation.status as status,es.empType as empType FROM tbl_emp_master as emp
		 LEFT JOIN emp_probation_confirmation ON emp.empId = emp_probation_confirmation.empId
     LEFT JOIN ".TABLE_SERVICE." es on emp.empId=es.empId

		  WHERE emp.status = 1 AND DATE_ADD(emp.empDOJ, INTERVAL 6 MONTH) <= '".$today_date."'   AND (es.empType = 2 or es.empType = 1) AND reportingTo = '".$empId."' ".$addSql." ";

      // print_r($sql);


          if($resultType=='G')
        {
            $result = parent::result_grid_array($sql);
        }
        else
        {
            $result = $this->db->query($sql)->result_array();
        }
        // print_r($result);
         return $result;

	}

  function get_emp_probation_data($resultType='G')
  {
      $addSql = "  "; 
      $today_date = date('Y-m-d');


        if($this->input->post('filters')!=''){   
            $filterResultsJSON = json_decode($this->input->post('filters'));
            $filterArray = get_object_vars($filterResultsJSON);                      
            if(!empty($filterArray['rules']))
            {
                $addSql .= " and ".self::decodeFilters($this->input->post('filters'));
            }
         }  

    $sql = "SELECT  DATE_FORMAT(emp.empDOJ,'%d-%b-%Y') as empDOJ, emp.empId as emp_id, emp.status as emp_status,CONCAT(emp.empFname, ' ', emp.empLname) as emp_name, DATE_FORMAT(DATE_ADD(emp.empDOJ, INTERVAL 6 MONTH),'%d-%b-%Y') as confirmation_completele_date, DATE_FORMAT(DATE_ADD(emp.empDOJ, INTERVAL 6 MONTH),'%Y-%m-%d') as probation_completele_date, d.name as empDept, emp.ReportingTo,DATE_FORMAT(epc.confirmation_date,'%d-%b-%Y') as confirmation_date ,epc.confirmation_date as manger_action_date ,DATE_FORMAT(epc.email_sent_date,'%d-%b-%Y') as hr_action_date ,epc.email_send_status,epc.remarks,epc.status  as status, es.empType as empType, es.probation_mail_status as cronjob_email_status, es.probation_manager_status as manager_status, CONCAT(e.empFname, ' ', e.empLname) as empManagerName FROM tbl_emp_master as emp 
     LEFT JOIN emp_probation_confirmation as epc ON emp.empId =  epc.empId
     LEFT JOIN tbl_emp_master as e ON e.empId = emp.reportingTo
     Left Join tbl_mst_dept d on emp.empDept = d.id
     LEFT JOIN ".TABLE_SERVICE." es on emp.empId=es.empId
   
      WHERE emp.status = 1 AND emp.empId > 100000 AND DATE_ADD(emp.empDOJ, INTERVAL 6 MONTH) <= '".$today_date."' AND (es.empType = 2 or es.empType = 1)  ".$addSql."   "; 
      
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

  function get_emp_probation_confirmation($empId)
  {
        $this->db->select("epc.*,emp.empDOJ,emp.empId as emp_id,emp.status as emp_status,CONCAT(emp.empFname, ' ', emp.empLname) as emp_name,emp.ReportingTo,epc.confirmation_date,epc.remarks,epc.status as probation_status",false);
        $this->db->from("emp_probation_confirmation epc");
        $this->db->join('tbl_emp_master emp','epc.empId = emp.empId','RIGHT');
        $this->db->where('emp.empId',$empId);
        $query = $this->db->get();   
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            $result = $result[0];
           
        } else {
            $result = array(); 
        }
        return $result;
  }

  function get_emp_detail($empid)
  {
          $this->db->select("empId,concat(tem.empFname,' ',tem.empLname) as empName,reportingTo,empDOJ,empEmailOffice",false);
          $this->db->where('isActive',1);
          $this->db->where('empId',$empid);
         $query = $this->db->get("tbl_emp_master as tem");      
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            $result = $result[0];
        } else {
            $result = array(); 
        }
   
        return $result;
  }

  function update_probation_status($empId,$actionDate,$managerStatus = '')
  {
      $sql = "select id, name from ".TABLE_LEAVEGROUP." where name ='Permanent'";
    $emp_leave_group = $this->db->query($sql)->result_array();
   $emp_leave_group = $emp_leave_group[0]['id'];
    
    $empTypesql ="select id from ".TABLE_EMPTYPE." where name ='Permanent'";
     $emp_type = $this->db->query($empTypesql)->result_array();
      $emp_type =  $emp_type[0]['id'];
      

     $this->db->set('empType',$emp_type);
     $this->db->set('leaveGroup',$emp_leave_group);
     $this->db->set('effectiveDate',$actionDate);
     if($managerStatus != ''){
       $this->db->set('probation_manager_status',$managerStatus);
     }
     $this->db->where('empId',$empId);
      $this->db->update('tbl_emp_service');

     // echo $this->db->last_query();
      if ($this->db->affected_rows() == true) {
          return true;
      }else{
          return false;
      }
  }

  function get_probation_cronjob()
  {
      $today_date = date('Y-m-d');

     $sql = "SELECT  DATE_ADD(emp.empDOJ, INTERVAL 6 MONTH) as probation_Date, DATE_FORMAT(emp.empDOJ,'%d-%b-%Y') as empDOJ, probation_manager_status as manager_status, epc.confirmation_date ,  emp.empId as emp_id, emp.status as emp_status,CONCAT(emp.empFname, ' ', emp.empLname) as emp_name,CONCAT(e.empFname, ' ', e.empLname) as empManagerName, e.empEmailOffice as empManagerEmail, emp.ReportingTo FROM tbl_emp_master as emp
      LEFT JOIN tbl_emp_master as e ON e.empId = emp.reportingTo
      LEFT JOIN emp_probation_confirmation as epc ON emp.empId =  epc.empId
      LEFT JOIN ".TABLE_SERVICE." es on emp.empId=es.empId
      WHERE emp.status = 1 AND emp.empId >= 20000720  AND DATE_ADD(emp.empDOJ, INTERVAL 6 MONTH) <= '".$today_date."'  AND es.empType = 2";
   
	   $result = $this->db->query($sql)->result_array();
    // echo $this->db->last_query();

       return $result;

  }

  function probation_questions()
  {
        $this->db->select('*');
        $this->db->where('status',1);
        $query = $this->db->get("emp_probation_questions");      
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
            
        } else {
            $result = array(); 
        }


     
        return $result;
   
        // return $result;
  }

  function emp_probation_remarks($empId){
        $this->db->select('*');
        $this->db->from("emp_probation_remarks as epr");
        // $this->db->join('emp_probation_remarks epr','epr.empId = emp.empId','RIGHT');
        $this->db->where('epr.emp_id',$empId);
        // $this->db->where('epr.question_id',$quesId);
        $query = $this->db->get();   
        if($query->num_rows() > 0){       
            $result = $query->result_array(); 
           
           
        } else {
            $result = array(); 
        }

         return $result;
  }

  function cronjob_probation_email_status($empId)
  {

    $this->db->set('probation_mail_status',1);
    $this->db->where('empId',$empId);
    $this->db->update('tbl_emp_service');
    if ($this->db->affected_rows() == true) {
        return true;
    }else{
        return false;
    }

  }



}