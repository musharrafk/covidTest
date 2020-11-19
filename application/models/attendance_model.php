<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	class attendance_model extends parent_model {
	var $base_tbl = TABLE_LEAVEGROUP;
	var $u_column = 'id';
	
	
    function decodeFilters($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);
			
		foreach($objJson->{'rules'} as $rules)
		{
		if($rules->{'field'}!="")
		   {
		   foreach ($objJson->{'rules'} as $i=>$rules)
		       {
			   if($rules->{'field'}=='e.empFname')
			{
			$sql .= ' ( ';
				$expKey = explode(' ',filter_values($rules->{'data'}));
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
		 /*   if( count($objJson->{'rules'})>1)
				 { */
				 
				$sql .= $objJson->{'groupOp'}.' ';
				/* } */
			unset($objJson->{'rules'}[$i]);
			}
			
			if($rules->{'field'}=='range.attendanceDatetime')
			{
			$start="";
			$end="";
			
			$sql .= ' ( ';
				$expKey = explode('-',filter_values($rules->{'data'}));			
				$start = filter_date((date('Y-m-d',strtotime($expKey[0]))));
				$end   = filter_date((date('Y-m-d',strtotime($expKey[1]))));
				 $sql  .= "DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') >= '".$start."'" ;
				$sql  .= "and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') <= '".$end."'"; $sql .= ' ) ';
				 /* if( count($objJson->{'rules'})>1)
				 { */
				$sql .= $objJson->{'groupOp'}.' ';
				/* } */
				unset($objJson->{'rules'}[$i]);
			}

            if($rules->{'field'}=='e.empFname')
			{ 
				$sql .= ' ( ';
				$expKey = explode(' ',$rules->{'data'});
				for($k=0; $k<count($expKey); $k++)
				{
					if($k>0)
					{
						$sql .= " or ";

					}
					$sql  .= " e.empTitle like '%".$expKey[$k]."%'";
					$sql  .= " or e.empFname like '%".$expKey[$k]."%'";
					$sql  .= " or e.empMname like '%".$expKey[$k]."%'";
					$sql  .= " or e.empLname like '%".$expKey[$k]."%'";
				}

				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';                 	
			}


			
			//employee attendance hr part
			if($rules->{'field'}=='rangehr.attendanceDate')
			{
			$start="";
			$end="";
			$sql .= ' ( ';
				$expKey = explode('-',filter_values($rules->{'data'}));
				
				$start=(date('Y-m-d',strtotime($expKey[0])));
				$end=(date('Y-m-d',strtotime($expKey[1])));
				 $sql  .= "DATE_FORMAT(attendanceDate,'%Y-%m-%d') >= '".$start."'" ;
				$sql  .= "and  DATE_FORMAT(attendanceDate,'%Y-%m-%d') <= '".$end."'"; $sql .= ' ) ';
				 /* if( count($objJson->{'rules'})>1)
				 { */
				$sql .= $objJson->{'groupOp'}.' ';
				/* } */
			
				unset($objJson->{'rules'}[$i]);
				
			}
			
			//employee attendance hr part
			 
			}
			foreach ($objJson->{'rules'} as $rules)
		       {
			$sql .= $rules->{'field'}.' '; // field name
			$sql .= $this->decodeGridOP($rules->{'op'},filter_values($rules->{'data'})).' '; // op, val
			$sql .= $objJson->{'groupOp'}.' '; // and, or
			}
			
			$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
		return $sql.') ';
			}
			}
		

	   echo $sql;
       die;	 
	}

    //24-Aug-18
	// function get_emp_attendance_details($id=0, $resultType='G')
	// {
	    
	// 	if($id){
	// 	$addsql .= " AND a.empId=".$id."";
	// 	}
	 
	// 	if($this->input->post('filters')!='') // search filters
	// 	{
	// 	    if(self::decodeFilters($this->input->post('filters')))
	// 		{
	// 		$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
	// 		}
	// 	}
	// 	// print_r($addsql);
	// 	// die();
	// 	//new search option
	// 	if($this->input->post('from')!='')
	// 	{
	// 		$addsql .= " and  DATE_FORMAT(attendanceDate,'%Y-%m-%d')>='".$this->input->post('from')."' ";
	// 	}
	// 	if($this->input->post('to')!='')
	// 	{
	// 		$addsql .= " and  DATE_FORMAT(attendanceDate,'%Y-%m-%d')<='".$this->input->post('to')."' ";
	// 	} 
	// 	//new search option
	// 	$region = explode(',',$this->session->userdata('admin_region')); 
	// 	if($region['0']){
	// 	$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
	// 	}
	// 	$sql = "select DATE_FORMAT(e.empDOJ,'%d-%b-%Y')as doj,d.name as dept,de.name as desig,c.cityName,mobileattendance.*,a.*,a.status as regularizationStatus,if(epr.responceDate='00:00:00','00:00:00', DATE_FORMAT(epr.responceDate,'%d %b, %Y')) as responceDate, DATE_FORMAT(a.attendanceDate,'%d %b, %Y') as  attendanceDay, if(a.inTime ='00:00:00','00:00',DATE_FORMAT(a.inTime, '%h:%i %p')) as inTime, if(a.outTime ='00:00:00','00:00',DATE_FORMAT(a.outTime, '%h:%i %p')) as outTime, if(a.workingHours ='00:00:00','00:00',DATE_FORMAT(a.workingHours, '%H:%i')) as workingHours, DATE_FORMAT(a.inTimediff,'%H:%i') as inTimediff, DATE_FORMAT(a.outTimediff,'%H:%i') as outTimediff, concat(e.empFname,' ',e.empLname) as empName from ".TABLE_ATTENDANCE." a
	// 	LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
	// 	LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
	// 	LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
	// 	LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
	// 	LEFT JOIN ".TABLE_REGION." r on s.region=r.id
	// 	Left Join ".TABLE_DEPT." d on e.empDept=d.id
	// 	Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
	// 	Left Join ".TABLE_REGULARIZATION." epr on epr.requestFrom = a.empId AND a.attendanceDate between epr.fromDate AND epr.toDate
		
	// 	LEFT JOIN mobileattendance on mobileattendance.rowId= a.id
 // 		 where 1=1 AND a.empId!=0  ".$addsql." ";
	// // echo $sql;
	// // exit;
	// 	  if($resultType=='G')
	// 	{
	// 		$result = parent::result_grid_array($sql);
	// 	}
	// 	else
	// 	{
	// 		$result = $this->db->query($sql)->result_array();
	// 	}
	// 	// pre($result);
		
	// 	return $result;
		
	// }

	function get_emp_attendance_details($id='', $resultType='G')
	{
	    
		if($id){
		$addsql .= " AND a.empId=".$id."";
		}
	 
		if($this->input->post('filters')!='') // search filters
		{
		    if(self::decodeFilters($this->input->post('filters')))
			{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		// print_r($addsql);
		// die();
		//new search option
		if($this->input->post('from')!='')
		{
			$addsql .= " and  DATE_FORMAT(attendanceDate,'%Y-%m-%d')>='".filter_date($this->input->post('from'))."' ";
		}
		if($this->input->post('to')!='')
		{
			$addsql .= " and  DATE_FORMAT(attendanceDate,'%Y-%m-%d')<='".filter_date($this->input->post('to'))."' ";
		} 
		//new search option
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}

        $stateIds =   $this->stateRolePermission($this->session->userdata('admin_id'));	    
		if($stateIds['stateIds']){
			$commaSepState   =   $stateIds['stateIds'];   
			$stateCond       =  " and e.jobLocation in (".$commaSepState.")";      
		} else {
            $stateCond = '';
		}


		$sql = "select DATE_FORMAT(e.empDOJ,'%d-%b-%Y')as doj,d.name as dept,de.name as desig,c.cityName,GROUP_CONCAT(rd.attLocTyp order by rd.attendanceDatetime asc) as workType, mobileattendance.*,a.*,a.status as regularizationStatus,if(epr.responceDate='00:00:00','00:00:00', DATE_FORMAT(epr.responceDate,'%d %b, %Y')) as responceDate, DATE_FORMAT(a.attendanceDate,'%d %b, %Y') as  attendanceDay, if(a.inTime ='00:00:00','00:00',DATE_FORMAT(a.inTime, '%h:%i %p')) as inTime, if(a.outTime ='00:00:00','00:00',DATE_FORMAT(a.outTime, '%h:%i %p')) as outTime, if(a.workingHours ='00:00:00','00:00',DATE_FORMAT(a.workingHours, '%H:%i')) as workingHours, DATE_FORMAT(a.inTimediff,'%H:%i') as inTimediff, DATE_FORMAT(a.outTimediff,'%H:%i') as outTimediff, concat(e.empFname,' ',e.empLname) as empName from ".TABLE_ATTENDANCE." a
		LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
		LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		LEFT JOIN ".TABLE_ROWDATA." rd on a.attendanceDate = date(rd.attendanceDatetime) and  a.empId = rd.empId
		Left Join ".TABLE_REGULARIZATION." epr on epr.requestFrom = a.empId AND a.attendanceDate between epr.fromDate AND epr.toDate AND epr.status = 'A'
		LEFT JOIN mobileattendance on mobileattendance.rowId= a.id
 		where 1=1  $stateCond AND a.empId!=0 ".$addsql."  group by rd.empId,date(rd.attendanceDatetime)";
	echo $sql;
	exit;
		  if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		// pre($result);
		
		return $result;
		
	}

// Added for emp-attendance-logs 
	function get_emp_attendance_logs($id=0, $resultType='G')
	{	    
		if($id){
		$addsql .= " AND a.empId=".$id."";
		}
	 
		if($this->input->post('filters')!='') // search filters
		{
		    if(self::decodeFilters($this->input->post('filters')))
			{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		// print_r($addsql);
		// die();
		//new search option
		if($this->input->post('from')!='')
		{
			$addsql .= " and  DATE_FORMAT(attendanceDate,'%Y-%m-%d')>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addsql .= " and  DATE_FORMAT(attendanceDate,'%Y-%m-%d')<='".$this->input->post('to')."' ";
		} 
		//new search option
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		$sql = "select DATE_FORMAT(e.empDOJ,'%d-%b-%Y')as doj,d.name as dept,de.name as desig,c.cityName,mobileattendance.*,a.*,a.status as regularizationStatus,if(epr.responceDate='00:00:00','00:00:00', DATE_FORMAT(epr.responceDate,'%d %b, %Y')) as responceDate, epr.fromDate as fromDate,epr.toDate as toDate, epr.status as leaveStatus,epr.regularizationType as regularizationType,epr.parentId as parentId, DATE_FORMAT(a.attendanceDate,'%d %b, %Y') as  attendanceDay, eprl.status as regulStatus,eprl.parentId as regulParentId,eprl.parentId as regulParentId,eprl.regularizationApplyfor as regulRegularizeType, a.attendanceDate as dateAttendance, if(a.inTime ='00:00:00','00:00',DATE_FORMAT(a.inTime, '%h:%i %p')) as inTime, if(a.outTime ='00:00:00','00:00',DATE_FORMAT(a.outTime, '%h:%i %p')) as outTime, if(a.workingHours ='00:00:00','00:00',DATE_FORMAT(a.workingHours, '%H:%i')) as workingHours, DATE_FORMAT(a.inTimediff,'%H:%i') as inTimediff, DATE_FORMAT(a.outTimediff,'%H:%i') as outTimediff, concat(e.empFname,' ',e.empLname) as empName from ".TABLE_ATTENDANCE." a
		LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
		LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_REGULARIZATION." epr on a.empId = epr.requestFrom  AND a.attendanceDate between epr.fromDate AND epr.toDate AND epr.status != 'R' AND epr.parentId = 0
		Left Join ".TABLE_REGULARIZATION." eprl on a.id = eprl.parentId
		
		LEFT JOIN mobileattendance on mobileattendance.rowId= a.id
 		 where 1=1 AND a.empId!=0  ".$addsql." group by a.id ";
	// echo $sql;
	// exit;
		  if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		// pre($result);
		
		return $result;
		
	}
	 //24-Aug-18
	function get_emp_late_details($date)
	{
	echo $sql = "select a.*,a.status as regularizationStatus,DATE_FORMAT(a.attendanceDate,'%d %b, %Y') as  attendanceDay, DATE_FORMAT(a.inTime,'%H:%i:%s') as inTime,DATE_FORMAT(a.outTime,'%H:%i:%s') as outTime,  DATE_FORMAT(a.workingHours,'%H:%i') as workingHours, DATE_FORMAT(a.inTimediff,'%H:%i') as inTimediff, DATE_FORMAT(a.outTimediff,'%H:%i') as outTimediff, concat(e.empFname,' ',e.empLname) as empName from ".TABLE_ATTENDANCE." a
		LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
		where 1=1 and a.attendanceDate=".$date." ";
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		pre($result);
		
		return $result;
		
	}

	//19DEC
	function attendance_regularization_details($id=0, $resultType='G')
	{
	    
		if($id){
		    $addsql .= " AND a.empId=".$id."";
		}
	 
		if($this->input->post('filters')!='') // search filters
		{
		    if(self::decodeFilters($this->input->post('filters')))
			{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		//new search option
		if($this->input->post('from')!='')
		{
			$addsql .= " and  DATE_FORMAT(attendanceDate,'%Y-%m-%d')>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addsql .= " and  DATE_FORMAT(attendanceDate,'%Y-%m-%d')<='".$this->input->post('to')."' ";
		} 
		//new search option
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}

		$stateIds   =  $this->stateRolePermission($this->session->userdata('admin_id'));	    
		if($stateIds['stateIds']){
			$commaSepState   =   $stateIds['stateIds'];
			$stateCond       =  " and e.jobLocation in (".$commaSepState.")";      
		} else {
            $stateCond = '';
		}

		$sql = "select DATE_FORMAT(e.empDOJ,'%d-%b-%Y')as doj,d.name as dept,de.name as desig,c.cityName,mobileattendance.*,a.*,epr.status as regularizationStatus,epr.approved_status as approvedStatus,epr.parentId as attendenceId, DATE_FORMAT(a.attendanceDate,'%d %b, %Y') as  attendanceDay, if(a.inTime ='00:00:00','00:00',DATE_FORMAT(a.inTime, '%h:%i %p')) as inTime, if(a.outTime ='00:00:00','00:00',DATE_FORMAT(a.outTime, '%h:%i %p')) as outTime, if(a.workingHours ='00:00:00','00:00',DATE_FORMAT(a.workingHours, '%H:%i')) as workingHours, DATE_FORMAT(a.inTimediff,'%H:%i') as inTimediff, DATE_FORMAT(a.outTimediff,'%H:%i') as outTimediff, concat(e.empFname,' ',e.empLname) as empName from ".TABLE_ATTENDANCE." a
		LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
		LEFT JOIN ".TABLE_CANDIDATE." can on e.candidateId=can.id
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_REGULARIZATION." epr on epr.requestFrom = a.empId AND a.attendanceDate  between epr.fromDate AND epr.toDate
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
		LEFT JOIN mobileattendance on mobileattendance.rowId= a.id
 		 where 1=1   $stateCond AND a.empId!=0 ".$addsql." ";

	
		  if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);	
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		// pre($result);

		// print_r($result);
		
		return $result;
		
	}
	
	
	function get_leavegroup_details($id=0, $resultType='G')
	{
	if($id> 0 )
	{
	$addsql .= " and id=".$id;
	}
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		 $sql = "select id, name from ".$this->base_tbl." where 1=1 ";
		//echo $sql; 

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
	
	function get_leavetype_details($id=0, $resultType='G')
	{
	if($id> 0 )
	{
	$addsql .= " and id=".$id;
	}
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		 $sql = "select * from ".TABLE_LEAVETYPE." where 1=1 ";
		//echo $sql; 

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
	
	function get_leave_details($id=0, $ltype)
	{
	$addsql ='';
	if($id > 0 )
	{
	$addsql .= " and l.leaveGroup=".$id;
	}
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select l.*,g.name as leave_group,lt.description as leaveType  from ".TABLE_RULE." l Left Join ".$this->base_tbl." g on g.id=l.leaveGroup Left Join tbl_mst_leavetype lt  on l.leaveType=lt.name  where 1=1 and lt.ltype='".$ltype."'  ".$addsql."";
		//echo $sql; die;
	return $result = $this->db->query($sql)->result_array();
	}
	
		function get_dayoff_details($id=0, $ltype)
	{
	
		$sql = "select id, dayoff, weekoff, weekno  from ".TABLE_RULE."  where 1=1 and leaveGroup='".$id."' and leaveType='".$ltype."'";
		//echo $sql; die;
	return $result = $this->db->query($sql)->result_array();
	}
	
	function get_dayoff($id)
	{
	
		$sql = "select *  from ".TABLE_RULE."  where 1=1 and id=".$id."";
		//echo $sql; die;
	return $result = $this->db->query($sql)->result_array();
	}
	
	
	
	function get_shift_details($id=0, $resultType='G')
	{
	if($id>0)
	{
	$addsql .= " and id=".$id;
	}
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		 $sql = "select *,DATE_FORMAT(shiftTimeFrom,'%h:%i %p') as shiftTimeFrom, DATE_FORMAT(shiftTimeTo,'%h:%i %p') as shiftTimeTo,  graceTimeTill, DATE_FORMAT(halfDayStart, '%h:%i %p') as halfDayStart, DATE_FORMAT(minimumWorkingHours, '%H:%i') as minimumWorkingHours from ".TABLE_SHIFT." where 1=1 ".$addsql."";
		//echo $sql; 

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
	
	
	
	function get_daily_attendance_log_details($id=0, $resultType='G')
	{
	if($id> 0 )
	{
	$addsql .= " and id=".$id;
	}
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		if($this->input->post('from')!='' and $this->input->post('to')=='')
		{
			$addSql .= " and  date(r.attendanceDatetime) between date('".$this->input->post('from')."') and date('".$this->input->post('from')."') ";
		}
		if($this->input->post('from')=='' and $this->input->post('to')!='')
		{
			$addSql .= " and  r.attendanceDatetime <='".$this->input->post('to')."' ";
		}
		if($this->input->post('from')!='' and $this->input->post('to')!='')
		{
			$addSql .= " and  date(r.attendanceDatetime) between date('".$this->input->post('from')."') and date('".$this->input->post('to')."') ";
		}
			
		
        $stateIds =   $this->stateRolePermission($this->session->userdata('admin_id'));	    
		if($stateIds['stateIds']){
			$commaSepState   =   $stateIds['stateIds'];   
			$stateCond       =  " and e.jobLocation in (".$commaSepState.")";      
		} else {
            $stateCond = '';
		} 
		
		 $sql ="select r.id, r.empId, DATE_FORMAT(r.attendanceDatetime,'%W %d-%b-%Y') as attendancedate, DATE_FORMAT(r.attendanceDatetime,'%H:%i:%s') as attendancetime, concat(e.empFname,' ',e.empLname) as empName from rowdata r
		   Left Join  ".TABLE_EMP." e on r.empId=e.empId 
		   LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		   LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id	
		     where 1=1 $stateCond ".$addSql.""; 

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
	
	
	function insert_in_emp_daily_attendance($date)
	{

		 $sql = "select GROUP_CONCAT(a.lat) as lat,GROUP_CONCAT(a.log) as log,GROUP_CONCAT(a.image) as image,r.id as region,a.source, a.attendanceDatetime, a.empId, DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') as attendanceDate, min(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as inTime,  max(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as outTime, TIME_FORMAT(TIMEDIFF(max(a.attendanceDatetime),min(a.attendanceDatetime)),'%H:%i:%s') as workingHours, es.shift as servicesShift, ed.shift as departmentShift  from rowdata a
		LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
		LEFT JOIN ".TABLE_SERVICE." es on e.empId=es.empId
		LEFT JOIN ".TABLE_DEPT." ed on e.empDept=ed.id
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
		LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
 		 where 1=1 and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d')='".$date."' and a.empId=e.empId group by a.empId";
		return $result = $this->db->query($sql)->result_array();
		//pre($result);
		
	}
	
	function insert_in_emp_daily_attendance_backup_12_feb_2016($date)
	{
	
		 $sql = "select r.id as region,a.source, a.attendanceDatetime, a.empId, DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') as attendanceDate, min(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as inTime,  max(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as outTime, DATE_FORMAT(TIMEDIFF(max(a.attendanceDatetime),min(a.attendanceDatetime)),'%H:%i') as workingHours, s.shiftTimeFrom, TIMEDIFF(min(DATE_FORMAT(a.attendanceDatetime,'%H:%i')), DATE_FORMAT(s.shiftTimeFrom,'%H:%i')) as inTimeDiff, TIMEDIFF(max(DATE_FORMAT(a.attendanceDatetime,'%H:%i')), DATE_FORMAT(s.shiftTimeTo,'%H:%i')) as outTimeDiff, s.shiftName as shift from rowdata a
		LEFT JOIN ".TABLE_SERVICE." es on a.empId=es.empId
		LEFT JOIN ".TABLE_SHIFT." s on es.shift = s.id
		LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
		LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
 		 where 1=1 and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d')='".$date."' group by a.empId";
		return $result = $this->db->query($sql)->result_array();
		//pre($result);
		
	}
	
	
	function leaveGroupList()
	{
		$sql = "select id, name from ".$this->base_tbl." where 1=1 order by name";
		return $result = $this->db->query($sql)->result_array();
	}

	function attendanceLog($id)
	{
	if($id!=10000){
	$sql ="select a.*,DATE_FORMAT(a.attendanceDate,'%d-%b-%Y') attendanceDate from ".TABLE_ATTENDANCE." a where a.empId=".$id." Order By a.attendanceDate DESC";
	}else{
	$sql ="select a.*,DATE_FORMAT(a.attendanceDate,'%d-%b-%Y') attendanceDate from ".TABLE_ATTENDANCE." a where 1=1 Order By a.attendanceDate DESC";
	}
	 return $result = $this->db->query($sql)->result_array();
	}
	
	function teamattendanceLog($id)
	
	{
		$sql ="select a.*,DATE_FORMAT(a.attendanceDate,'%d-%b-%Y') attendanceDate from ".TABLE_ATTENDANCE." a
		Left Join ".TABLE_EMP." e on a.empId = e.empId
		 where 	e.reportingTo=".$id."  Order By a.attendanceDate DESC";
	 return $result = $this->db->query($sql)->result_array();
	}
	
	
	
	function attendanceDetail($id)
	{
	$sql ="select a.id, a.workingHours, a.empId, attendanceDate, a.attendanceStatus, DATE_FORMAT(a.inTime,'%H:%i') as inTime, DATE_FORMAT(a.outTime,'%H:%i') as outTime, r.empEmailOffice,e.reportingTo,r.empFname  from ".TABLE_ATTENDANCE." a 
	Left Join ".TABLE_EMP." e on a.empId=e.empId
	Left Join ".TABLE_EMP." r on e.reportingTo=r.empId
	 where a.id = ".$id."";
	 return $result = $this->db->query($sql)->result_array();
	
	}
		function regularizationDetail($id)
	{
	
	$sql ="select r.id, r.parentId, r.regularizationType,r.regularizationApplyfor, r.requestFrom, r.requestTo, a.workingHours, a.attendanceStatus, DATE_FORMAT(a.inTime,'%H:%i %p') as inTime, DATE_FORMAT(a.outTime,'%H:%i %p') as outTime, a.attendanceDate, r.remarks,e.empEmailPersonal, e.empEmailOffice, concat(e.empFname,' ',e.empLname) as empName from ".TABLE_REGULARIZATION." r
			LEFT JOIN ".TABLE_ATTENDANCE." a on r.parentId=a.id
			LEFT JOIN ".TABLE_EMP." e on r.requestFrom=e.empId
	 	 where r.id = ".$id."";
	 $result = $this->db->query($sql)->result_array();
	//pre($result);die;
	return $result;
	
	}
	
	function fetchAttendance()
	{
	$sql ="select empId, attendanceDate, inTime, outTime, source from ".TABLE_ATTENDANCE_TEMP."  where attendanceDate = CURDATE() - INTERVAL 1 DAY ";
	
	 return $result = $this->db->query($sql)->result_array();
	}
	
	function dailyAttendancelog()
	{
	$sql ="select empId,attendanceDate,inTime,outTime,source,DATE_FORMAT(attendanceDate,'%d %b, %Y') attendanceDate from ".TABLE_ATTENDANCE_TEMP."
	  where 1=1 order by id DESC";
		//$sql ="select empId,attendanceDate,inTime,outTime,Source from ".TABLE_ATTENDANCE_TEMP."  where attendanceDate = CURDATE() - INTERVAL 1 DAY ";
	 return $result = $this->db->query($sql)->result_array();
	}
	
	function getEmployeeList($date)
	{
	 $sql ="select a.*,DATE_FORMAT(a.attendanceDate,'%d %b, %Y') attendanceDate,e.empEmailOffice,concat(e.empFname,' ',e.empLname) as empName from ".TABLE_ATTENDANCE." a
	Left Join ".TABLE_EMP." e on a.empId=e.empId where a.attendanceDate ='".$date."'";
	 return $result = $this->db->query($sql)->result_array();
	}
	function getInfoattendance($id)
	{
	 $sql ="select empId,DATE_FORMAT(attendanceDate,'%d %b, %Y') attendanceDate from ".TABLE_ATTENDANCE."  where id=".$id."";
	 $data['result1'] = $this->db->query($sql)->result_array();
	 $sql1 ="select empEmailOffice, concat(empFname,' ',empLname) as empName from ".TABLE_EMP."  where empId=".$data['result1']['0']['empId']."";
	 $data['result2'] = $this->db->query($sql1)->result_array();
	 return $data;
	 }
	 //leave Request Details
	 function getLeaveDetails($id)
	{
	 $sql ="select *,DATE_FORMAT(leaveFrom,'%d %b, %Y') leaveFrom,DATE_FORMAT(leaveTo,'%d %b, %Y') leaveTo  from ".TABLE_EMP_LEAVE."  where id=".$id."";
	 $data['result1'] = $this->db->query($sql)->result_array();
	 $sql1 ="select empEmailOffice, concat(empFname,' ',empLname) as empName from ".TABLE_EMP."  where empId=".$data['result1']['0']['empId']."";
	 $data['result2'] = $this->db->query($sql1)->result_array();
	 return $data;
	 }
	 //
	 function leaveTypeList($type)
	{
		$sql = "select id, name from ".TABLE_LEAVETYPE." where 1=1 and ltype='".$type."' order by name";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}


	function leaveTypeListforemp($empId)
	{
		$sql ="select b.empId, b.id, b.leaveType,r.description, b.balanceLeave, es.leaveGroup, r.leaveType, r.noOfleave from tbl_emp_leave_balance b
	    Left Join ".TABLE_SERVICE." es on b.empId=es.empId
	    Left Join ".TABLE_RULE." r on es.leaveGroup=r.leaveGroup
	    WHERE b.empId=".$empId." and  b.leaveType=r.leaveType and r.noOfleave > 0 ";
		$result = $this->db->query($sql)->result_array();

	#######For checking SBL Leave Availability past 2 years#################
        $getsbl=$this->db->query('Select id,requestFrom from tbl_regularization where leaveType = "SBL" AND requestFrom="'.$empId.'" AND status!="R" AND (YEAR(fromDate)="'.date("Y",strtotime("-2 year")).'") ')->result_array();
        // print_r( $getsbl);

     #######For Getting empgender and Grade and no of months################# 
	  $this->db->select('emp.empDOJ,emp.empgender,ep.empGender,eg.grade,ep.empMaritalStatus,TIMESTAMPDIFF(MONTH, emp.empDOJ, now()) noofmonth');
	  $this->db->from('tbl_emp_master emp');
	  $this->db->join('tbl_emp_personal ep', 'emp.empId = ep.empId');
	  $this->db->join('employees_grade eg', 'eg.empId= emp.empId');
	  $this->db->where('ep.empId',$empId);
	  $empresult = $this->db->get();
	   $data = $empresult->result_array();


	   

	   // print_r($this->db->last_query());
	   // die();
	   
	   $count = count($result);

	    ######## For Special Leave count #########
     $this->db->select('id,total_leave as opening,leave_type as leaveType,description');
     $query = $this->db->get('special_leave');
     $specialLeave =   $query->result_array();

     // print_r( $specialLeave[0]);
     $totalPL =  $specialLeave[0]['opening'];
     $totalML =  $specialLeave[1]['opening'];
     $totalMSL =  $specialLeave[2]['opening'];


	   

		// // echo $count;
	   		if($data[0]['empgender'] == "Male" && $data[0]['empMaritalStatus'] =='Married')
		    {
		      	$result[$count]['empId'] = $empId;
		      	$result[$count]['id'] = '';
		      	$result[$count]['leaveType'] = $specialLeave[0]['leaveType'];
		      	$result[$count]['description'] = $specialLeave[0]['description'];
		      	$result[$count]['fullName']     = $specialLeave[0]['description'];
		      	$result[$count]['balanceLeave'] = '';
		      	$result[$count]['leaveGroup'] = $result[0]['leaveGroup'];
		      	$result[$count]['noOfleave'] = "6";
		      	$result[$count]['grade'] = $data[0]['grade'];
		      	$result[$count]['noOfmonth'] = $data[0]['noofmonth']; 

		    }
	        else if($data[0]['empgender'] == "Female" or $data[0]['empGender'] == "Female"){

	        	// if($data[0]['empMaritalStatus'] == 'Unmarried' or $data[0]['empMaritalStatus'] == 'Single'){
	             $this->db->select('id,total_leave as opening,leave_type as leaveType,description');
				$this->db->where('marital_status',$data[0]['empMaritalStatus']);
				$this->db->where('leave_type','LSL');
			     $query = $this->db->get('special_leave');
			     $LSL =   $query->result_array();

			     if(!empty($LSL)){
			        $result[$count]['empId'] = $empId;
			      	$result[$count]['id'] = '';
			      	$result[$count]['leaveType'] = $LSL[0]['leaveType'];
			      	$result[$count]['description'] = $LSL[0]['description'];
			      	$result[$count]['fullName'] = $LSL[0]['description'];
			      	$result[$count]['balanceLeave'] = '';
			      	$result[$count]['leaveGroup'] = $result[0]['leaveGroup'];
			      	$result[$count]['noOfleave'] = "1";
			      	$result[$count]['grade'] = $data[0]['grade'];
			      	$result[$count]['noOfmonth'] = $data[0]['noofmonth']; 
			      }

				      
				// }else{
			

				  //     	$now    = time(); // or your date as well
						// $joing_date = strtotime($data[0]['empDOJ']);
						// $datediff = $now - $joing_date;
						// $diff     =round($datediff / (60 * 60 * 24));
						// echo $diff;

				      	if($data[0]['noofmonth'] >'4'){
				      	$this->db->select('id,total_leave as opening,leave_type as leaveType,description,pre_maternity,post_maternity');
				      	$this->db->where('marital_status',$data[0]['empMaritalStatus']);
						$this->db->where('leave_type','ML');
					    $query = $this->db->get('special_leave');
					    $ML =   $query->result_array();
					    if(!empty($ML)){
				      	   $result[$count+1]['empId'] = $empId;
					      	$result[$count+1]['id'] = '';
					      	$result[$count+1]['leaveType'] = $ML[0]['leaveType'];;
					      	$result[$count+1]['description'] = $ML[0]['description'];
					      	$result[$count+1]['fullName'] =  $ML[0]['description'];
					      	$result[$count+1]['balanceLeave'] = '';
					      	$result[$count+1]['leaveGroup'] = $result[0]['leaveGroup'];
					      	$result[$count+1]['noOfleave'] = 84;
					      	$result[$count+1]['grade'] = $data[0]['grade'];
					      	$result[$count+1]['noOfmonth'] = $data[0]['noofmonth'];
					      
					    } 
				     }
				    // }

			      	if($data[0]['noofmonth'] >='60' && count($getsbl) == 0){
			      	$this->db->select('id,total_leave as opening,leave_type as leaveType,description,pre_maternity,post_maternity');
				     $this->db->where('marital_status',$data[0]['empMaritalStatus']);
					$this->db->where('leave_type','SBL');
					$query = $this->db->get('special_leave');
					$SBL =   $query->result_array();	
					    if(!empty($SBL)){


					      	$result[$count+2]['empId'] = $empId;
					      	$result[$count+2]['id'] = '';
					      	$result[$count+2]['leaveType'] = $SBL[0]['leaveType'];
					      	$result[$count+2]['description'] = $SBL[0]['description'];
					      	$result[$count+2]['fullName'] =  $SBL[0]['description'];
					      	$result[$count+2]['balanceLeave'] = '';
					      	$result[$count+2]['leaveGroup'] = $result[0]['leaveGroup'];
					      	$result[$count+2]['noOfleave'] = "21";
					      	$result[$count+2]['grade'] = $data[0]['grade'];
					      	$result[$count+2]['noOfmonth'] = $data[0]['noofmonth']; 
			            }
			      	// $result[$count+2]['empId'] = $empId;
			      	}


				
	        }

		
		

	
		return $result;
	}


	function updateAttendence($data)
	{

		// $data = array('attendanceStatus' => 'A');
		$this->db->where('empId',$data['requestFrom']);
		// $this->db->where('attendanceDate', '>=',$data['fromDate']);
		$this->db->where('attendanceDate >=',$data['fromDate']);
        $this->db->where('attendanceDate <=',$data['toDate']);
		$query = $this->db->get('tbl_emp_attendance');
		   
		//return $query->result_array();
		foreach ($query->result_array() as $row)
		{
		     $id =  $row['id'];
		    if($row['attendanceStatus'] == "L"){
			     $data = array('attendanceStatus' => 'A');
			      $this->db->where('id',$id);
			      $this->db->where('attendanceStatus','L');
			      // $this->db->orwhere('attendanceStatus','HL');
			      $this->db->update('tbl_emp_attendance', $data);
		    }else if($row['attendanceStatus'] == "HL" and $row['workingHours'] >= '4:00:00'){
                  $data = array('attendanceStatus' => 'HD');
			      $this->db->where('id',$id);
			      $this->db->where('attendanceStatus','HL');
			      $this->db->update('tbl_emp_attendance', $data);
		    }else if(($row['attendanceStatus'] == "HD") or ($row['attendanceStatus'] == "HL" and $row['workingHours'] < '4:00:00')){
		    	
		    	 $data = array('attendanceStatus' => 'A');
			      $this->db->where('id',$id);
			      // $this->db->where('attendanceStatus','HD');
			      $this->db->update('tbl_emp_attendance', $data);
		    }
		   
		    
		}
		 //return $success;
    

	}

	#########  Get Leave Data Of Empoyee ############
	function empLeaveData($empid = '' ,$id)
	{
		$this->db->select("*");
		$this->db->from("tbl_regularization");
		$this->db->where('id',$id);
		// $this->db->where('requestFrom',$empId);
		$this->db->limit(1);
		$query = $this->db->get();
		// print_r($this->db->last_query());
		// die();
		return $query->result_array();
	}

	######### Update Cancel Leave Data Of Empoyee ############
	function cancelLeaveUpdate($data,$id)
	{
		
		$result = $this->db->update('tbl_regularization', $data, array('id' => $id));	
		return $result;
	}
	######### Update Cancel Leave Data Of Empoyee ############
	function getinfoleave($id)
	{
		$this->db->select("*");
		$this->db->from("tbl_regularization");
		$this->db->where("id",$id);
		$query = $this->db->get();
		if($query->num_rows() > 0){      
            $result     =  $query->result_array();
           return $result     =  $result[0];
        } else {
           return $result = array(); 
        }
	}


    ######### Get Manager Details for Sending Mail ############

	function getManagerDetail($id)
	{
		$this->db->select("empFname,empLname,empEmailOffice");
		$this->db->from("tbl_emp_master");
		$this->db->where('empId',"$id");
		$this->db->limit(1);
		$query = $this->db->get();

		return $query->result_array();
	}
	
	
	function leaveGroup()
	{
		$sql = "select id, name from ".TABLE_LEAVEGROUP." where 1=1 order by name";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	function getLeaveList($id,$resultType='G')
	{
		if($id > 0)
		{
			$addsql .= " and requestFrom=".$id;
		}
	
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select *,DATE_FORMAT(fromDate,'%d-%b-%Y')as lFrom, DATE_FORMAT(toDate,'%d-%b-%Y')as lTo, DATE_FORMAT(regularizationDate,'%d-%b-%Y %h:%i:%S')as rdate, noofdays, leaveType  from ".TABLE_REGULARIZATION." where 1=1 and (regularizationType='L' or regularizationType='FHD'or regularizationType='SHD') ".$addsql." ";
		//echo $sql; die;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		// pre($result);
		return $result;

	}
	
	function get_team_leave_request_details($id,$resultType='G',$hrid=0)
	{
		$stateCond = '';
		if($id!=10000 AND $hrid!='HR'){
	   $addsql .=" and r.requestTo='".$id."'";
	   $addCond = " or (r.requestTo='".$id."' and r.cancelled_status = '2')"; 
	}
	else
	{
		 $addsql.="";
		 $stateIds =   $this->stateRolePermission($this->session->userdata('admin_id'));	    
		 if($stateIds['stateIds']){
			$commaSepState   =   $stateIds['stateIds'];   
			$stateCond       =  " and e.jobLocation in (".$commaSepState.")";      
	     } else {
              $stateCond = '';
	     }	
	}
	/* if($id > 0)
		{
			$addsql .= " and r.requestTo=".$id;
		} */
	
	if($this->input->post('filters')!='') // search filters
		{
		   if(self::decodeFilters($this->input->post('filters')))
			{ 
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			// echo $addsql;
			}
		}
	
	

	$sql = "select e.empId,ep.empMaritalStatus,DATE_FORMAT(e.empDOJ,'%d-%b-%Y')as DOJ,concat(rep.empFname,' ',rep.empLname) as managerName,de.name as designame,d.name as department,c.cityName as jobCityName,r.*,DATE_FORMAT(r.fromDate,'%d-%b-%Y')as lFrom, DATE_FORMAT(r.toDate,'%d-%b-%Y')as lTo, DATE_FORMAT(r.regularizationDate,'%d-%b-%Y ')as rdate, concat(e.empFname,' ',e.empLname) as empName, r.noofdays, lt.description as lType   from ".TABLE_REGULARIZATION." r
	 Left Join ".TABLE_EMP." e on r.requestfrom =e.empId
	 Left Join ".TBL_EMP_PERSONAL." ep on ep.empId =e.empId
	 Left Join ".TABLE_LEAVETYPE." lt on r.leaveType=lt.name
	 Left Join ".TABLE_DEPT." d on e.empDept=d.id
	Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
    Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
	LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
	LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
    LEFT JOIN ".TABLE_EMP." rep on rep.empId=e.reportingTo
	 where 1=1 $stateCond and e.empId NOT LIKE '%100%'   and (r.regularizationType='L' or r.regularizationType='SHD' or r.regularizationType='FHD') ".$addsql.' '.$addCond;

	// echo  $sql;die;
		if($resultType=='G')
		{
			$result = parent::result_grid_array($sql);
		}
		
		else
		{
			$result = $this->db->query($sql)->result_array();
		}
		// pre($result);
		return $result;

	}
	
	function shiftTime($id)
	{
		$sql = "select * from ".TABLE_SHIFT." where location=".$id." order by id";
		$result = $this->db->query($sql)->result_array();
		return $result['0'];
	}
	function leaveRequest($id)
	{
		$sql ="select l.*,DATE_FORMAT(l.leaveFrom,'%d-%b-%Y')as leaveFrom,DATE_FORMAT(l.leaveTo,'%d-%b-%Y')as leaveTo, concat(e.empFname,' ',e.empLname) as empName,e.empId,lt.Name from ".TABLE_EMP_LEAVE." l Left Join
		".TABLE_EMP." e on l.empId = e.empId Left Join
		".TABLE_LEAVETYPE." lt on l.leaveType =lt.Id Left Join
		".TABLE_SERVICE." s on l.empId=s.empId where e.reportingTo=".$id."";
	 return $result = $this->db->query($sql)->result_array();
	}
	
	function regularizationRequest($id)
	{
		$sql ="select a.*,DATE_FORMAT(a.attendanceDate,'%d-%b-%Y') attendanceDate,concat(e.empFname,' ',e.empLname) as empName from ".TABLE_ATTENDANCE." a
		Left Join ".TABLE_EMP." e on a.empId = e.empId
		 where 	e.reportingTo=".$id."  Order By a.attendanceDate ASC";
		 //AND (regularizationStatus='0' or regularizationStatus='1')*/
	 return $result = $this->db->query($sql)->result_array();
	}
	function get_emp_regularization_request_details($id=0,$resultType='G',$hrid=0)
	{
	if($id!='10001' AND $hrid!='HR'){
	$addsql =" and r.requestTo='".$id."'";
	}
	else
	{
	$addsql="";
	}
	if($this->input->post('filters')!='') // search filters
	{
	 if(self::decodeFilters($this->input->post('filters')))
	{
	$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
	}
	}

	$stateIds =   $this->stateRolePermission($this->session->userdata('admin_id'));	    
		if($stateIds['stateIds']){
			$commaSepState   =   $stateIds['stateIds'];   
			$stateCond       =  " and e.jobLocation in (".$commaSepState.")";      
		} else {
            $stateCond = '';
		}

	$sql ="select a.attendanceStatus,e.empId,DATE_FORMAT(e.empDOJ,'%d-%b-%Y')as DOJ,concat(rep.empFname,' ',rep.empLname) as managerName,de.name as designame,d.name as department,c.cityName as jobCityName, concat(e.empFname,' ',e.empLname) as empName, r.parentId, r.id, r.requestFrom, r.regularizationType, DATE_FORMAT(r.regularizationDate,'%d-%b-%Y ') as regularizationDate, r.remarks, r.status , DATE_FORMAT(a.attendanceDate,'%d-%b-%Y') attendanceDate, if(a.inTime ='00:00:00','00:00',DATE_FORMAT(a.inTime, '%h:%i %p')) as inTime, if(a.outTime ='00:00:00','00:00',DATE_FORMAT(a.outTime, '%h:%i %p')) as outTime, if(a.workingHours ='00:00:00','00:00',DATE_FORMAT(a.workingHours, '%H:%i')) as workingHours, r.regularizationType from ".TABLE_REGULARIZATION." r
	Left Join ".TABLE_ATTENDANCE." a on r.parentId = a.id
	Left Join ".TABLE_EMP." e on r.requestFrom = e.empId
	Left Join ".TABLE_DEPT." d on e.empDept=d.id
	Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id		
    Left Join ".TABLE_CANDIDATE." can on e.candidateId=can.id
	LEFT JOIN ".TABLE_CITY." c on can.jobCity=c.cityId
	LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id	
    LEFT JOIN ".TABLE_EMP." rep on rep.empId=e.reportingTo
	 where 1=1 $stateCond ".$addsql." and  (r.regularizationType ='FL' or r.regularizationType ='FLO' or r.regularizationType ='FP' or r.regularizationType ='M')";

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
	
	function holidayList($cid=0,$lid=0,$type="G",$date=0)
	{
		if($this->session->userdata('admin_id')==10000 || $this->session->userdata('empDesination')==8 || strlen($this->session->userdata('admin_id'))==3)
		{
			$sql="SELECT GROUP_CONCAT(DISTINCT z.name) as name,h.holiday as holiday,DATE_FORMAT(h.holidayDate,'%a %d-%b') as date FROM tbl_mst_holiday h inner join tbl_mst_holiday_region_client hr on h.id=hr.holiday left join tbl_region z on hr.region=z.id where h.holidayDate>='".$date."'  group by h.id order by h.holidayDate asc";
			//echo $sql;die;
			return $result = $this->db->query($sql)->result_array();
		}
		else
		{
			$addsql = "";
			if($date){
			//	echo $date;
			//$addsql .=" AND holidayDate >'".$date."'";
			}
			$addsql .=" AND h.holidayDate >='".$date."'";
			
			//working one
			$sql="SELECT h.holiday as holiday,DATE_FORMAT(h.holidayDate,'%a %d-%b') as date from tbl_mst_holiday h left join tbl_mst_holiday_region_client hr on h.id=hr.holiday left join tbl_region r on r.id=hr.region where r.id = '".$this->session->userdata('region')."' and hr.clients='".$this->session->userdata('clients')."' ".$addsql." Order By h.holidayDate ASC";
			//echo $sql;die;
			return $result = $this->db->query($sql)->result_array();
		}
	}

	function empHolidayList($region,$clients,$type='A')
	{
		$addsql="";
		$stateId =  filter_numeric($this->input->post('state',true));
		if($stateId !='')
		{
			$addsql .= " and  hr.stateId='".c."' ";
			
		} 
		//new on 19-Aug-18 will be changed later
		/* if($this->input->post('filters')!='') // search filters
		{
		    if(self::decodeFilters($this->input->post('filters')))
			{  
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		} */
		//new on 19-Aug-18 will be changed later
		if(strlen($this->session->userdata('admin_id'))==3)
		{
			$sql="SELECT h.holiday as holiday,DATE_FORMAT(h.holidayDate,'%a %d-%b-%Y') as holidayDates FROM tbl_mst_holiday h inner join tbl_mst_holiday_region_client hr on h.id=hr.holiday left join tbl_region z on hr.region=z.id where DATE_FORMAT(h.holidayDate,'%Y')='".date('Y')."' group by h.id";
		}
		else
		{
			$sql ="SELECT h.holiday as holiday,DATE_FORMAT(h.holidayDate,'%W %d-%b-%Y') as holidayDates from tbl_mst_holiday h left join tbl_mst_holiday_region_client hr on h.id=hr.holiday left join tbl_region r on r.id=hr.region where DATE_FORMAT(h.holidayDate,'%Y')='".date('Y')."'  ".$addsql."  group by h.id";
			
			//echo $sql;die;
		}
		if($type=='G')

		{

			$result = parent::result_grid_array($sql);

		}
		else
		{

	 		$result = $this->db->query($sql)->result_array();

		}
//	pre($result);die;

	return $result;	
}
	/*	function empHolidayList($region,$type='A'){
	
	$sql="select h.holiday as holiday,r.name as name,DATE_FORMAT(h.holidayDate,'%d-%b, %Y') date,h.holidayDate from tbl_mst_holiday h left join tbl_mst_holiday_region hr on h.id=hr.holidayId left join tbl_region r on r.id=hr.region where DATE_FORMAT(h.holidayDate,'%Y')='".date('Y')."' and  hr.region = '".$region."' ".$addsql." ";
	if($type=='G')
		{
			$result = parent::result_grid_array($sql);
		}else{
	 $result = $this->db->query($sql)->result_array();
	}
	return $result;
	}*/
	//16-Aug-18 for new leave calulation 4-sep-18
	function todayempHolidayList($region=0,$clients=0,$date){
	$addSql="";
	if($date)
	{
	$addSql.="and h.holidayDate='".$date."' ";
	}
	$sql ="select h.holidayDate from ".TABLE_HOLIDAYS." h
	Left Join ".TABLE_REGION_HOLIDAY_CLIENTS_MASTER." hr on h.id=hr.holiday
	 where hr.stateId='".$region."' and hr.clients='".$clients."' ".$addSql." Order By h.holidayDate ASC";
	 
	//return $sql;
	 $result = $this->db->query($sql)->result_array();
	 
	 if($date)
	 {
	 return $result['0'];
	 }
	 else
	 {
	  return $result;
	 }
	}
	
	
	function avalableLeavepermonth($id)
	{
	$sql ="select r.*,r.leaveType, lt.description from ".TABLE_RULE." r
	LEFT JOIN ".TABLE_SERVICE." s on r.leaveGroup = s.leaveGroup
	LEFT JOIN ".TABLE_LEAVETYPE." lt on r.leaveType = lt.name
	WHERE s.empId =".$id." and (r.leaveType !='DO' and r.leaveType !='WO')
	";
	return $result = $this->db->query($sql)->result_array();
	}
	
 function availableLeave($eid, $ltype='', $noOfleave='')
	{
	if($ltype){
	$addsql =" and b.leaveType='".$ltype."'";
	}
	$sql1  ="select effectiveDate,leaveGroup from ".TABLE_SERVICE." where empId='".$eid."'";
	$result1 = $this->db->query($sql1)->result_array();
	
	if($result1['0']['effectiveDate'] >= date('Y-m-d')){
	 $sql ="select b.empId, b.id, b.leaveType, b.balanceLeave,b.opening, elg.leaveGroup, r.leaveType, r.noOfleave from tbl_emp_leave_balance b
	Left Join ".TABLE_SERVICE." es on b.empId=es.empId
	Left Join tbl_leavegrouplog elg on b.empId=es.empId
	Left Join ".TABLE_RULE." r on es.leaveGroup=r.leaveGroup
	  WHERE b.empId=".$eid." ".$addsql." and  b.leaveType=r.leaveType";
	
	  }else{
	
  $sql = "select b.empId, b.id, b.leaveType,b.opening,b.balanceLeave, es.leaveGroup, r.leaveType, r.noOfleave from tbl_emp_leave_balance b
	Left Join ".TABLE_SERVICE." es on b.empId=es.empId
	Left Join ".TABLE_RULE." r on es.leaveGroup=r.leaveGroup
	  WHERE b.empId=".$eid." ".$addsql." and  b.leaveType=r.leaveType";
	  }
	
	return $result = $this->db->query($sql)->result_array();
	
	//pre($result);die;
	}
	function empavailableLeave($eid,$year = '')
	{ 
	if($year != ''){	
		if($year == date("Y")){
			$leaveBalTable = 'tbl_emp_leave_balance';
		} else {
			$leaveBalTable = 'tbl_emp_leave_balance_'.$year;		
		}
	} else {
		$leaveBalTable = 'tbl_emp_leave_balance';
		$year = date("Y");
	}	
  
	 $current = date('Y-m');	 
	 $sql ="select b.empId, b.id, b.leaveType,r.description, b.balanceLeave,b.opening,b.el_donated, es.leaveGroup, r.leaveType, r.noOfleave from $leaveBalTable b
	 Left Join ".TABLE_SERVICE." es on b.empId=es.empId
	 Left Join ".TABLE_RULE." r on es.leaveGroup=r.leaveGroup
	 WHERE b.empId=".$eid." ".$addsql." and  b.leaveType=r.leaveType";

	  ######## For Getting Employee Gender and material status #########
	  $this->db->select('emp.empDOJ,emp.empgender,ep.empGender,eg.grade,ep.empMaritalStatus,TIMESTAMPDIFF(MONTH, emp.empDOJ, now()) noofmonth,TIMESTAMPDIFF(YEAR, emp.empDOJ, now()) noofyear');
	  $this->db->from('tbl_emp_master emp');
	  $this->db->join('tbl_emp_personal ep', 'emp.empId = ep.empId');
	  $this->db->join('employees_grade eg', 'eg.empId = ep.empId','left');
	  $this->db->where('ep.empId',$eid);
	  $empresult = $this->db->get();
	   $data = $empresult->result_array();
     
     // print_r($this->db->last_query());
     // die();

	 ######## For Special Leave count #########
     $this->db->select('id,total_leave as opening,leave_type as leaveType,description');
     $query = $this->db->get('special_leave');
     $specialLeave =   $query->result_array();
     $totalPL =  $specialLeave[0]['opening'];
     $totalML =  $specialLeave[1]['opening'];
     $totalMSL =  $specialLeave[2]['opening'];
  
   
	$RSQl='SELECT SUM(CASE WHEN `leaveType`="SL" THEN `noofdays` end  ) as countSL,SUM(CASE WHEN `leaveType`="CL" THEN `noofdays` end  ) as countCL,SUM(CASE WHEN `leaveType`="EL" THEN `noofdays` end  ) as countEL,SUM(CASE WHEN `leaveType`="PL" THEN `noofdays` end  ) as countPL,SUM(CASE WHEN `leaveType` = "ML" THEN `noofdays` end  ) as countML, SUM(CASE WHEN `leaveType`="LSL"  THEN `noofdays` end  ) as countMSL from `tbl_regularization` WHERE  (`status` ="A" || (status="P" and cancelled_status=2) )  and `requestFrom`='.$eid.' and Year(fromDate)="'.$year.'" and Year(regularizationDate)="'.$year.'"';
	    $resultR = $this->db->query($RSQl)->result_array(); 
	    // print_r($resultR);

	    $currentY = date('Y');
		$currentM = date('m');
			
		// $mslcount = $this->db->query("SELECT sum(noofdays)  as noofdays FROM tbl_regularization WHERE (requestFrom = ?) AND (leaveType = ?) AND (status = ? ) AND  (YEAR(fromDate)=? AND MONTH(fromDate)=?)", array($eid,"MSL","A",$currentY,$currentM))->result_array();

		$mslcount = $this->db->query("SELECT sum(CASE WHEN `leaveType`= 'SBL'  THEN `noofdays` end )  as SBLnoofdays, sum( CASE WHEN `leaveType`= 'LSL' AND (YEAR(fromDate)= '".$currentY."' AND MONTH(fromDate)= '".$currentM."')  THEN `noofdays` end )  as MSLnoofdays FROM tbl_regularization WHERE (requestFrom = ?)  AND (status = ? ) ", array($eid,"A"))->result_array();
		

		 // $getprevdata=$this->db->query('Select sum(noofdays)  as noofdays, fromDate,toDate from tbl_regularization where requestFrom="'.$this->session->userdata('admin_id').'" AND leaveType="'.$leaveType.'"')->row_array();

		// print_r($mslcount[0]['SBLnoofdays']);
		// die();
		

	   
		
	$result = $this->db->query($sql)->result_array(); 
// 	echo "<br>";
// print_r($result);
	
  
	     $result[0]['balanceLeave']=$result[0]['opening']-$resultR[0]['countCL'];
	     $result[1]['balanceLeave']=$result[1]['opening']-$resultR[0]['countSL'];
		 $result[2]['balanceLeave']=$result[2]['opening']-($resultR[0]['countEL'] + $result[2]['el_donated']);

		 $result[0]['appliedLeave']= $resultR[0]['countCL'];
	     $result[1]['appliedLeave']= $resultR[0]['countSL'];
		 $result[2]['appliedLeave']= $resultR[0]['countEL'];

		 $result[2]['el_donated']= $result[2]['el_donated']; 
		 // For PL 
		 if($data[0]['empgender'] == "Male" && $data[0]['empMaritalStatus'] == "Married"){
		 $result[3]['empId'] = $eid;
		 $result[3]['id'] = $specialLeave[0]['id'];
		 $result[3]['leaveType'] = $specialLeave[0]['leaveType'];
		 $result[3]['description'] = $specialLeave[0]['description'];
		 $result[3]['balanceLeave'] = $totalPL-$resultR[0]['countPL'];
		 $result[3]['opening'] = $totalPL;
		 $result[3]['appliedLeave']= $resultR[0]['countPL'];
		 $result[3]['leaveGroup'] = $result[0]['leaveGroup'];
		 $result[3]['noOfleave'] =  $totalPL;
		}else if($data[0]['empgender'] == "Female" or $data[0]['empGender'] == "Female" ){
				$this->db->select('id,total_leave as opening,leave_type as leaveType,description');
				$this->db->where('marital_status',$data[0]['empMaritalStatus']);
				$this->db->where('leave_type','LSL');
			     $query = $this->db->get('special_leave');
			     $LSL =   $query->result_array();
			     // print_r($LSL);

			     $result[3]['empId'] = $eid ;
				 $result[3]['id'] =  "";
				 $result[3]['leaveType']   = $LSL[0]['leaveType'];
				 $result[3]['description']  = $LSL[0]['description'];
				 // $result[3]['balanceLeave'] = $LSL[0]['opening'] - $mslcount[0]['MSLnoofdays']; 
				 $result[3]['balanceLeave'] = $LSL[0]['opening'] * 12 - $resultR[0]['countMSL']; 
				 //$result[3]['appliedLeave'] =  $mslcount[0]['MSLnoofdays'];
				 $result[3]['appliedLeave'] =  $resultR[0]['countMSL'];
				 $result[3]['opening']      = $LSL[0]['opening'] * 12;
				 $result[3]['leaveGroup']   = $result[0]['leaveGroup'];
				 $result[3]['noOfleave']    =  $LSL[0]['opening'] * 12;
		

				 if($data[0]['noofmonth'] >'4'){
				 $this->db->select('id,total_leave as opening,leave_type as leaveType,description,pre_maternity,post_maternity');
				 $this->db->where('marital_status',$data[0]['empMaritalStatus']);
				 $this->db->where('leave_type','ML');
			     $query = $this->db->get('special_leave');
			     $ML =   $query->result_array();
			     // print_r($this->db->last_query());
			     // print_r($ML);
			        if(!empty($ML)){
					 $result[4]['empId'] = $eid ;
					 $result[4]['id'] =  $ML[0]['id'];
					 $result[4]['leaveType'] = $ML[0]['leaveType'];
					 $result[4]['description']  = $ML[0]['description'];
					 $result[4]['balanceLeave'] = $ML[0]['opening']-$resultR[0]['countML']; 
					 $result[4]['appliedLeave'] =  $resultR[0]['countML'];
					 $result[4]['opening']     = $ML[0]['opening'];
					 $result[4]['leaveGroup']   =$result[0]['leaveGroup'];
					 $result[4]['noOfleave']  = $ML[0]['opening'];

					 // $result[5]['empId'] = $eid ;
					 // $result[5]['id'] =  $ML[0]['id'];
					 // $result[5]['leaveType']    = "PSML";
					 // $result[5]['description']  = "Post-Maternity";
					 // $result[5]['balanceLeave'] = $ML[0]['post_maternity']-$resultR[0]['countPSML']; 
					 // $result[5]['appliedLeave'] =  $resultR[0]['countPSML'];
					 // $result[5]['opening']     = $ML[0]['post_maternity'];
					 // $result[5]['leaveGroup']   =$result[0]['leaveGroup'];
					 // $result[5]['noOfleave']  = $ML[0]['post_maternity'];
					}
				 }
				 $grade = $data[0]['grade'];
				 $years =  $data[0]['noofyear'];
				 $multipleWhere = ['from_year <=' => $years, 'to_year >' => $years];
				 $this->db->select('id,total_leave as opening,leave_type as leaveType,description');
				 $this->db->where('leave_type','SBL');
				 $this->db->where('marital_status',$data[0]['empMaritalStatus']);
				  $this->db->where("FIND_IN_SET('$grade',emp_grade) !=", 0);
				 $this->db->where($multipleWhere);
			     $query = $this->db->get('special_leave');
			     $SBL =   $query->result_array();
			        if(!empty($SBL)){
			         $result[5]['empId'] = $eid ;
					 $result[5]['id'] =  "";
					 $result[5]['leaveType'] = $SBL[0]['leaveType'] ;
					 $result[5]['description'] = $SBL[0]['description'] ;
					 $result[5]['balanceLeave']  = $SBL[0]['opening'] -  $mslcount[0]['SBLnoofdays'] ;
					 $result[5]['appliedLeave'] =   $mslcount[0]['SBLnoofdays'];
					 $result[5]['opening'] = $SBL[0]['opening'];
					 $result[5]['leaveGroup'] =$result[0]['leaveGroup'];
					 $result[5]['noOfleave'] = $SBL[0]['opening'];
					}	
			}	

		
	   return $result;
	   //return  $sql;
	}
	
/*function empavailableLeave($eid)
	{
	
	   $sql ="select b.empId, b.id, b.leaveType, b.balanceLeave, es.leaveGroup, r.leaveType, r.noOfleave from tbl_emp_leave_balance b
	Left Join ".TABLE_SERVICE." es on b.empId=es.empId
	Left Join ".TABLE_RULE." r on es.leaveGroup=r.leaveGroup
	  WHERE b.empId=".$eid." ".$addsql." and  b.leaveType=r.leaveType";
	  
	  $RSQl='SELECT SUM(CASE WHEN `leaveType`="SL" THEN `noofdays` end  ) as countSL,SUM(CASE WHEN `leaveType`="CL" THEN `noofdays` end  ) as countCL,SUM(CASE WHEN `leaveType`="EL" THEN `noofdays` end  ) as countEL  from `tbl_regularization` WHERE `status` ="A" and `requestFrom`=20000499';
	  return $result = $this->db->query($sql)->result_array();
	   return  $sql;
	}*/
	function leaveHistory()
	{
	 $sql ="select id, DATE_FORMAT(attendanceDate,'%d-%b-%Y') as attendanceDate from ".TABLE_ATTENDANCE." WHERE empId=".$this->session->userdata('admin_id')." and attendanceStatus='L' Order by attendanceDate DESC";
	 //$result = $this->db->query($sql)->result_array();pre($result);die;
	 return $result = $this->db->query($sql)->result_array();
	}

	function leaveTaken($ltype,$requestfrom)
	{
	 $sql ="select requestFrom as empId, sum(noofdays) as totalleave, leaveType from ".TABLE_REGULARIZATION." WHERE requestFrom='".$requestfrom."' and leaveType='".$ltype."'  and status='A'";
	 // $sql ="select requestFrom as empId, sum(noofdays) as totalleave, leaveType from ".TABLE_REGULARIZATION." WHERE requestFrom=".$this->session->userdata('admin_id')." and regularizationType='L' group by  leaveType ";
	 //$result = $this->db->query($sql)->result_array();pre($result);die;
	 return $result = $this->db->query($sql)->result_array();
	}
	
	function threadDetails($id)
	{
	 $sql ="select id,DATE_FORMAT(threadTime,'%d-%b-%Y') as date,DATE_FORMAT(threadTime,'%H:%i') as time, remarks from tbl_regularization_thread where regularizationId=".$id." order by id ASC";
	 //$result = $this->db->query($sql)->result_array();pre($result);die;
	 return $result = $this->db->query($sql)->result_array();
	}
	
	function regularizationType($a='')
	{
	return array('T'=>'Travel','FL'=>'Forgot Login','FLO'=>'Forgot Logout','FP'=>'Forgot Punch', 'M'=>'Meeting');
	}

	function get_emp_travel_details($type='',$id=0, $resultType='G')
	{
	 if($type=='T'){
	$addsql .= " AND a.requestTo=".$id."";
	}else{
	$addsql .= " AND a.requestFrom=".$id."";
	}
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		$sql = "select  a.remarks, a.status,a.id,DATE_FORMAT(a.fromDate,'%d-%b-%Y') as  fDate, DATE_FORMAT(a.toDate,'%d-%b-%Y') as  tDate, DATE_FORMAT(a.regularizationDate,'%d-%b-%Y %h:%i %p') requestDate,a.requestFrom,concat(e.empFname,' ',e.empLname) as empName, a.remarks, a.responceRemarks from ".TABLE_REGULARIZATION." a
		LEFT JOIN ".TABLE_EMP." e on a.requestFrom =e.empId
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
		LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
 		 where 1=1  and a.regularizationType='T' ".$addsql." ";
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
 
 function get_team_offday_details($resultType='G')
	{
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		
		
		$sql = "select a.remarks,a.status,a.id,DATE_FORMAT(a.fromDate,'%d-%b-%Y') as  fDate, DATE_FORMAT(a.toDate,'%d-%b-%Y') as  tDate, DATE_FORMAT(a.regularizationDate,'%d-%b-%Y %h:%i %p') rDate,a.requestFrom,concat(e.empFname,' ',e.empLname) as empName, a.regularizationType from ".TABLE_REGULARIZATION." a
		LEFT JOIN ".TABLE_EMP." e on a.requestFrom =e.empId
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
		LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
 		 where 1=1  and (a.regularizationType='DO' or a.regularizationType='WO' or a.regularizationType='CO') and a.requestTo=".$this->session->userdata('admin_id')."  ".$addsql." ";
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
 
  function get_emp_offday_details($resultType='G')
	{
	
	if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		$sql = "select a.remarks, a.status,a.id,DATE_FORMAT(a.fromDate,'%d-%b-%Y') as  fDate, DATE_FORMAT(a.toDate,'%d-%b-%Y') as  toDate, DATE_FORMAT(a.regularizationDate,'%d-%b-%Y %h:%i %p') rDate,a.requestFrom,concat(e.empFname,' ',e.empLname) as empName, a.regularizationType from ".TABLE_REGULARIZATION." a
		LEFT JOIN ".TABLE_EMP." e on a.requestFrom =e.empId
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
		LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
 		 where 1=1  and (a.regularizationType='DO' or a.regularizationType='WO' or a.regularizationType='CO') and a.requestFrom=".$this->session->userdata('admin_id')." ".$addsql." ";
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
 
  function get_report_state($resultType='G')
	{
		$addSql = "  ";

                $dateDiff = date('d',strtotime( $this->input->post('to'))-strtotime($this->input->post('from') ));
                
		if($this->input->post('from')!='')
		{
			$addSql .= " and a.attendanceDate>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and a.attendanceDate<='".$this->input->post('to')."' ";
		}

		$sql = "select s.State_Id, s.State_Name, "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='present' ".$addSql." ) as present, "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='Week Off' ".$addSql." ) as weekoff, ". 
"(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='Leave' ".$addSql." ) as lea, "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId like '%Other Off%' ".$addSql." ) as otheroff, "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId like 'Name of Training:%' ".$addSql." ) as training,  "
. " (select (count(empId)*".$dateDiff.") from tbl_emp_master e inner join tbl_mst_city c on c.cityId=e.jobLocation where c.state=s.State_Id)-("
                        . "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='present' ".$addSql." ) + "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='Week Off' ".$addSql." ) + ". 
"(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='Leave' ".$addSql." ) + "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId like '%Other Off%' ".$addSql." ) + "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId like 'Name of Training:%' ".$addSql." )   "
                        . ") as tot_emp "
. "from tbl_mst_state s ";
		
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
           function get_report_city($resultType='G')
	{
		$addSql = "  ";

                $dateDiff = date('d',strtotime( $this->input->post('to'))-strtotime($this->input->post('from') ));
                
		if($this->input->post('from')!='')
		{
			$addSql .= " and a.attendanceDate>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and a.attendanceDate<='".$this->input->post('to')."' ";
		}

		$sql = "select s.State_Id, s.State_Name, "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='present' ".$addSql." ) as present, "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='Week Off' ".$addSql." ) as weekoff, ". 
"(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='Leave' ".$addSql." ) as lea, "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId like '%Other Off%' ".$addSql." ) as otheroff, "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId like 'Name of Training:%' ".$addSql." ) as training,  "
. " (select (count(empId)*".$dateDiff.") from tbl_emp_master e inner join tbl_mst_city c on c.cityId=e.jobLocation where c.state=s.State_Id)-("
                        . "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='present' ".$addSql." ) + "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='Week Off' ".$addSql." ) + ". 
"(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId='Leave' ".$addSql." ) + "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId like '%Other Off%' ".$addSql." ) + "
. "(select count(a.empId) from tbl_emp_attendance a inner join tbl_mst_city c on c.cityId=a.cityId where c.state=s.State_Id and a.attendanceTypeId like 'Name of Training:%' ".$addSql." )   "
                        . ") as tot_emp "
. "from tbl_mst_state s ";
		
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
 function get_report_user_employee($resultType='G',$param1,$param2,$id="")
	{
            
		$addSql = "  ";

        $dateDiff = date('d',strtotime( $this->input->post('to'))-strtotime($this->input->post('from') ));
                
		if($this->input->post('from')!='')
		{
			$addSql .= " and a.attendanceDate>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and a.attendanceDate<='".$this->input->post('to')."' ";
		}
		if($id)
		{
		$addsql.="AND e.empId=".$id."";
		}
		// if(($this->session->userdata('role')!=1) && $this->session->userdata('role')!=5 && $this->session->userdata('role')!=4 && $this->session->userdata('role')!=3){
			// $addsql .= " AND  reportingTo=".$this->session->userdata('admin_id')."";
		// } 
         if($this->input->post('filters')!='') // search filters
		{
		    if(self::decodeFilters($this->input->post('filters')))
			{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		//$sql = "SELECT empId, group_concat(date_format(attendanceDate,'%d')) as d, GROUP_CONCAT(attendanceTypeId) as t FROM `tbl_emp_attendance` where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by empId  ";	
                
        $sql = "SELECT DAY(LAST_DAY(DATE_FORMAT(attendanceDate,'%Y%m%d'))) as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='A'  and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalleave,    concat(e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' ".$addsql."  group by e.empId ";
                
		//echo $sql; exit;
		if($resultType=='G')
		{
                    
			$result = parent::result_grid_array($sql);
                        $result->mdays = cal_days_in_month(CAL_GREGORIAN, $param1, $param2);
		}
		else
		{
			$result = $this->db->query($sql." order by empId ASC ")->result_array();
		}
                 
              //pre($result); die; 
		return $result;
	} 
	 /*  function get_report_user_employee($resultType='G',$param1,$param2,$id="")
	{
	   
		$addSql = "  ";
       
        $dateDiff = date('d',strtotime( $this->input->post('to'))-strtotime($this->input->post('from') ));
                
		if($this->input->post('from')!='')
		{
			$addSql .= " and attendanceDate>='".$param1."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and attendanceDate<='".$param2."' ";
		}
		
		if($id)
		{
		$addSql.="AND e.empId=".$id."";
		}
		if($this->input->post('filters')!='') // search filters
		{
		    if(self::decodeFilters($this->input->post('filters')))
			{
			$addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		//  if(($this->session->userdata('role')!=1) && $this->session->userdata('role')!=5 && $this->session->userdata('role')!=4 && $this->session->userdata('role')!=3){
			// $addsql .= " AND  reportingTo=".$this->session->userdata('admin_id')."";
		// } 

		//$sql = "SELECT empId, group_concat(date_format(attendanceDate,'%d')) as d, GROUP_CONCAT(attendanceTypeId) as t FROM `tbl_emp_attendance` where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by empId  ";	
                
        //$sql = "SELECT DAY(LAST_DAY(DATE_FORMAT(attendanceDate,'%Y%m%d'))) as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='A'  and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalleave,    concat(e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' ".$addSql."  group by e.empId ";
        $sql = "SELECT DATEDIFF('".$param2."','".$param1."') as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LWP'  and e.empId=ea.empId    ".$addSql."  group by e.empId) as totalLWP, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A'  and e.empId=ea.empId ".$addSql." group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId ".$addSql." group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId ".$addSql." group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId ".$addSql." group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId ".$addSql." group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId ".$addSql." group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where (ea.attendanceStatus='A' )  and e.empId=ea.empId ".$addSql." group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId ".$addSql." group by e.empId) as totalleave,    concat(e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d%b')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where 1=1  and e.empId NOT LIKE '%100%'  ".$addSql."  group by e.empId ";
                
		//echo $sql; exit;
		if($resultType=='G')
		{
                    
			$result = parent::result_grid_array($sql);
            $result->mdays = cal_days_in_month(CAL_GREGORIAN, $param1, $param2);
		}
		else
		{
			$result = $this->db->query($sql." order by empId ASC ")->result_array();
		}
                 
              //pre($result); die; 
		return $result;
	} */
   //5-sep-18    
    function get_report_user($resultType='G',$param1,$param2,$id="")
	{
		$addSql = "  ";
       
        $dateDiff = date('d',strtotime( $this->input->post('to'))-strtotime($this->input->post('from') ));
                
		if($this->input->post('from')!='')
		{
			$addSql .= " and attendanceDate>='".$param1."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and attendanceDate<='".$param2."' ";
		}
		
		if($id)
		{
		$addsql.="AND e.empId=".$id."";
		}
		if($this->input->post('filters')!='') // search filters
		{
		    if(self::decodeFilters($this->input->post('filters')))
			{
			$addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		/* if(($this->session->userdata('role')!=1) && $this->session->userdata('role')!=5 && $this->session->userdata('role')!=4 && $this->session->userdata('role')!=3){
			$addsql .= " AND  reportingTo=".$this->session->userdata('admin_id')."";
		} */

		//$sql = "SELECT empId, group_concat(date_format(attendanceDate,'%d')) as d, GROUP_CONCAT(attendanceTypeId) as t FROM `tbl_emp_attendance` where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by empId  ";	
                
        //$sql = "SELECT DAY(LAST_DAY(DATE_FORMAT(attendanceDate,'%Y%m%d'))) as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='A'  and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalleave,    concat(e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' ".$addsql."  group by e.empId ";
	   
		$stateIds =   $this->stateRolePermission($this->session->userdata('admin_id'));	    
		if($stateIds['stateIds']){
			$commaSepState   =   $stateIds['stateIds'];
			$stateCond       =  " and e.jobLocation in (".$commaSepState.")";      
		} else {
            $stateCond = '';
		}
		
		$sql = "SELECT d.name,DATEDIFF('".$param2."','".$param1."') as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LWP'  and e.empId=ea.empId    ".$addSql."  group by e.empId) as totalLWP, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A'  and e.empId=ea.empId ".$addSql." group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId ".$addSql." group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId ".$addSql." group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId ".$addSql." group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId ".$addSql." group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId ".$addSql." group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where (ea.attendanceStatus='A' )  and e.empId=ea.empId ".$addSql." group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId ".$addSql." group by e.empId) as totalleave,    concat(e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d%b')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId 
		 Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id	
		where 1=1 $stateCond and e.empId NOT LIKE '%100%'  ".$addSql."  group by e.empId ";
                
		//echo $sql; exit;
		if($resultType=='G')
		{
                    
			$result = parent::result_grid_array($sql);
            $result->mdays = cal_days_in_month(CAL_GREGORIAN, $param1, $param2);
		}
		else
		{
			$result = $this->db->query($sql." order by empId ASC ")->result_array();
		}
                 
              // pre($result); die; 
		return $result;
	}
   //5-sep-18    
function getleaveTravelApprove($date)
{
$sql =" select *  from ".TABLE_REGULARIZATION." where fromDate <='".$date."' and toDate >='".$date."' and status='A'  group by requestFrom";

return $result = $this->db->query($sql)->result_array();
}

function getleaveOfEmp($date,$empId)
{
   $sql =" select *  from ".TABLE_REGULARIZATION." where fromDate <='".$date."' and toDate >='".$date."' and status !='R' and requestFrom='".$empId."' ";

 return $result = $this->db->query($sql)->result_array();
}

 //5-sep-18
 function getmonthlyLevaeTravelApprove($start, $end)
{
 $sql =" select *  from ".TABLE_REGULARIZATION." where fromDate >='".$start."' and toDate <='".$end."' and status='A'";
return $result = $this->db->query($sql)->result_array();
}
function gettodayAttendance($date, $empId)
	{
	 $sql ="select * from ".TABLE_ATTENDANCE."  where empId=".$empId." and attendanceDate='".$date."'";
	 $result = $this->db->query($sql)->result_array();
	 return $result['0'];
	 }
	 //attendance log new 4-7-18
	 function get_details($resultType='G',$id="")
	   {
		$addSql = "  ";
		$keyword = trim($this->input->post('keyword'));
		if($this->input->post('filters')!='')
		{
		
		if(self::decodeFilters($this->input->post('filters')))
		{ 
			$addSql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
			/* pre($addSql);
			die; */
		}
		if($this->input->post('from')!='')
		{
			$addSql .= " and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d')>='".filter_date($this->input->post('from'))."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d')<='".filter_date($this->input->post('to'))."' ";
		}
		//nw 21-july-18
		if($id)
		{
		   $addSql .=" and a.empId='".$id."' ";
		}
		
        $stateIds =   $this->stateRolePermission($this->session->userdata('admin_id'));
	    
		if($stateIds['stateIds']){
			$commaSepState   =   $stateIds['stateIds'];   
			$stateCond       =  " and e.jobLocation in (".$commaSepState.")";      
		} else {
            $stateCond = '';
		}
		//nw 21-july-18
		 $sql = "select c.cityName,DATE_FORMAT(e.empDOJ,'%d-%b-%Y') as DOJ, ed.name as dept_name,GROUP_CONCAT(a.attLocTyp order by a.attendanceDatetime asc) as attLocTyp, DATE_FORMAT(a.attendanceDatetime,'%d %M %Y') as date, e.empId,CONCAT(e.empFname,'  ',e.empLname)as emp_name,GROUP_CONCAT(a.lat order by a.attendanceDatetime asc) as lat,GROUP_CONCAT(a.log order by a.attendanceDatetime asc) as log,GROUP_CONCAT(a.image order by a.attendanceDatetime asc) as image,r.id as region,a.source, a.attendanceDatetime, a.empId, DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') as attendanceDate, min(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as inTime,  max(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as outTime, TIME_FORMAT(TIMEDIFF(max(a.attendanceDatetime),min(a.attendanceDatetime)),'%H:%i') as workingHours, es.shift as servicesShift, ed.shift as departmentShift   from rowdata a
		LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
		LEFT JOIN ".TABLE_SERVICE." es on e.empId=es.empId
		LEFT JOIN ".TABLE_DEPT." ed on e.empDept=ed.id
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
		LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
 		 where 1=1  ".$addSql." $stateCond AND e.empId NOT LIKE '%100%'  and a.empId=e.empId group by a.empId,date(a.attendanceDatetime) ";

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
	 //attendance log new 4-7-18
	 //team attendance log
	function get_team_details($resultType='G',$id="")
	{
		$addSql = "  ";
		$keyword = trim($this->input->post('keyword'));
		/* pre($this->input->post('filters'));
		die; */
		if($this->input->post('filters')!='') // search filters
		{
		   if(self::decodeFilters($this->input->post('filters')))
			{ 
			$addSql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		/* echo $addSql;
		die; */
		if($this->input->post('from')!='')
		{
			$addSql .= " and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d')>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d')<='".$this->input->post('to')."' ";
		}
		//nw 21-july-18
		if($id)
		{
		   $addSql .=" and e.reportingTo='".$id."' ";
		}
		//nw 21-july-18
		$sql = "select DATE_FORMAT(a.attendanceDatetime,'%d %M %Y') as date,e.empId,CONCAT(e.empFname,'  ',e.empLname)as emp_name,GROUP_CONCAT(a.lat order by a.attendanceDatetime asc) as lat,GROUP_CONCAT(a.log order by a.attendanceDatetime asc) as log,GROUP_CONCAT(a.image order by a.attendanceDatetime asc) as image,r.id as region,a.source, a.attendanceDatetime, a.empId, DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') as attendanceDate, min(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as inTime,  max(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as outTime, TIME_FORMAT(TIMEDIFF(max(a.attendanceDatetime),min(a.attendanceDatetime)),'%H:%i') as workingHours, es.shift as servicesShift, ed.shift as departmentShift ,a.attLocTyp  from rowdata a
		LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
		LEFT JOIN ".TABLE_SERVICE." es on e.empId=es.empId
		LEFT JOIN ".TABLE_DEPT." ed on e.empDept=ed.id
		LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
		LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
		LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
 		 where 1=1  ".$addSql."  and a.empId=e.empId group by a.empId,date(a.attendanceDatetime)";
		/* echo $sql;die; */
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
	 //team attendance log
	 //team monthly observation
	 function get_teammonthly_attendance($resultType='G',$param1,$param2,$id="")
	{
            
		$addSql = "  ";

        $dateDiff = date('d',strtotime( $this->input->post('to'))-strtotime($this->input->post('from') ));
                
		if($this->input->post('from')!='')
		{
			$addSql .= " and a.attendanceDate>='".$this->input->post('from')."' ";
		}
		if($this->input->post('to')!='')
		{
			$addSql .= " and a.attendanceDate<='".$this->input->post('to')."' ";
		}
		if($id)
		{
			$addsql .= " AND  reportingTo=".$this->session->userdata('admin_id')."";
		}
		/* if(($this->session->userdata('role')!=1) && $this->session->userdata('role')!=5 && $this->session->userdata('role')!=4 && $this->session->userdata('role')!=3){
			$addsql .= " AND  reportingTo=".$this->session->userdata('admin_id')."";
		} */

		//$sql = "SELECT empId, group_concat(date_format(attendanceDate,'%d')) as d, GROUP_CONCAT(attendanceTypeId) as t FROM `tbl_emp_attendance` where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by empId  ";	
                
        //$sql = "SELECT DAY(LAST_DAY(DATE_FORMAT(attendanceDate,'%Y%m%d'))) as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A' and ea.attendanceStatus !='L' and ea.attendanceStatus !='LWP' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where (ea.attendanceStatus='A' or ea.attendanceStatus='LWP') and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalleave,    concat(e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' ".$addsql."  group by e.empId ";
          $sql = "SELECT DAY(LAST_DAY(DATE_FORMAT(attendanceDate,'%Y%m%d'))) as workingday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus !='A' and ea.attendanceStatus !='L' and ea.attendanceStatus !='LWP' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='R' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as resign,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus ='NJ' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as newjoin,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfday,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HL' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalhalfdayleave,(select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='LC' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as latecoming , (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where (ea.attendanceStatus='A' or ea.attendanceStatus='LWP') and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId ) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId and DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId) as totalleave,    concat(e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d')) as d, max(date_format(attendanceDate,'%d')) as maxday,  GROUP_CONCAT(a.attendanceStatus) as t, GROUP_CONCAT(DATE_FORMAT(a.inTime,'%H:%i:%s')) as ti, GROUP_CONCAT(DATE_FORMAT(a.outTime,'%H:%i:%s')) as outTime, GROUP_CONCAT(DATE_FORMAT(a.workingHours,'%H:%i')) as workingHours FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' ".$addsql."  group by e.empId ";
      
		//echo $sql; exit;
		if($resultType=='G')
		{
                    
			$result = parent::result_grid_array($sql);
                        $result->mdays = cal_days_in_month(CAL_GREGORIAN, $param1, $param2);
		}
		else
		{
			$result = $this->db->query($sql." order by empId ASC ")->result_array();
		}
                 
              //pre($result); die; 
		return $result;
	}
	
	
function availableLeavePrevious($eid, $ltype='', $noOfleave='')
	{
	if($ltype){
	$addsql =" and b.leaveType='".$ltype."'";
	}
	$sql1  ="select effectiveDate,leaveGroup from ".TABLE_SERVICE." where empId='".$eid."'";
	$result1 = $this->db->query($sql1)->result_array();
	
	if($result1['0']['effectiveDate'] >= date('Y-m-d')){
	 $sql ="select b.empId, b.id, b.leaveType, b.balanceLeave, elg.leaveGroup, b.opening,r.leaveType, r.noOfleave from tbl_emp_leave_balance_".date("Y",strtotime("-1 year"))." b
	Left Join ".TABLE_SERVICE." es on b.empId=es.empId
	Left Join tbl_leavegrouplog elg on b.empId=es.empId
	Left Join ".TABLE_RULE." r on es.leaveGroup=r.leaveGroup
	  WHERE b.empId=".$eid." ".$addsql." and  b.leaveType=r.leaveType";
	
	  }else{
	
  $sql ="select b.empId, b.id, b.leaveType, b.balanceLeave, b.opening, es.leaveGroup, r.leaveType, r.noOfleave from tbl_emp_leave_balance_".date("Y",strtotime("-1 year"))." b
	Left Join ".TABLE_SERVICE." es on b.empId=es.empId
	Left Join ".TABLE_RULE." r on es.leaveGroup=r.leaveGroup
	  WHERE b.empId=".$eid." ".$addsql." and  b.leaveType=r.leaveType";
	  }
	
	return $result = $this->db->query($sql)->result_array();
	
	//pre($result);die;
	}
	
	
function countApprovLeaves($empId,$leaveType,$id=NULL)
	{
		
	$getprevdata=$this->db->query('Select sum(noofdays) as noofdays  from tbl_regularization where requestFrom="'.$empId.'" AND leaveType="'.$leaveType.'" AND status="A"  AND id!="'.$id.'" and YEAR(fromDate)="'.date('Y').'"')->row_array();
	return $getprevdata; 
	}
	
function countPreApprovLeaves($empId,$leaveType,$id=NULL)
	{	
	$getprevdata=$this->db->query('Select sum(noofdays) as noofdays  from tbl_regularization where requestFrom="'.$empId.'" AND leaveType="'.$leaveType.'" AND status="A" AND id!="'.$id.'" and YEAR(fromDate)="'.date("Y",strtotime("-1 year")).'"')->row_array();
	
	return $getprevdata; 
	}
	

	function checkPerformance($fromdate,$empId)
	{
		$fromdateYear = date('Y',strtotime($fromdate));
		$oneyearBefore = date('Y',strtotime($fromdate . "last year"));
		$twoyearBefore = date('Y',strtotime("-2 year", strtotime($fromdate)));
		$duration  = $oneyearBefore."-".$fromdateYear;
		$duration1 = $twoyearBefore."-".$oneyearBefore;
		$year_duration = array($duration1,$duration);
        $this->db->select("krm.quarter,krm.id,AVG(kra.manager_tot_weightage) as per_avg");
        $this->db->from("tbl_kra_mst krm");
        $this->db->join('tbl_kra_emp kra','kra.quarter_id = krm.id');
        $this->db->where_in('krm.financial_year',$year_duration);
        $this->db->where('kra.empId',$empId);
        $query = $this->db->get();
        $performance  = $query->result_array();
        
        // echo $this->db->last_query();
        return $performance;
        // print_r($performance);

        // die();


	}

	function getRegularizeAttendance($attendance_id)
	{
		$sql ="select * from ".TABLE_REGULARIZATION."  where parentId=".$attendance_id." ";
	    $result = $this->db->query($sql)->result_array();
	     return $result['0'];
	}

	function getEmpleave($date,$empId)
	{
	   $sql =" select *  from ".TABLE_REGULARIZATION." where fromDate <='".$date."' and toDate >='".$date."' and requestFrom='".$empId."' and parentId = '0' order by id DESC ";

	 $result = $this->db->query($sql)->result_array();
	 return $result[0];
	}

	
   function stateRolePermission($empId){
		  
	$this->db->select("GROUP_CONCAT(stateId SEPARATOR ',') as stateIds",FALSE); 
    $this->db->from('employee_state_wise_permission');
	$this->db->where('empId', $empId);
	$this->db->where('type', 2);
    $query  =  $this->db->get(); 
	$result =  $query->row_array();
	return $result;
   }


     ######## For Getting Employee Gender and material status, no of years #########
     function empGrade($empId){
     $this->db->select('emp.empDOJ,emp.empgender,eg.grade,ep.empMaritalStatus,TIMESTAMPDIFF(MONTH, emp.empDOJ, now()) noofmonth,TIMESTAMPDIFF(YEAR, emp.empDOJ, now()) noofyear');
	  $this->db->from('tbl_emp_master emp');
	  $this->db->join('tbl_emp_personal ep', 'emp.empId = ep.empId');
	  $this->db->join('employees_grade eg', 'eg.empId = ep.empId');
	  $this->db->where('ep.empId',$empId);
	  $empresult = $this->db->get();
	   $data = $empresult->result_array();
	   return $data;
     }

     ##########for getting employee special leave
     function specialLeaveTotal($leaveType='',$maritalStatus = '',$child = '',$empId = '')
     {
     	if($leaveType == "SBL")
     	{
     		
     		$this->db->select('emp.empDOJ,eg.grade,ep.empMaritalStatus,TIMESTAMPDIFF(YEAR, emp.empDOJ, now()) noofyear');
			  $this->db->from('tbl_emp_master emp');
			   $this->db->join('tbl_emp_personal ep', 'emp.empId = ep.empId');
			  $this->db->join('employees_grade eg', 'eg.empId = emp.empId');
			  $this->db->where('emp.empId',$empId);
			  $empresult = $this->db->get();
			   $data = $empresult->result_array();
			   if($maritalStatus == '0'){
			   	   $maritalStatus = $data[0]['empMaritalStatus'];
			   }

			  
     		 $grade = $data[0]['grade'];
		     $years =  $data[0]['noofyear'];
			 $multipleWhere = ['from_year <=' => $years, 'to_year >' => $years];
			 $this->db->select('id,total_leave as opening,leave_type as leaveType,description');
			 $this->db->where('leave_type','SBL');
			 $this->db->where('marital_status',$maritalStatus);
			  $this->db->where("FIND_IN_SET('$grade',emp_grade) !=", 0);
			 $this->db->where($multipleWhere);
		     $query = $this->db->get('special_leave');
		     $SBL   = $query->result_array();

		     return $SBL;

     	}else{
     		
     		if($maritalStatus == '0' && $empId != '' ){
     			 $this->db->select('ep.empMaritalStatus');
				  $this->db->from('tbl_emp_master emp');
				  $this->db->join('tbl_emp_personal ep', 'emp.empId = ep.empId');
				  $this->db->where('emp.empId',$empId);
				  $empresult = $this->db->get();
			      $data = $empresult->result_array();
			      $maritalStatus = $data[0]['empMaritalStatus'];
			      
     		}

     		$this->db->select('id,total_leave as opening,leave_type as leaveType,pre_maternity,post_maternity');
	      $this->db->where('leave_type',$leaveType);
	      $this->db->where('marital_status',$maritalStatus);
	      if($child != '' and $leaveType != "LSL" ){
	      	$child = ''.$child .'';
	      	 $this->db->where('child',$child);
	      }
	      $query = $this->db->get('special_leave');
	      $getspecialleave =  $query->result_array();
	       // print_r($this->db->last_query());
	      return $getspecialleave;
     	}
     	
     }

     function Last_PL($empId,$leaveType)
     {
     	$lastPL = $this->db->query('Select * from tbl_regularization where requestFrom="'.$empId.'"  AND status!="R" AND leaveType = "'.$leaveType.'" ORDER BY id ASC LIMIT 1')->result_array();
     	return $lastPL[0];
     	//echo $this->db->last_query();
     }

     function Last_SBL($empId,$leaveType)
     {
     	$lastPL = $this->db->query('Select * from tbl_regularization where requestFrom="'.$empId.'"  AND status!="R" AND leaveType = "'.$leaveType.'" ORDER BY id DESC LIMIT 1')->result_array();
     	
     	//echo $this->db->last_query();
     	return $lastPL[0];
     }
     

	


}
