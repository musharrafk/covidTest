<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class master_model extends parent_model {
		var $base_tbl = TABLE_LEAVEGROUP;
		var $u_column = 'id';
		
		function get_emp_attendance_details($id=0, $resultType='G')
		{
			if($id){
				$addsql .= " AND a.empId=".$id."";
			}
			if($this->input->post('filters')!='') // search filters
			{
				$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
			$region = explode(',',$this->session->userdata('admin_region')); 
			if($region['0']){
				$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
			}
			$sql = "select a.*, DATE_FORMAT(a.attendanceDate,'%d %b, %Y') as  attendanceDate, DATE_FORMAT(a.inTime,'%H:%i:%s') as inTime, DATE_FORMAT(a.outTime,'%H:%i:%s') as outTime,  DATE_FORMAT(a.workingHours,'%H:%i') as workingHours, DATE_FORMAT(a.inTimediff,'%H:%i') as inTimediff, DATE_FORMAT(a.outTimediff,'%H:%i') as outTimediff, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName from ".TABLE_ATTENDANCE." a
			LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
			LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
			LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
			LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
			where 1=1 ".$addsql." ";
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
		function get_asset_list()
		{
			$sql="select id, name from tbl_mst_asset where 1=1 ";
			return $this->db->query($sql)->result_array();
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
			$sql = "select id, name from ".TABLE_LEAVETYPE." where 1=1 ";
			//echo $sql; 
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
		
		function get_leave_details($id=0, $resultType='G')
		{
			if($id> 0 )
			{
				$addsql .= " and id=".$id;
			}
			if($this->input->post('filters')!='') // search filters
			{
				$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
			$sql = "select l.*,g.name as leave_group,lt.name as leaveType  from ".TABLE_LEAVE." l Left Join ".$this->base_tbl." g on g.id=l.leaveGroup Left Join tbl_mst_leavetype lt  on l.leaveType=lt.id  where 1=1 ";
			//echo $sql; 
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
		function get_weekoff_details($id=0, $resultType='G')
		{
			if($id> 0 )
			{
				$addsql .= " and id=".$id;
			}
			if($this->input->post('filters')!='') // search filters
			{
				$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
			$sql = "select w.*,d.name as department  from ".TABLE_WEEKOFF." w left join ".TABLE_DEPT." d on w.deptId=d.id where 1=1 ";
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
			if($this->input->post('from')!='')
			{
				$addSql .= " and r.attendanceDatetime >='".$this->input->post('from')."' ";
			}
			
			if($this->input->post('to')!='')
			
			{
				
				$addSql .= " and r.attendanceDatetime <='".$this->input->post('to')."' ";
				
			}
			
			
			
			
			
			$sql = "select r.id, r.empId, DATE_FORMAT(r.attendanceDatetime,'%d-%b-%Y') as attendancedate, DATE_FORMAT(r.attendanceDatetime,'%H:%i:%s') as attendancetime, concat(e.empFname,' ',e.empLname) as empName from rowdata r
			
			Left Join ".TABLE_EMP." e on r.empId=e.empId
			
			where 1=1 ".$addSql."";
			
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
			
		}	function insert_in_emp_daily_attendance($date)
		
		{
			$sql = "select r.id as region,a.source, a.attendanceDatetime, a.empId, DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d') as attendanceDate, min(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as inTime,  max(DATE_FORMAT(a.attendanceDatetime,'%H:%i:%s')) as outTime, DATE_FORMAT(TIMEDIFF(max(a.attendanceDatetime),min(a.attendanceDatetime)),'%H:%i') as workingHours, s.shiftTimeFrom, TIMEDIFF(min(DATE_FORMAT(a.attendanceDatetime,'%H:%i')), DATE_FORMAT(s.shiftTimeFrom,'%H:%i')) as inTimeDiff, TIMEDIFF(max(DATE_FORMAT(a.attendanceDatetime,'%H:%i')), DATE_FORMAT(s.shiftTimeTo,'%H:%i')) as outTimeDiff, s.shiftName as shift from rowdata a
			
			LEFT JOIN ".TABLE_SERVICE." es on a.empId=es.empId
			
			LEFT JOIN ".TABLE_SHIFT." s on es.shift = s.id
			
			LEFT JOIN ".TABLE_EMP." e on a.empId =e.empId
			
			LEFT JOIN ".TABLE_CITY." c on e.jobLocation=c.cityId
			
			LEFT JOIN ".TABLE_STATE." sa on c.state=sa.State_Id
			
			LEFT JOIN ".TABLE_REGION." r on sa.region=r.id
			
			where 1=1 and  DATE_FORMAT(a.attendanceDatetime,'%Y-%m-%d')='".$date."'   group by a.empId";
			
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
			$sql ="select r.id, r.parentId, r.requestFrom, r.requestTo, a.workingHours, a.attendanceStatus, DATE_FORMAT(a.inTime,'%H:%i') as inTime, DATE_FORMAT(a.outTime,'%H:%i') as outTime, a.attendanceDate, r.remarks,e.empEmailPersonal, e.empEmailOffice, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName from ".TABLE_REGULARIZATION." r
			LEFT JOIN ".TABLE_ATTENDANCE." a on r.parentId=a.id
			LEFT JOIN ".TABLE_EMP." e on r.requestFrom=e.empId
			where r.id = ".$id."";
			$result = $this->db->query($sql)->result_array();
			
			//pre($result);die;
			
			return $result;
		}
		function fetchAttendance()
		
		{
			
			$sql ="select empId,attendanceDate,inTime,outTime,source from ".TABLE_ATTENDANCE_TEMP."  where attendanceDate = CURDATE() - INTERVAL 1 DAY ";
			
			//$sql ="select empId,attendanceDate,inTime,outTime,Source from ".TABLE_ATTENDANCE_TEMP."  where attendanceDate = CURDATE() - INTERVAL 1 DAY ";
			
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
		
		function leaveTypeList()
		
		{
			
			$sql = "select id, name from ".TABLE_LEAVETYPE." where 1=1 order by name";
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
			
		}
		
		function leaveGroup()
		
		{
			
			$sql = "select id, name from ".TABLE_LEAVEGROUP." where 1=1 order by name";
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
			
		}
		function getLeaveList($id)
		
		{
			
			$sql = "select *,DATE_FORMAT(leaveFrom,'%d-%b-%Y')as leaveFrom,DATE_FORMAT(leaveTo,'%d-%b-%Y')as leaveTo from ".TABLE_EMP_LEAVE." where empId=".$id." order by Request_Date";
			
			$result = $this->db->query($sql)->result_array();
			
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
			
			where 	e.reportingTo=".$id." AND (regularizationStatus='0' or regularizationStatus='1') Order By a.attendanceDate,regularizationStatus ASC";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		
		function get_emp_regularization_request_details($id=0,$resultType='G')
		
		{
			$sql ="select r.parentId, r.id, r.requestFrom, r.regularizationType, DATE_FORMAT(r.regularizationDate,'%d-%b-%Y %H:%m:%i') as regularizationDate, r.remarks, r.status , DATE_FORMAT(a.attendanceDate,'%d-%b-%Y') attendanceDate, a.inTime, a.outTime, a.workingHours, a.attendanceStatus from ".TABLE_REGULARIZATION." r
			
			Left Join ".TABLE_ATTENDANCE." a on r.parentId = a.id
			
			where r.requestTo='".$id."' and r.regularizationType !='T'";
			
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
		function holidayList($cid=0,$lid=0,$date=0){
			
			if($date){
				
				$addsql =" AND holidayDate >'".$date."'";
				
			}
			
			$region = explode(',',$this->session->userdata('admin_region')); 
			
			if($region['0']){
				
				$addsql .=" AND region in(".$this->session->userdata('admin_region').")";
				
			}
			
			$sql ="select holiday,DATE_FORMAT(holidayDate,'%d-%b-%Y') date from ".TABLE_HOLIDAYS." where company=".$cid." ".$addsql." Order By holidayDate ASC";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		function empHolidayList($region=0){
			$sql ="select holiday,holidayDate from ".TABLE_HOLIDAYS." where region=".$region."  Order By holidayDate ASC";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		function todayempHolidayList($region=0,$date){
			$sql ="select holidayDate from ".TABLE_HOLIDAYS." where region=".$region." and holidayDate='".$date."'  Order By holidayDate ASC";
			
			$result = $this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		
		
		function empAvailableLeave($id)
		
		{
			
			$sql ="select l.Id,l.leaveType,l.noOfleave,lt.name as leaveType from ".TABLE_LEAVE." l
			
			LEFT JOIN ".TABLE_LEAVETYPE." lt on l.leaveType=lt.Id
			
			LEFT JOIN ".TABLE_SERVICE." s on l.leaveGroup = s.leaveGroup
			
			WHERE s.empId=".$this->session->userdata('admin_id')." Order By l.leaveType";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		
		function leaveTaken()
		
		{
			
			$sql ="select l.leaveType,l.noofDays,e.empDOJ from ".TABLE_EMP_LEAVE." l Left Join ".TABLE_EMP." e on l.empId=e.empId WHERE l.empId=".$this->session->userdata('admin_id')." AND l.status=1 Order by l.leaveType";
			
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
			
			return array('T'=>'Travel','L'=>'Late Coming','C'=>'Cumb-off');
			
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
			
			$sql = "select a.status,a.id,DATE_FORMAT(a.fromDate,'%d-%b-%Y') as  fromDate, DATE_FORMAT(a.toDate,'%d-%b-%Y') as  toDate, DATE_FORMAT(a.regularizationDate,'%d-%b-%Y %H:%i') regularizationDate,a.requestFrom,concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, a.remarks from ".TABLE_REGULARIZATION." a
			
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
		
        function get_report_user($resultType='G',$param1,$param2)
		
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
			
			
			
			//$sql = "SELECT empId, group_concat(date_format(attendanceDate,'%d')) as d, GROUP_CONCAT(attendanceTypeId) as t FROM `tbl_emp_attendance` where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by empId  ";
			
			
			$sql = "SELECT  (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='P' and e.empId=ea.empId) as totalpresent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='HD' and e.empId=ea.empId) as totalhalfday, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='A' and e.empId=ea.empId) as totalabsent, (select count(ea.attendanceStatus) from tbl_emp_attendance as ea where ea.attendanceStatus='L' and e.empId=ea.empId) as totalleave,    concat(e.empTitle,' ',e.empFname,' ',e.empLname) as emp_name, a.empId, group_concat(date_format(attendanceDate,'%d')) as d, GROUP_CONCAT(if(attendanceStatus='P','P',if(attendanceStatus='L','L', if(attendanceStatus='WO','W',attendanceStatus ) ) )) as t FROM `tbl_emp_attendance` a inner join tbl_emp_master e on a.empId=e.empId where DATE_FORMAT(attendanceDate,'%Y%m')='".$param2.$param1."' group by e.empId ";
			
			
			
			
			
			
			
			//echo $sql; exit;
			
			if($resultType=='G')
			
			{
				
				
				
				$result = parent::result_grid_array($sql);
				
				$result->mdays = cal_days_in_month(CAL_GREGORIAN, $param1, $param2);
				
			}
			
			else
			
			{
				
				$result = $this->db->query($sql." order by attendanceDate asc ")->result_array();
				
			}
			
            return $result;
			
		}
		
		
		
	function getempList()
		
		{
			//16-Aug-18
			$sql =" select s.State_Id ,a.empId,e.empDOJ, e.status,e.clients, r.id as region, se.leaveGroup, se.shift as servicesShift, ed.shift as departmentShift from rowdata a
			
			left join ".TABLE_EMP." e on a.empId=e.empId
			
			left join ".TABLE_DEPT." ed on e.empDept=ed.id
			
			left join ".TABLE_SERVICE." se on e.empId=se.empId
			
			left join ".TABLE_CITY_MASTER." c on e.jobLocation=c.cityId
			
			left join ".TABLE_STATE_MASTER." s on c.state=s.State_Id
			
			left join ".TABLE_REGION_MASTER." r on s.region=r.id
			
			WHERE 1=1 and e.isActive=1   group by e.empId";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		
		function getempweekoff($leaveGroup)
		
		{
			
			 $sql =" select * from ".TABLE_RULE." WHERE leaveGroup=".$leaveGroup." and leaveType='WO' ";
			
			return $result = $this->db->query($sql)->result_array();
			
			
			
		}
		
		
		
		
		
		function getempdayoff($leaveGroup)
		
		{
			
			$sql =" select * from ".TABLE_RULE." WHERE leaveGroup=".$leaveGroup." and leaveType='DO' ";
			
			
			
			/*  $sql =" select a.deptId, a.weekoff, e.empId from ".TABLE_EMP." e
				
				Left Join  ".TABLE_ADDWEEKOFF." a on e.empDept= a.deptId
				
				where a.deptId = '".$dept."' and a.weekoff='".$date."'";
				
			*/
			
			return $result = $this->db->query($sql)->result_array();
			
			//pre($result);
			
		}  
		
		
		
		
		
		function get_addweekoff_details($id=0, $resultType='G')
		
		{
			
			if($id> 0 )
			
			{
				
				$addsql .= " and id=".$id;
				
			}
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql = "select w.*,  DATE_FORMAT(w.weekoff,'%b, %Y') as  month, DATE_FORMAT(w.weekoff,'%d %b, %Y') as  weekoffdate, d.name as department  from ".TABLE_ADDWEEKOFF." w left join ".TABLE_DEPT." d on w.deptId=d.id where 1=1 ";
			
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
		
		function get_weekoff($field,$ltype,$gid)
		
		{
			
			$sql ="select *,group_concat(weekno) as n from ".TABLE_RULE." where 1=1 and leaveGroup=".$gid." and leaveType='".$ltype."' group by ".$field."";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		
		
		
		function get_weekoff_data($field,$ltype,$day, $gid)
		
		{
			
			$sql ="select *,group_concat(weekno) as n from ".TABLE_RULE." where 1=1 and leaveGroup=".$gid." and leaveType='".$ltype."' and weekoff=".$day." group by ".$field."";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		
		
		
		
		
		function get_dayoff($field,$ltype,$gid)
		
		{
			
			$sql ="select * from ".TABLE_RULE." where 1=1 and leaveGroup=".$gid." and leaveType='".$ltype."'";
			
			//echo $sql ="select *,group_concat(".$field.") as n from ".TABLE_RULE." where 1=1 and leaveGroup=".$gid." and  leaveType='".$ltype."' group by ".$field."";
			
			//$sql ="select id,weekno, ".$field." from ".TABLE_RULE." where 1=1 and leaveGroup=".$gid." and  leaveType='".$ltype."' group by ".$field."";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		
		
		
		
		
		function getleaveType($ltype)
		
		{
			
			$sql ="select id,name,description from ".TABLE_LEAVETYPE." where 1=1 and ltype='".$ltype."'";
			
			return $result = $this->db->query($sql)->result_array();
			
		}	    
		
		
		
		function getweekoff($day,$gid)
		
		{
			
			$sql ="select *,group_concat(weekno) as n from ".TABLE_RULE." where 1=1 and leaveGroup=".$gid." and weekoff='".$day."' group by weekoff";
			
			return $result = $this->db->query($sql)->result_array();
			
		}
		
		
		
		//suraj
		
		function get_designation_details($resultType='G')		
		{			
			$addSql = "  ";			
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select d.*,concat(e.empFname,' ',e.empLname) as ename,concat(ef.empFname,' ',ef.empLname) as ename1, DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(d.isUpdated,'%d-%b-%Y') as isUpdated,cl.name as clientName  from ".TABLE_DESIGNATION_MASTER." d left join ".TABLECLIENTS." cl on cl.id=d.clientId left join ".TABLE_EMP." e on e.empId=d.addedBy left join ".TABLE_EMP." ef on ef.empId=d.updatedBy where 1=1 ".$addSql." ";
			//echo $sql;			
			if($resultType=='G'){				
				$result = parent::result_grid_array($sql);				
			}else{				
				$result = $this->db->query($sql)->result_array();				
			}			
			return $result;			
		}		
		
		function duplicateDesignation($client,$name)
		{
			$sql="select id from ".TABLE_DESIGNATION_MASTER." where name='".$name."' AND clientId='".$client."' ";
			$result=$this->db->query($sql)->result_array();
			return $result['0'];
		}
		
		function get_department_details($resultType='G')
		{			
			$addSql = "  ";
			if($this->input->post('filters')!='') // search filters
			{				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}			
			$sql = "select d.*,concat(e.empFname,' ',e.empLname) as ename,concat(ef.empFname,' ',ef.empLname) as ename1, DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isUpdated, s.shiftName as shift, s.shiftTimeFrom, s.shiftTimeTo from ".TABLE_DEPT." d left join ".TABLE_EMP." e on e.empId=d.addedBy left join ".TABLE_EMP." ef on ef.empId=d.updatedBy left join tbl_mst_shift s on d.shift=s.id where 1=1 ".$addSql." ";			
			//echo $sql;			
			if($resultType=='G')			
			{				
				$result = parent::result_grid_array($sql);				
			}else{				
				$result = $this->db->query($sql)->result_array();				
			}			
			return $result;			
		}
		function duplicateDepartment($name)
		
		{
			
			$sql="select id from ".TABLE_DEPT." where name='".$name."'";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		//asset model
		
		function get_asset_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
				
			}
			
			
			
			$sql = "select *,concat(e.empFname,' ',e.empLname) as ename,concat(ef.empFname,' ',ef.empLname) as ename1, DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isUpdated from ".TABLE_MASTER_ASSET." d left join ".TABLE_EMP." e on e.empId=d.addedBy left join ".TABLE_EMP." ef on ef.empId=d.updatedBy where 1=1 ".$addSql." ";
			
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
		function duplicateAsset($name)
		
		{
			
			$sql="select id from ".TABLE_MASTER_ASSET." where name='".$name."'";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		
		//holiday model
		
		function get_holiday_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " where ".parent::decodeFilters($this->input->post('filters'));
				
				//$sname = s.name;
				
			}
			
			$sql="select d.*,concat(e.empFname,' ',e.empLname) as addedBy,concat(ef.empFname,' ',ef.empLname) as updatedBy, DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(d.isUpdated,'%d-%b-%Y') as isUpdated,d.id, d.holiday,DATE_FORMAT(d.holidayDate,'%d-%b-%Y') as holidayDates FROM ".TABLE_HOLIDAYS." d
			
			left join ".TABLE_EMP." e on e.empId=d.addedBy 
			
			left join ".TABLE_EMP." ef on ef.empId=d.updatedBy 
			
			where 1=1  ".$addSql." ";
			
			
			
			if($resultType=='G')
			
			{
				
				$result = parent::result_grid_array($sql);
				
			}
			
			else
			
			{
				
				$result = $this->db->query($sql)->result_array();
				
			}
			
			//pre($result);
			
			//echo $sql;
			
			return $result;
			
		}

		// 29-march-08
		function get_holiday_state_details($resultType='G')		
		{			
			$addSql = "  ";			
			if($this->input->post('filters')!='') // search filters			
			{				
				$addSql .= " where ".parent::decodeFilters($this->input->post('filters'));
			}			
			$sql="select * from tbl_mst_state where 1=1  ".$addSql." order by State_Name asc";			
			if($resultType=='G')			
			{				
				$result = parent::result_grid_array($sql);				
			}else{				
				$result = $this->db->query($sql)->result_array();				
			}			
			return $result;
		}



		function get_state_holiday_detail()
		{
			
		}




		function edit_clientRegion($holiId,$clientId)
		
		{
			
			$sql="select distinct region from tbl_mst_holiday_region_client where holiday=".$holiId." and clients=".$clientId." ";
			
			return $this->db->query($sql)->result_array();	    
			
		}
		function get_region_client_details($id=0)
		{

			if($this->input->post('filters')!='') // search filters

			{

			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));

			}

			 $sql="SELECT tbl_client.id as clientId,tbl_client.name as clientsn,tbl_mst_holiday_region_client.*,group_concat(DISTINCT tbl_region.name) as regclient FROM `tbl_mst_holiday_region_client` left join tbl_region on tbl_mst_holiday_region_client.region=tbl_region.id left join tbl_client on tbl_mst_holiday_region_client.clients=tbl_client.id where holiday=".$id." group by clients";

			return $result=$this->db->query($sql)->result_array();

		}
		function get_region_client_details____($holiday=0,$client_id=0)		
		{
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql="SELECT tbl_client.id as clientId,tbl_client.name as clientsn,tbl_mst_holiday_region_client.*,group_concat(DISTINCT tbl_region.name) as regclient FROM tbl_mst_holiday_region_client left join tbl_region on tbl_mst_holiday_region_client.region=tbl_region.id left join tbl_client on tbl_mst_holiday_region_client.clients=tbl_client.id where tbl_mst_holiday_region_client.clients=".$client_id." AND holiday=".$holiday." group by holiday";
			
			return $result=$this->db->query($sql)->result_array();
			
		}
		
		function add_edit_client_region()
		
		{
			
			$hid=$this->input->post('edit');
			
			$sql="Select * from ".TABLE_REGION_HOLIDAY_CLIENTS_MASTER." where holiday=".$hid."";
			
			//echo $sql;die;
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
			
		}
		
		function delete_region($holidayRegionId,$clients)
		
		{
			
			$this->db->where('holiday', $holidayRegionId);
			
			$this->db->where('clients', $clients);
			
			$this->db->delete('tbl_mst_holiday_region_client'); 
			
		}
		//clients
		
		function get_clients_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql = "select *,concat(e.empFname,' ',e.empLname) as ename,concat(ef.empFname,' ',ef.empLname) as ename1, DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isUpdated from ".TABLECLIENTS." d left join ".TABLE_EMP." e on e.empId=d.addedBy left join ".TABLE_EMP." ef on ef.empId=d.updatedBy where 1=1 ".$addSql." 
			
			";
			
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
		function duplicateClients($name,$id=false)
		{ 
			$addSql = "  ";
			if($id)
			{
				$addsql .= " and id!=".$id;
			}
			$sql="select * from ".TABLECLIENTS." where name LIKE '".$name."' ";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
		}
		//language
		function get_mst_language_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql = "select *,concat(e.empFname,' ',e.empLname) as ename,concat(ef.empFname,' ',ef.empLname) as ename1, DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isUpdated from ".TABLE_MST_LANGUAGE." d left join ".TABLE_EMP." e on e.empId=d.addedBy left join ".TABLE_EMP." ef on ef.empId=d.updatedBy where 1=1 ".$addSql." 
			
			";
			
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
		function duplicate_mst_language($name)
		
		{
			
			$sql="select id from ".TABLE_MST_LANGUAGE." where name='".$name."'";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		
		function getMailcontent()
		
		{
			
			$sql="select id,content from ".TABLE_CONTENT." where id=1";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		
		function decode_scode($arr,$str)
		
		{
			
			$rep = array('{EMPNAME}',
			
			'{MONTH}','{YEAR}'
			
			);
			
			
			
			$repWith = array($arr['empName'],$arr['month'], $arr['year']);
			
			
			
			
			
			return str_replace($rep,$repWith,$str);
			
		}
		
		function shiftList()
		
		{
			
			$sql="select id, shiftName, DATE_FORMAT(shiftTimeFrom,'%h:%i %p') as shiftTimeFrom, DATE_FORMAT(shiftTimeTo,'%h:%i %p') as shiftTimeTo from tbl_mst_shift where 1=1";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result;
			
		}
		
		//poplicy
		
		function get_policy_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql="select concat(e.empFname,' ',e.empLname) as addedBy,concat(ef.empFname,' ',ef.empLname) as updatedBy, DATE_FORMAT(pt.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(pt.isUpdated,'%d-%b-%Y') as isUpdated,pt.id as id,pt.policyTitle as name,pt.description as description,ct.name as clientsName,prt.name as projectsName,tcat.categoryName as categoryName from ".TABLE_POLICY." pt left join ".TABLE_EMP." e on e.empId=pt.addedBy left join ".TABLE_EMP." ef on ef.empId=pt.updatedBy left join ".TABLECLIENTS." ct on pt.clients=ct.id left join ".TABLEPROJECT." prt on pt.projects=prt.id left join ".TABLE_CATEGORY." tcat  on pt.category=tcat.catId where 1=1 ".$addSql;
			
			//echo $sql;die;
			
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
		
		
		
		function policyList()
		
		{
			
			$sql = "select p.description,p.id as id,p.policyTitle as policyTitle,c.categoryName from ".TABLE_POLICY." p LEFT JOIN ".TABLE_CATEGORY." c on p.Category=c.catId where 1=1 ";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result;
			
		}
		
		
		
		function clientList()
		
		{
			
			$sql = "select id,name from ".TABLECLIENTS."  where 1=1  Order By name ASC";
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
			
		} 
		
		function regionList()		
		{
			
			$sql = "select * from ".TABLE_REGION."  where 1=1  Order By name ASC";
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
			
		}
		
		
		
		function projectList()
		
		{
			
			$sql = "select * from ".TABLEPROJECT."  where 1=1  Order By name ASC";
			
			$result = $this->db->query($sql)->result_array();
			
			return $result;
			
		}
		
		
		
		function duplicatePolicy($name,$id)
		
		{
			
			$sql="select id from ".TABLE_POLICY." where policyTitle='".$name."' and id='".$id."'";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		
		//---------------Resign Reason--------//
		function duplicateResignReason($name,$id)
		{
			$sql="select id from ".TABLE_RESIGN_REASON_MASTER." where name='".$name."' and id!='".$id."'";
			$result=$this->db->query($sql)->result_array();
			return $result['0'];
		}
		function get_resign_reason_details($resultType='G')
		{
			$addSql = "";
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select * from ".TABLE_RESIGN_REASON_MASTER." where 1=1 ".$addSql;
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
		//---------------State--------//
		function get_state_details($resultType='G')
		{
			$addSql = "";
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select concat(em.empFname,' ',em.empLname) as addedBy,DATE_FORMAT(st.isCreated,'%d-%b-%Y') as isCreated ,region,State_Name,State_Id,rm.name as name from ".TABLE_STATE." st left join ".TABLE_REGION_MASTER." rm on st.region=rm.id left join ".TABLE_EMP." em on st.addedBy=em.empId where 1=1 ".$addSql;
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
		
		function duplicateState($name,$id)
		{
			$sql="select State_Id from ".TABLE_STATE." where State_Name='".$name."' and State_Id!='".$id."'";
			$result=$this->db->query($sql)->result_array();
			return $result['0'];
		}
		
		//---------------Goal Kpi--------//
		function get_goal_kpi_details($resultType='G')
		{
			$addSql = "";
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select tgk.*,concat(em.empFname,' ',em.empLname) as addedBy,DATE_FORMAT(tgk.isCreated,'%d-%b-%Y') as isCreated,tmd.name as designationName from tbl_goal_kpi tgk left join tbl_mst_designation tmd on tgk.designationId=tmd.id left join ".TABLE_EMP." em on tgk.addedBy=em.empId where 1=1 ".$addSql;
			
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
		
		/*function duplicate_goal_kpi($request_key,$chat_keyword_id)
			{
			$sql="select chat_keyword_id from tbl_goal_kpi where request_key='".$request_key."' and chat_keyword_id!='".$chat_keyword_id."'";
			$result=$this->db->query($sql)->result_array();
			return $result['0'];
		}*/
		
		//---------------Chat Keyword--------//
		function get_chat_keyword_details($resultType='G')
		{
			$addSql = "";
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select tck.*,concat(em.empFname,' ',em.empLname) as addedBy,DATE_FORMAT(tck.isCreated,'%d-%b-%Y') as isCreated from tbl_chat_keyword tck left join ".TABLE_EMP." em on tck.addedBy=em.empId where 1=1 ".$addSql;
			
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
		
		function duplicate_chat_keyword($request_key,$chat_keyword_id)
		{
			$sql="select chat_keyword_id from tbl_chat_keyword where request_key='".$request_key."' and chat_keyword_id!='".$chat_keyword_id."'";
			$result=$this->db->query($sql)->result_array();
			return $result['0'];
		}
		
		//city
		
		function get_city_details($resultType='G')
		{
			$addSql = "";
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select reg.name as region,concat(em.empFname,' ',em.empLname) as addedBy,DATE_FORMAT(st.isCreated,'%d-%b-%Y') as isCreated,state,cityName,cityId,rm.State_Name as State_Name from ".TABLE_CITY_MASTER." st left join ".TABLE_STATE_MASTER." rm on st.state=rm.State_Id left join ".TABLE_EMP." em on st.addedBy=em.empId left join ".TABLE_REGION_MASTER." reg on reg.id=rm.region where 1=1 ".$addSql;
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
		
		function duplicateCity($name,$id)
		{
			$sql="select cityId from ".TABLE_CITY_MASTER." where cityName='".$name."' and cityId!='".$id."'";
			$result=$this->db->query($sql)->result_array();
			return $result['0'];
		}
		
		function get_wages_details($id,$resultType='G', $sid)
		{
			
			$addSql = "";
			if($id){
				$addSql .=" and w.id='".$id."'";
			}
			if($sid){
				$addSql .=" and w.state='".$sid."'";
			}
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select w.id,w.state,w.min_take_home,w.semi_skilled_min_take_home,w.isUpdated, w.isCreated, w.rewise_date,w.updatedBy, w.addedBy, s.State_Name, concat(e.empFname,' ',e.empLname) as add_user, concat(u.empFname,' ',u.empLname) as update_user,  DATE_FORMAT(w.isCreated,'%d-%b-%Y') as createdOn , DATE_FORMAT(w.isUpdated,'%d-%b-%Y') as 	updateOn, DATE_FORMAT(w.rewise_date,'%d-%b-%Y') as 	effectiveDate, w.skilled_rate, w.pc_skilled_basic, w.unskilled_rate  from ".TABLE_WAGES." w
			left join ".TABLE_STATE." s on w.state=s.State_Id
			left join ".TABLE_EMP." e on w.addedBy=e.empId
			left join ".TABLE_EMP." u on w.updatedBy=u.empId
			where 1=1 ".$addSql;
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
		
		function duplicateWages()
		{
			
			$sql="select id from ".TABLE_WAGES." where state='".$this->input->post('id')."'";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		function get_wages_update_history($id,$resultType='G')
		{
			$addSql = "";
			if($id){
				$addSql .=" and w.state='".$id."'";
			}
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select w.id,w.state,w.isUpdated, w.isCreated, w.rewise_date,w.updatedBy,  s.State_Name, concat(u.empFname,' ',u.empLname) as update_user,  DATE_FORMAT(w.isCreated,'%d-%b-%Y %h:%i %p') as createdOn , DATE_FORMAT(w.rewise_date,'%d-%b-%Y') as effectiveDate, w.skilled_rate, w.pc_skilled_basic, w.unskilled_rate  from ".TABLE_WAGES_HISTORY." w
			left join ".TABLE_STATE." s on w.state=s.State_Id
			left join ".TABLE_EMP." u on w.updatedBy=u.empId
			where 1=1 ".$addSql;
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
		function get_employee_esic_pf_details($resultType)
		{
			if($this->input->post('filters')!='') // search filters
			{
				$addsql .= " and ".$this->decodeFiltersUpload($this->input->post('filters'));
			}
			$sql = "select ser.empId, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, ser.esicNumber, ser.uanNumber, ser.pfNumber from ".TABLE_EMP." e
			Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId where 1=1 ".$addsql."";
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
		
		function decodeFiltersUpload($filters)
		{
			$sql = ' (';
			$objJson = json_decode($filters);
			foreach($objJson->{'rules'} as $rules)
			{
				if($rules->{'field'}=='empName')
				{
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
					}
					$sql .= ' ) ';
					$sql .= $objJson->{'groupOp'}.' ';
				}
				else
				{
					$sql .= $rules->{'field'}.' ';
					$sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' ';
					$sql .= $objJson->{'groupOp'}.' ';
				}
			}
			$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
			return $sql.') ';
		}
		
		//15-sep-17
		public function get_tax_details($resultType='G')
		{
			// print_r($this->input->post()); die;
			if($this->input->post('filters')!='') // search filters
			{
				$addsql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql="Select p.*,s.State_Name as state from ".TABLE_PTax." as p Left join ".TABLE_STATE_MASTER." as s on p.job_state_id=s.State_Id where 1=1 ".$addsql."  ";
			if($resultType=='G')
			{
				$result = parent::result_grid_array($sql);
			}
			else
			{
				$result = $this->db->query($sql)->result_array();
			}			
			return $result;
			/* return $this->db->last_query(); */
		}
		function listing_tax_details($resultType='G')
		{
			$addSql = "";
			if($this->input->post('filters')!='') // search filters
			{
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
			}
			$sql = "select * from ".TABLE_PTax." where 1=1 ".$addSql;
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
		function getCandiatePTax($job_state_id,$gross_salary,$to_salary,$wh)
		{
			$sql = "select * from tbl_p_tax_statewise where ((job_state_id=".$job_state_id." AND from_amt<=".$gross_salary." AND to_amt>=".$gross_salary.") OR (job_state_id=".$job_state_id." AND from_amt<=".$to_salary." AND to_amt>=".$to_salary."))".$wh." ";
			return $candidateList=$this->db->query($sql)->result_array();	
			// return $this->db->last_query();
		}
		function getQueryRow($sql)
		{	
			return $list=$this->db->query($sql)->row();	
		}
		function getQueryArray($sql=false)
		{	
			return $list_array=$this->db->query($sql)->result_array();
		}
		//15-sep-17
		//10-jan-17
		function get_handbook_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql="select concat(e.empFname,' ',e.empLname) as addedBy,concat(ef.empFname,' ',ef.empLname) as updatedBy, DATE_FORMAT(pt.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(pt.isUpdated,'%d-%b-%Y') as isUpdated,pt.id as id,pt.handbookTitle as name,pt.description as description,ct.name as clientsName,prt.name as projectsName,tcat.categoryName as categoryName from ".TABLE_HANDBOOK." pt left join ".TABLE_EMP." e on e.empId=pt.addedBy left join ".TABLE_EMP." ef on ef.empId=pt.updatedBy left join ".TABLECLIENTS." ct on pt.clients=ct.id left join ".TABLEPROJECT." prt on pt.projects=prt.id left join ".TABLE_CATEGORY." tcat  on pt.category=tcat.catId where 1=1 ".$addSql;
			
			//echo $sql;die;
			
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
		function duplicateHandbook($name,$id)
		
		{
			
			$sql="select id from ".TABLE_HANDBOOK." where handbookTitle='".$name."' and id='".$id."'";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		
		//10-jan-17
		//16-march-18
		function get_grade_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql = "select *,tg.grade_id as id,concat(e.empFname,' ',e.empLname) as ename,concat(ef.empFname,' ',ef.empLname) as ename1,DATE_FORMAT(tg.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(tg.isCreated,'%d-%b-%Y') as isUpdated from ".TABLEGRADE." tg left join ".TABLECLIENTS." tc on tg.client_id=tc.id left join ".TABLE_EMP." e on e.empId=tg.addedBy left join ".TABLE_EMP." ef on ef.empId=tg.updatedBy  where 1=1 ".$addSql." ";
			
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
		//16-march-18
		//17-march-18
		function get_trade_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql = "select *,tg.id as id,tg.name as trade_name,concat(e.empFname,' ',e.empLname) as ename,concat(ef.empFname,' ',ef.empLname) as ename1,DATE_FORMAT(tg.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(tg.isCreated,'%d-%b-%Y') as isUpdated from ".TABLEPROJECT." tg left join ".TABLECLIENTS." tc on tg.clients=tc.id left join ".TABLE_EMP." e on e.empId=tg.addedBy left join ".TABLE_EMP." ef on ef.empId=tg.updatedBy  where 1=1 ".$addSql." ";
			
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
		//17-march-18
		//17-march-18
		function get_desigfunction_details($resultType='G')
		
		{
			
			$addSql = "  ";
			
			
			
			if($this->input->post('filters')!='') // search filters
			
			{
				
				$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
				
			}
			
			$sql = "select d.*,d.dfunctionId as id,concat(e.empFname,' ',e.empLname) as ename,concat(ef.empFname,' ',ef.empLname) as ename1, DATE_FORMAT(d.isCreated,'%d-%b-%Y') as isCreated,DATE_FORMAT(d.isUpdated,'%d-%b-%Y') as isUpdated  from ".TABLE_DESIG_FUNCTION." d left join ".TABLE_EMP." e on e.empId=d.addedBy left join ".TABLE_EMP." ef on ef.empId=d.updatedBy
			
			
			
			where 1=1 ".$addSql." ";
			
			//echo $sql;
			
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
		function duplicateDesigfunction($name)
		
		{
			
			$sql="select dfunctionId from ".TABLE_DESIG_FUNCTION." where dfunctionName='".$name."'";
			
			$result=$this->db->query($sql)->result_array();
			
			return $result['0'];
			
		}
		function get_ajax_grade($id)
		{
		return $this->db->get_where('tbl_grade',array('client_id'=>$id))->result_array();
		
		}
		//17-march-18
	}	