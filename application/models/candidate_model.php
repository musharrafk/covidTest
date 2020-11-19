<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class candidate_model extends parent_model {
	var $base_tbl = TABLE_CANDIDATE;
	var $u_column = 'id';

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
			
			if($rules->{'field'}=='o.empFname')
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


					$sql  .= " o.empTitle like '%".$expKey[$k]."%'";
					$sql  .= " or o.empFname like '%".$expKey[$k]."%'";
					$sql  .= " or o.empMname like '%".$expKey[$k]."%'";
					$sql  .= " or o.empLname like '%".$expKey[$k]."%'";

                            //$addSql .= " or ";
				}

				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
                                //////////
								
			}
           

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
				 

		    if($rules->{'field'}=='cityName')
			{
				
			$sql .= ' ( ';
		
			
				$sql  .= "cityName like '%".filter_values($rules->{'data'})."%'";
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
			} 
			
			if($rules->{'field'}=='s.State_Name')
			{
		
			    $sql .= ' ( ';
		
			
				$sql  .= "s.State_Name like '%".filter_values($rules->{'data'})."%'";
				
				$sql .= ' ) ';
				$sql .= $objJson->{'groupOp'}.' ';
				unset($objJson->{'rules'}[$i]);
				
			} 
			

			if($rules->{'field'}!='o.joiningDate' && $rules->{'field'} !='s.State_Name' && $rules->{'field'} !='cityName' && $rules->{'field'} != 'o.empFname')
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

// 21Nov
function getBackofficeCandidates($canId=0, $resultType='G')
{
	$role = explode(',',$this->session->userdata('admin_role_id')); 	
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
	}
	if($canId > 0)
	{
		$addSql .= " and o.id=".$canId;
	}
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}

		
		$sql = "select o.*,o.empType, concat(o.empTitle,' ',o.empFname,' ',o.empLname) as empName, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate, DATE_FORMAT(o.termEnddate,'%d-%b-%Y') as termEnddate,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName,cl.name as clients, p.name as projects,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as offerLetterCreatedBy  from ".$this->base_tbl." o
		LEFT JOIN ".TABLE_CITY." c on o.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_EMP." e on o.offerLetterCreatedBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where 1=1 and o.empType!=2 ".$addsql."  ";
  		//echo $sql; die;
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



	function appointmentStaffCandiateList($canId=0, $resultType='G')
	{
		$role = explode(',',$this->session->userdata('admin_role_id')); 		
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		$sql = "select o.*,concat(o.empTitle,' ',o.empFname,' ',o.empLname) as empName, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate,cl.name as clients,p.name as projects,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as appointmentLetterCreatedBy,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName  from ".$this->base_tbl." o
		LEFT JOIN ".TABLE_CITY." c on o.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_EMP." e on o.appointmentLetterCreatedBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where 1=1 ".$addsql." AND o.generateAppointment=1 AND o.empType!=2 ";
  		  //where o.status=1 AND o.basic>0 ".$addsql." order by o.id DESC";
		
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



// --21Nov--
	function get_candidate_details($canId=0, $resultType='G')
	{
	 // pre($this->session->all_userdata()); die;
		$role     = explode(',',$this->session->userdata('admin_role_id'));
		$region   = explode(',',$this->session->userdata('admin_region')); 
		if($region[0]){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}if($canId > 0)
		{
			$addSql .= " and o.id=".$canId;
		}
		if($this->input->post('filters')!='') // search filters
		{
			if(self::decodeFilters($this->input->post('filters')))
			{ 
				$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
			}
		}
		
         // permission
		 $stateaccess =   $this->stateRoleCandPermissionList($this->session->userdata('admin_id'));	    
		 if(isset($stateaccess['stateIds']) && $stateaccess['stateIds'] != ''){
			 $commaSepState   =   $stateaccess['stateIds'];  
			 $stateCond       =  " and c.cityId in (".$commaSepState.")";      
		 } else {
			 $stateCond = '';
		 }
		 $viewPermission = isset($stateaccess['viewPermission'])?$stateaccess['viewPermission']:0;

		$sql = "select o.*,o.empType, concat(o.empTitle,' ',o.empFname,' ',o.empMname,' ',o.empLname) as empName, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate, DATE_FORMAT(o.termEnddate,'%d-%b-%Y') as termEnddate,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName,cl.name as clients, p.name as projects,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as offerLetterCreatedBy,CHECK_EMPLOYEE_EXIST(o.id)as candSelectedStatus,$viewPermission as viewPermission  from ".$this->base_tbl." o
		LEFT JOIN ".TABLE_CITY." c on o.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_EMP." e on o.offerLetterCreatedBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where 1=1 $stateCond and o.empType!=1  ".$addsql." ";
		//where 1=1 and o.empType!=1 and o.id NOT LIKE '%10%' ".$addsql."  ";
	 
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
	
	function appointmentCandiateList($canId=0, $resultType='G')
	{
		$role = explode(',',$this->session->userdata('admin_role_id')); 		
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		$sql = "select o.*,concat(o.empTitle,' ',o.empFname,' ',o.empLname) as empName, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate,cl.name as clients,p.name as projects,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as appointmentLetterCreatedBy,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName  from ".$this->base_tbl." o
		LEFT JOIN ".TABLE_CITY." c on o.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_EMP." e on o.appointmentLetterCreatedBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where 1=1 ".$addsql." AND o.generateAppointment=1 AND o.empType!=1 ";
  		  //where o.status=1 AND o.basic>0 ".$addsql." order by o.id DESC";
		
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
	
	function get_document_details($canId=0, $resultType='G')
	{
		$role = explode(',',$this->session->userdata('admin_role_id')); 
		if(in_array("9", $role)){
		//$addsql = " AND empType='1'";
		}
		if(in_array("10", $role)){
		//$addsql = " AND empType='2'";
		}
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}if($canId > 0)
		{
			$addSql .= " and o.id=".$canId;

		}
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select o.id,concat(o.empTitle,' ',o.empFname,' ',o.empLname) as empName,o.empImage,o.photoIdproof,o.addressProof,o.certificates10th,o.marksheet10th,heighestqualificationsMarksheet,o.heighestqualificationsCertificates,o.experienceCertificates,o.panCard,o.panNo,o.payslip,o.relievingLetter,o.cancelCheck,o.documentVerify,o.bankAccountno,o.bankName,o.ifscCode,o.bankBranchaddress,o.documentVerify, c.cityName as jobLocation, cc.cityName, d.name,r.name as region, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as documentVerifyBy, s.State_Name  from ".$this->base_tbl." o
		LEFT JOIN ".TABLE_CITY." c on o.jobLocation=c.cityId
		LEFT JOIN ".TABLE_CITY." cc on o.city=cc.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_EMP." e on o.documentVerifyBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where o.documentUpload=1 ".$addsql." ";
  		  //where o.status=1 AND o.basic>0 ".$addsql." order by o.id DESC";
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
	
	
	function appointmentCandiateListforCSV($limit=0 , $empType = false)
	{
		$role = explode(',',$this->session->userdata('admin_role_id')); 
		if(in_array("9", $role)){
		//$addsql = " AND empType='1'";
		}
		if(in_array("10", $role)){
		//$addsql = " AND empType='2'";
		}
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0'])
			$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";


		if($empType==1)
		{
			$addsql .=" AND o.empType =  ".$empType."";
		}
		else{
			$addsql .=" AND o.empType != 1 ";

		}
		/*22-feb-18 $sql = "select o.*,concat(o.empTitle,' ',o.empFname,' ',o.empLname) as name, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate, DATE_FORMAT(o.appointmentCreatedon,'%d-%b-%Y') as appointmentCreatedon, c.cityName as city,c.cityName as jobLocation,cl.name as clients,p.name as projects,d.name as designation,r.name as region, s.State_Name as state, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as appointmentLetterCreatedBy  from ".$this->base_tbl." o
		LEFT JOIN ".TABLE_CITY." c on o.jobLocation=c.cityId
		LEFT JOIN ".TABLE_CITY." cc on o.jobLocation=cc.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_EMP." e on o.appointmentLetterCreatedBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where 1=1 ".$addsql." AND o.generateAppointment=1 order by o.id DESC "; */
  		//where o.status=1 AND o.basic>0 ".$addsql." order by o.id DESC";
		$sql = "select o.*,concat(o.empTitle,' ',o.empFname,' ',o.empLname) as empName, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate,cl.name as clients,p.name as projects,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as appointmentLetterCreatedBy,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName , presentCity.cityName as presentCityName  , presentState.State_Name as presentStateName , pc.cityName as permanentCityName  , ps.State_Name as permanentStateName  from ".$this->base_tbl." o

		LEFT JOIN ".TABLE_CITY." c on o.jobCity=c.cityId
		LEFT JOIN ".TABLE_CITY." presentCity on o.city=presentCity.cityId
		LEFT JOIN ".TABLE_CITY." pc on o.p_city=pc.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_STATE." presentState on o.state=presentState.State_Id
		LEFT JOIN ".TABLE_STATE." ps on o.p_state=ps.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_EMP." e on o.appointmentLetterCreatedBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where 1=1 ".$addsql." AND o.generateAppointment=1 ";
		
		$result=$this->db->query($sql)->result_array();
		//pre($result);die;
		return $result;
	}
	
	
	function getCandiate($id)
	{
		$sql = "select tbl_mst_dept.name as dept_name,contact_address.address as address_contact,contact_person.name as cntct_name, bg.name as blood_grp_name,o.*,o.clients as client,concat(o.empTitle,' ',o.empFname,' ',o.empMname,' ',o.empLname) as name, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate,  DATE_FORMAT(o.empDOB,'%d-%b-%Y') as dob, d.name as designationname,cl.name as clients, p.name as projects,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName
		from ".$this->base_tbl." o
		Left JOIN tbl_mst_dept on tbl_mst_dept.id=o.empDept
		LEFT JOIN contact_address on contact_address.id=o.contact_address
		LEFT JOIN contact_person on contact_person.id=o.contact_person
		LEFT JOIN ".TABLE_CITY." c on o.jobCity=c.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		LEFT JOIN tbl_mst_blood_group bg on o.blood_group=bg.id
		where o.id='$id'";
		$result=$this->db->query($sql)->result_array();
		//pre($result);die;
		return $result['0'];
	}
	
	function getCandiateDetails($id)
	{
		$sql = "select o.*,concat(o.empTitle,' ',o.empFname,' ',o.empLname) as name from ".$this->base_tbl." o where o.id=$id";
		$result=$this->db->query($sql)->result_array();
		
		return $result['0'];
	}
	
	
	function clientsList($clientId = false)
	{
		$wh = " ";
		if($clientId){
			$wh .=  " and id='".$clientId."'"; 
		}
		$sql = "select * from ".TABLECLIENTS."   where 1=1 and status='1' $wh Order By name ASC";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}

	function projectList($cid=0)
	{
		if($cid)
		{
			$addsql =" AND clients=".$cid."";
		}
		$sql = "select * from ".TABLEPROJECT."  where 1=1 ".$addsql." Order By name ASC";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	function ajaxProjectList($cid)
	{
		$sql = "select * from ".TABLEPROJECT."  where clients=".$cid." Order By name ASC";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	
	function duplicateRecord($name, $jobLocation, $designation, $joiningDate)
	{
		$sql = "select id from ".$this->base_tbl."  where empFname='".$name."' AND jobLocation=".$jobLocation." AND designation=".$designation." AND joiningDate='".$joiningDate."'";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}
	function fetchCandiate($id)
	{
		$sql = "select * from ".$this->base_tbl."  where id IN(".$id.")";
		return $candidateList=$this->db->query($sql)->result_array();
		//pre($candidateList);die;
		
	}
	
	function salaryList()
	{
		$sql = "select * from tbl_salarybreakup order by id DESC ";
		return $candidateList=$this->db->query($sql)->result_array();
		//pre($candidateList);die;
		
	}
	function totaljoin($id,$status='0')
	{
		if($status=='1')
		{
			$addsql ="and e.status=".$status."";
		}
		$sql = "select r.name,count(e.empDesination)as totaljoin from ".TABLE_EMP." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Where e.empDesination=".$id." ".$addsql." group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();
		
	}	
	
	function totaljoinjoiningType($status)
	{

		$addsql ="and e.status=".$status."";
		$sql = "select j.name, count(e.empDesination)as totaljoin, count( NULLIF(e.empEmailOffice, '' )) as totalOfficeemail,count( NULLIF(e.empMobile, '' )) as empMobile, count( NULLIF(e.empEmailPersonal, '' )) as empEmailPersonal, count( NULLIF(e.empDesination, '' )) as empDesination, count( NULLIF(e.empDept, '' )) as empDept, count( NULLIF(e.empDept, '' )) as empDept,  count( NULLIF(e.appointmentLetterSend, '' )) as appointmentLetterSend,  count(e.projects), count( NULLIF(e.empImage, '' )) as empImage, count( NULLIF(e.empEmailPersonal, 'NULL' )) as empEmailPersonal, count( reportingTo) as reporting, count( NULLIF(e.empDOJ , '' )) as totalDOJ, count( NULLIF(p.empDOB , '' )) as totalDOB, count( NULLIF(p.empGender , '' )) as totalGender, count( NULLIF(p.empFathersName , '' )) as totalFathersname, count( NULLIF(p.empMotherName , '' )) as totalMothersname, count( NULLIF(p.emergencyContactNumber , '' )) as totalEmergencycontactno, count( NULLIF(p.emergencyContactName , '' )) as totalEmergencycontactperson, count( NULLIF(s.ctc , '' )) as totalCTC,count( NULLIF(se.esicNumber , '' )) as totalESI, count( NULLIF(se.uanNumber , '' )) as totalUANno,  count( NULLIF(b.accountNo , '' )) as totalAccountno, count( NULLIF(b.bankName , '' )) as totalBankname, count( NULLIF(b.branch , '' )) as totalBankbranch, count( NULLIF(b.ifscCode , '' )) as totalIFSC, count( NULLIF(se.pfNumber , '' )) as totalpfNumber, count( NULLIF(pe.panNo , '' )) as totalPANno, count( NULLIF(pe.pan , '' )) as totalPANcardupload, count( NULLIF(pe.photoIdproof , '' )) as totalPhotoID, count( NULLIF(pe.addressProof , '' )) as totalAddressproof,count( NULLIF(pe.certificates10th , '' )) as totalcertificates10th, count( NULLIF(pe.heighestqualificationsCertificates , '' )) as totalheighestqualificationsCertificates, count( NULLIF(pe.cVSubmited , '' )) as totalResume from ".TABLE_EMP." e
		Left Join tbl_joiningtype j on j.id=e.joiningFor
		Left Join tbl_emp_personal p on p.empId=e.empId
		Left Join tbl_salarybreakup s on s.empId=e.empId
		Left Join tbl_emp_bank b on b.empId=e.empId
		Left Join ".TABLE_SERVICE." se on se.empId=e.empId
		Left Join tbl_emp_document pe on pe.empId=e.empId
		Where 1=1 ".$addsql." and e.empId !=10000 group by e.joiningFor order by e.joiningFor";
		return $candidateList=$this->db->query($sql)->result_array();
		
	}	
	
	function totalresign($id)
	{
		$sql = "select r.name,count(e.status)as totalresign from ".TABLE_EMP." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Where e.status=0 and  e.empDesination IN(12,26,27,31,33)  group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();
		
	}	
	function totalworking($id)
	{
		$sql = "select r.name,count(e.status)as total_working from ".TABLE_EMP." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Where e.status=1 and empDesination=".$id."  group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();
		
	}	
	
	function totalappointmentissue($id,$status='0')
	{
		if($status=='1')
		{
			$addsql ="and e.status=".$status."";
		}
		$sql = "select r.name as region,count(e.appoinmentLetterdownload)as totappointmentissue from ".TABLE_REGION." r
		Right Join ".TABLE_STATE." s on r.id=s.region
		Left Join ".TABLE_CITY." c on s.State_Id=c.state
		Left Join ".TABLE_EMP." e on e.jobLocation=c.cityId
		Where e.appoinmentLetterdownload=1 and e.empDesination=".$id." ".$addsql." group by r.id order by r.name";
		return $this->db->query($sql)->result_array();
		
	}	
	
	function totalappointmentsent($id,$status='0')
	{
		if($status=='1')
		{
			$addsql ="and e.status=".$status."";
		}
		$sql = "select r.name as region,count(e.empId)as totappointmentLetterSend from ".TABLE_REGION." r
		Right Join ".TABLE_STATE." s on r.id=s.region
		Left Join ".TABLE_CITY." c on s.State_Id=c.state
		Left Join ".TABLE_EMP." e on e.jobLocation=c.cityId
		Where e.appointmentLetterSend=1 and e.empDesination=".$id." ".$addsql." group by r.id order by r.name";
		return $this->db->query($sql)->result_array();
		
	}	
	
	function canidateDatajoiningtype($status)
	{
		$addsql .=" and c.status=".$status."";
		$sql = "select j.name, count(c.offerLettersent)as totalLOIsent, count( NULLIF(c.offerLetterIssue, '' )) as totalLOIdownload   from ".TABLE_CANDIDATE." c
		Left Join tbl_joiningtype j on j.id=c.empType
		WHERE 1=1 ".$addsql." group by c.empType order by c.empType";
		return $candidateList=$this->db->query($sql)->result_array();
	}	
	

	function totalLOIIssed($id,$status='0',$designationIN='')
	{

		if($status=='1')
		{
			$addsql .=" and em.status=".$status."";
		}
		if($designationIN=='Y')
		{
			$addsql .=" and e.designation IN(12,26,27,31,33)";
		}else{
			if($id>0)
			{
				$addsql .=" and e.designation='".$id."'";
			}
		}
		$sql = "select r.name,count(e.id)as totalLOI from ".TABLE_CANDIDATE." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_EMP." em on e.id=em.candidateId
		Where 1=1 and e.offerLetterIssue=1 ".$addsql." group by r.id order by r.name";
		
		/*$sql = "select e.id,e.designation,em.status, r.name from ".TABLE_CANDIDATE." e
			Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
			Left Join ".TABLE_STATE." s on c.state=s.State_Id
			Left Join ".TABLE_REGION." r on s.region=r.id
			Left Join ".TABLE_EMP." em on e.id=em.candidateId
			Where e.designation=".$id." and e.offerLetterIssue=1 ".$addsql."  order by r.name";*/

			return $candidateList=$this->db->query($sql)->result_array();
		//pre($candidateList);die;

	}	


	function totalLOIIsent($id,$status='0', $emptype='', $designationIN='Y')
	{
		if($status=='1')
		{
			$addsql .=" and e.status=".$status."";
		}
		if($designationIN=='Y'){
			$addsql .=" and e.empDesination IN(12,26,27,31,33)";

		}
		else{
			if($id>0){
				$addsql .=" and e.empDesination='".$id."'";
			}
		}
		if($emptype)
		{
			$addsql .=" and e.joiningFor='".$emptype."'";
		}
		$sql = "select r.name, count(e.empId)as totalLOIsent from ".TABLE_EMP." e
		left join ".TABLE_CANDIDATE." c on e.candidateId=c.id 
		Left Join ".TABLE_CITY." ci on e.jobLocation=ci.cityId
		Left Join ".TABLE_STATE." s on ci.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Where  c.offerLettersent='1' ".$addsql." group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();

	}	

	function candidateData($id,$status='0',$designationIN='')
	{
		if($status=='1')
		{
			$addsql .=" and em.status=".$status."";
		}
		if($designationIN=='Y'){
			$addsql .=" and e.designation IN(12,26,27,31,33)";
		}else{
			if($id>0){
				$addsql .=" and e.designation =".$id."";
			}
		}

		$sql = "select r.name,count(NULLIF(e.backout,'')) as totalactive, count( NULLIF(e.id, '' )) as totalshort, count( NULLIF(e.offerLettersent, '' )) as totalLOIsent, count( NULLIF(e.offerLetterIssue, '' )) as totalLOI from ".TABLE_CANDIDATE." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLE_EMP." em on e.id=em.candidateId
		Where 1=1  ".$addsql." group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();

	}	

		function totalshortlist($id='',$status='0')
		{
			if($status=='1')
			{
				$addsql ="and em.status=".$status."";
			}
			if($id)
			{
				$addsql ="and e.designation=".$id."";
			}
			$region = explode(',',$this->session->userdata('admin_region')); 
			if($region['0']){
				$addsql .=" and r.id in(".$this->session->userdata('admin_region').")";
			}

			$sql = "select r.name as region, r.name as name, count(e.designation)as totalshort from ".TABLE_CANDIDATE." e
			Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
			Left Join ".TABLE_STATE." s on c.state=s.State_Id
			Left Join ".TABLE_REGION." r on s.region=r.id
			Left Join ".TABLE_EMP." em on e.id=em.candidateId
			Where 1=1 ".$addsql." group by r.id order by r.name";
		/*$sql = "select e.id, r.name,e.designation from ".TABLE_CANDIDATE." e
			Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
			Left Join ".TABLE_STATE." s on c.state=s.State_Id
			Left Join ".TABLE_REGION." r on s.region=r.id
			Left Join ".TABLE_EMP." em on e.id=em.candidateId
			Where e.designation=".$id." ".$addsql." and r.id=4 order by e.id";*/
			return $candidateList=$this->db->query($sql)->result_array();
		//pre($candidateList);die;

		}	

		function totalbackout($id,$status)
		{
			$sql = "select r.name,count(e.designation)as totalrecord from ".TABLE_CANDIDATE." e
			Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
			Left Join ".TABLE_STATE." s on c.state=s.State_Id
			Left Join ".TABLE_REGION." r on s.region=r.id
			Where e.designation IN(12,26,27,31,33) and e.backout=".$status." group by r.id order by r.name";
			return $candidateList=$this->db->query($sql)->result_array();
		//pre($candidateList);die;

		}	



		function totalcountrecords($id,$fields,$status='0')
		{

			if($fields=='empDOJ'){
				$addsql .="AND e.".$fields."!='0000-00-00'";
			}else{
				$addsql .="AND e.".$fields."!=''";
			}
			if($status=='1')
			{
				$addsql .="and e.status=".$status."";
			}

			$sql = "select r.name,count(e.".$fields.")as total".$fields." from ".TABLE_EMP." e
			Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
			Left Join ".TABLE_STATE." s on c.state=s.State_Id
			Left Join ".TABLE_REGION." r on s.region=r.id
			Where  e.empDesination IN(12,26,27,31,33) ".$addsql."  group by r.id order by r.name";
			return $this->db->query($sql)->result_array();
		}

		function candiateList($limit=0,$alertsend=0 , $empType=false)
		{

			$role = explode(',',$this->session->userdata('admin_role_id')); 
			if(in_array("9", $role)){
		//$addsql = " AND empType='1'";
			}
			if(in_array("10", $role)){
		//$addsql = " AND empType='2'";
			}


			$region = explode(',',$this->session->userdata('admin_region')); 
			if($region['0'])
				$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";


			if($empType==1)
			{
				$addsql .=" AND o.empType =  ".$empType."";
			}
			else{
				$addsql .=" AND o.empType !=  1";

			}
			
			/*$sql = "select o.*,o.empType, concat(o.empTitle,' ',o.empFname,' ',o.empLname) as empName, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate, DATE_FORMAT(o.termEnddate,'%d-%b-%Y') as termEnddate,  c.cityName as jobLocation, c.cityName, cl.name as clients, p.name as projects,d.name,r.name as region, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as offerLetterCreatedBy, s.State_Name  from ".$this->base_tbl." o
			LEFT JOIN ".TABLE_CITY." c on o.jobLocation=c.cityId
			LEFT JOIN ".TABLE_STATE." s on o.jobState=s.State_Id
			LEFT JOIN ".TABLE_REGION." r on s.region=r.id
			LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
			LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
			LEFT JOIN ".TABLE_EMP." e on o.offerLetterCreatedBy=e.empId
			LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
			where 1=1  ".$addsql."  order by o.id DESC ";*/
			$sql = "select o.*,o.empType, concat(o.empTitle,' ',o.empFname,' ',o.empLname) as empName, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate, DATE_FORMAT(o.termEnddate,'%d-%b-%Y') as termEnddate,c.cityName as jobCityName,s.State_Name as jobStateName,r.name as jobRegionName,cl.name as clients, p.name as projects,d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as offerLetterCreatedBy , pc.cityName as pCityName  , ps.State_Name as pStateName,dpt.name as departmentName from ".$this->base_tbl." o
			LEFT JOIN ".TABLE_CITY." c on o.jobCity=c.cityId
			LEFT JOIN ".TABLE_CITY." pc on o.p_city=pc.cityId
			LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
			LEFT JOIN ".TABLE_STATE." ps on o.p_state=ps.State_Id
			LEFT JOIN ".TABLE_REGION." r on s.region=r.id
			LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
			LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
			LEFT JOIN ".TABLE_EMP." e on o.offerLetterCreatedBy=e.empId
			LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
			LEFT JOIN ".TABLE_DEPT." dpt on o.empDept=dpt.id
			where 1=1 and o.id NOT LIKE '%10%' ".$addsql."  ";
  		//where e.status=1 AND o.basic>0 ".$addsql." order by o.id DESC";	
			
			$result=$this->db->query($sql)->result_array();
			return $result;
		}

		function updateissuedLetter()
		{
			$sql ="select id,offerLetterIssue from tbl_candidate_25_june WHERE  offerLetterIssue='1' order by id desc";
			$result=$this->db->query($sql)->result_array();
			return $result;

		}	

		function duplicateEmail($email)
		{
			$sql="select id from ".TABLE_CANDIDATE." where email='".$email."' and status='1' and backout !='0'";
			$result=$this->db->query($sql)->result_array();
			return $result['0'];
		}


	function duplicateMobile($mobile)
	{
		$sql="select id from ".TABLE_CANDIDATE." where mobile='".$mobile."' and status='1' and backout !='0'";
		$result=$this->db->query($sql)->result_array();
		return $result['0'];
	}
	function duplicatePan($panNo,$cid)
	{
		$sql="select id from ".TABLE_CANDIDATE." where panNo='".$panNo."' and id!='".$cid."' and status='1'";
		$result=$this->db->query($sql)->result_array();
		return $result['0'];
	}

	function get_candiate_update_history($id)
	{
		/*$sql = "select o.*,concat(o.empTitle,' ',o.empFname,' ',o.empLname) as name, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate,  DATE_FORMAT(o.empDOB,'%d-%b-%Y') as dob, c.cityName as jobLocation, d.name as designationname, ci.cityName as canidatecity, c.cityName as jobLocation,cl.name as clients, p.name as projects, s.State_Name as jsname, adc.cityName as addcity, ads.State_Name as addstate, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as offerModifiedBy from ".TABLE_CANDIDATE_BACKUP." o
		LEFT JOIN ".TABLE_CITY." c on o.jobLocation=c.cityId
		LEFT JOIN ".TABLE_CITY." ci on o.city=ci.cityId
		LEFT JOIN ".TABLE_STATE." s on o.jobState=s.State_Id
		LEFT JOIN ".TABLE_CITY." adc on o.city=adc.cityId
		LEFT JOIN ".TABLE_STATE." ads on o.state=ads.State_Id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		 LEFT JOIN ".TABLE_EMP." e on o.offerLetterModifiedBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where o.id='$id' ";*/

		$sql = "select o.*,o.empType, concat(o.empTitle,' ',o.empFname,' ',o.empLname) as eName, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate, c.cityName as jobLocation, cc.cityName, cl.name as clients, p.name as projects,d.name,r.name as region, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as offerModifiedBy, s.State_Name  from ".TABLE_CANDIDATE_BACKUP." o
		LEFT JOIN ".TABLE_CITY." c on o.jobLocation=c.cityId
		LEFT JOIN ".TABLE_CITY." cc on o.city=cc.cityId
		LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		LEFT JOIN ".TABLE_EMP." e on o.offerLetterModifiedBy=e.empId
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		where 1=1 and o.candidateId='".$id."'  ";

		return $result = parent::result_grid_array($sql);
		//return $result=$this->db->query($sql)->result_array();
		//pre($result);die;
		//return $result;
	}


	function get_candidate_emp_details($field)
	{

		$sql = "select o.*,e.candidateId, e.empId  from ".$this->base_tbl." o
		LEFT JOIN ".TABLE_EMP." e on o.id=e.candidateId
		LEFT JOIN ".TABLE_DOCUMENT." d on e.empId=d.empId WHERE d.".$field." =''  order by id ASC";
		$result = $this->db->query($sql)->result_array();
		return $result;
	}

	function zonewiseEmployeeData($id='',$status='',$emptype='',$designationIN='')
	{
		if($status=='1')
		{
			$addsql .=" and e.status='".$status."'";
		}
		if($designationIN=='Y')
		{
			$addsql .=" and e.empDesination IN (12,26,27,31,33)";
		}else{
			if($id>0)
			{
				$addsql .=" and e.empDesination='".$id."'";
			}
		}
		if($emptype)
		{
			$addsql .=" and e.joiningFor='".$emptype."'";
		}

		$sql = "select r.name, count( NULLIF(e.status, '1' )) totalresign, count( NULLIF(e.status, '' )) as totalworking,  count(e.empId)as totaljoin, count( NULLIF(e.empEmailOffice, '' )) as totalOfficeemail,count( NULLIF(e.empMobile, '' )) as empMobile, count( NULLIF(e.empEmailPersonal, '' )) as empEmailPersonal, count( NULLIF(e.empDesination, '' )) as empDesination, count( NULLIF(e.empDept, '' )) as empDept, count( NULLIF(e.empDept, '' )) as empDept,  count( NULLIF(e.appointmentLetterSend, '' )) as appointmentLetterSend,  count(e.projects), count( NULLIF(e.empImage, '' )) as empImage, count( NULLIF(e.empEmailPersonal, 'NULL' )) as empEmailPersonal, count( reportingTo) as reporting, count( NULLIF(e.empDOJ , '' )) as totalDOJ, count( NULLIF(p.empDOB , '' )) as totalDOB, count( NULLIF(p.empGender , '' )) as totalGender, count( NULLIF(p.empFathersName , '' )) as totalFathersname, count( NULLIF(p.empMotherName , '' )) as totalMothersname, count( NULLIF(p.emergencyContactNumber , '' )) as totalEmergencycontactno, count( NULLIF(p.emergencyContactName , '' )) as totalEmergencycontactperson, count( NULLIF(s.ctc , '' )) as totalCTC,count( NULLIF(se.esicNumber , '' )) as totalESI, count( NULLIF(se.uanNumber , '' )) as totalUANno,  count( NULLIF(b.accountNo , '' )) as totalAccountno, count( NULLIF(b.bankName , '' )) as totalBankname, count( NULLIF(b.branch , '' )) as totalBankbranch, count( NULLIF(b.ifscCode , '' )) as totalIFSC, count( NULLIF(se.pfNumber , '' )) as totalpfNumber, count( NULLIF(pe.panNo , '' )) as totalPANno, count( NULLIF(pe.pan , '' )) as totalPANcardupload, count( NULLIF(pe.photoIdproof , '' )) as totalPhotoID, count( NULLIF(pe.addressProof , '' )) as totalAddressproof,count( NULLIF(pe.certificates10th , '' )) as totalcertificates10th, count( NULLIF(pe.heighestqualificationsCertificates , '' )) as totalheighestqualificationsCertificates, count( NULLIF(pe.cVSubmited , '' )) as totalResume    from ".TABLE_EMP." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." st on c.state=st.State_Id
		Left Join ".TABLE_REGION." r on st.region=r.id
		Left Join tbl_emp_personal p on p.empId=e.empId
		Left Join tbl_salarybreakup s on s.empId=e.empId
		Left Join tbl_emp_bank b on b.empId=e.empId
		Left Join ".TABLE_SERVICE." se on se.empId=e.empId
		Left Join tbl_emp_document pe on pe.empId=e.empId
		Where 1=1 ".$addsql." and e.empId !=10000 group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();
	}


	function zonewiseEmployeeDataforHRgrid($status='',$emptype='')
	{
		if($status=='1')
		{
			$addsql .=" and e.status='".$status."'";
		}

		$addsql .=" and e.empDesination IN(12,26,27,31,33)";
		if($emptype)
		{
			$addsql .=" and e.joiningFor='".$emptype."'";
		}

		$sql = "select r.name, count( NULLIF(e.status, '1' )) totalresign, count( NULLIF(e.status, '' )) as totalworking,  count(e.empId)as totaljoin, count( NULLIF(e.empEmailOffice, '' )) as totalOfficeemail,count( NULLIF(e.empMobile, '' )) as empMobile, count( NULLIF(e.empEmailPersonal, '' )) as empEmailPersonal, count( NULLIF(e.empDesination, '' )) as empDesination, count( NULLIF(e.empDept, '' )) as empDept, count( NULLIF(e.empDept, '' )) as empDept,  count( NULLIF(e.appointmentLetterSend, '' )) as appointmentLetterSend,  count(e.projects), count( NULLIF(e.empImage, '' )) as empImage, count( NULLIF(e.empEmailPersonal, 'NULL' )) as empEmailPersonal, count( reportingTo) as reporting, count( NULLIF(e.empDOJ , '' )) as totalDOJ, count( NULLIF(p.empDOB , '' )) as totalDOB, count( NULLIF(p.empGender , '' )) as totalGender, count( NULLIF(p.empFathersName , '' )) as totalFathersname, count( NULLIF(p.empMotherName , '' )) as totalMothersname, count( NULLIF(p.emergencyContactNumber , '' )) as totalEmergencycontactno, count( NULLIF(p.emergencyContactName , '' )) as totalEmergencycontactperson, count( NULLIF(s.ctc , '' )) as totalCTC,count( NULLIF(se.esicNumber , '' )) as totalESI, count( NULLIF(se.uanNumber , '' )) as totalUANno,  count( NULLIF(b.accountNo , '' )) as totalAccountno, count( NULLIF(b.bankName , '' )) as totalBankname, count( NULLIF(b.branch , '' )) as totalBankbranch, count( NULLIF(b.ifscCode , '' )) as totalIFSC, count( NULLIF(se.pfNumber , '' )) as totalpfNumber, count( NULLIF(pe.panNo , '' )) as totalPANno, count( NULLIF(pe.pan , '' )) as totalPANcardupload, count( NULLIF(pe.photoIdproof , '' )) as totalPhotoID, count( NULLIF(pe.addressProof , '' )) as totalAddressproof,count( NULLIF(pe.certificates10th , '' )) as totalcertificates10th, count( NULLIF(pe.heighestqualificationsCertificates , '' )) as totalheighestqualificationsCertificates, count( NULLIF(pe.cVSubmited , '' )) as totalResume    from ".TABLE_EMP." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." st on c.state=st.State_Id
		Left Join ".TABLE_REGION." r on st.region=r.id
		Left Join tbl_emp_personal p on p.empId=e.empId
		Left Join tbl_salarybreakup s on s.empId=e.empId
		Left Join tbl_emp_bank b on b.empId=e.empId
		Left Join ".TABLE_SERVICE." se on se.empId=e.empId
		Left Join tbl_emp_document pe on pe.empId=e.empId
		Where 1=1 ".$addsql." and e.empId !=10000 group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();
	}


	function zonewiseEmployeeDataforChart()
	{
		$region = explode(',',$this->session->userdata('admin_region')); 
		if($region['0']){
			$addsql .=" and r.id in(".$this->session->userdata('admin_region').")";
		}
		$sql = "select UPPER(r.name) as region, count( NULLIF(e.status, '1' )) totalresign, count( NULLIF(e.status, '' )) as totalworking, count(e.empId) as totaljoin from tbl_emp_master e
		Left Join tbl_mst_city c on e.jobLocation=c.cityId
		Left Join tbl_mst_state st on c.state=st.State_Id
		Left Join tbl_region r on st.region=r.id
		Where 1=1 ".$addsql." and e.empId !=10000 group by r.id order by r.name ";
		return $candidateList=$this->db->query($sql)->result_array();
	}
	function candidatesalaryList()
	{
		$sql = "select id, grossSalary, pf, esi, basic, hra, statutoryBonus, conveyance, specialAllow, medicalAllow from ".TABLE_CANDIDATE." order by id DESC ";
		return $candidateList=$this->db->query($sql)->result_array();
	}
	function getcandidatemedical()
	{
		$sql = "select id, statutoryBonus, medicalAllow from ".TABLE_CANDIDATE." where `empType` = 1 and costtype=2 and designation!=12 ";
		return $candidateList=$this->db->query($sql)->result_array();
		//pre($candidateList);die;

	}
	function getempmedical()
	{
		$sql = "select s.empId, s.statutoryBonus, s.medicalAllow from tbl_salarybreakup s
		Left Join ".TABLE_EMP." e on s.empId=e.empId
		Left Join ".TABLE_CANDIDATE." c on e.candidateId=c.id
		where  c.empType=1 and c.designation!=12 ";
		return $candidateList=$this->db->query($sql)->result_array();
		//pre($candidateList);die;

	}

	function zonewiseGSCEmployeeData($status='')
	{
		if($status=='1')
		{
			$addsql ="and e.status=".$status."";
		}
		$sql = "select r.name, count(e.empId)as totaljoin  from ".TABLE_EMP." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." st on c.state=st.State_Id
		Left Join ".TABLE_REGION." r on st.region=r.id
		Where e.empDesination IN(12,26,27,31,33) ".$addsql." and e.empId !=10000 group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();
	}
	function  zonewiseGSCcandidateData($status,$ldate='')
	{
		if(date('d')<21){
			$month =date('Y-m', strtotime(date('Y-m')." -1 month"));
		}else{
			$month = date('Y-m');
		}
		$date = date(''.$month.'-21');
		$sql = "select r.name, count(e.id) as totalshort from ".TABLE_CANDIDATE." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Where e.designation IN(12,26,27,31,33) and e.backout='1' and e.joiningDate BETWEEN '".$date."'and '".date('Y-m-d')."' and generateEmployyeid='0'  group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();
	}	
	function  nzonewiseGSCcandidateData($date)
	{
		$sql = "select r.name, count(e.id) as totalpendingjoin from ".TABLE_CANDIDATE." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Where e.designation IN(12,26,27,31,33) and e.backout='1' and e.joiningDate >'".$date."' and generateEmployyeid='0'  group by r.id order by r.name";
		return $candidateList=$this->db->query($sql)->result_array();
	}	

	function  candidateDataforCsv($id,$date)
	{
		if(date('d')<21){
			$month =date('Y-m', strtotime(date('Y-m')." -1 month"));
		}else{
			$month = date('Y-m');
		}
		$date = date(''.$month.'-21');
		$sql = "select r.name as region,  s.State_Name as state, c.cityName as jobLocation, e.id as candidateId, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, e.email,d.name as designation, DATE_FORMAT(e.joiningDate,'%d-%b-%Y') as joiningDate, e.mobile, e.gender, DATE_FORMAT(e.empDOB,'%d-%b-%Y') as empDOB, e.empFathersName, e.empMotherName,cl.name clients, p.name projects  from ".TABLE_CANDIDATE." e
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on e.designation=d.id
		LEFT JOIN ".TABLECLIENTS." cl on e.clients=cl.id
		LEFT JOIN ".TABLEPROJECT." p on e.projects=p.id
		Where e.designation IN(12,26,27,31,33) and e.backout='1' and e.joiningDate >='".$date."' and e.generateEmployyeid='0' order by r.name desc";
		return $candidateList=$this->db->query($sql)->result_array();
	}	
	function getCandiatePTax($job_state_id,$gross_salary)
	{
		$sql = "select * from tbl_p_tax_statewise where job_state_id=".$job_state_id." AND from_amt<=".$gross_salary." AND to_amt>=".$gross_salary;
		return $candidateList=$this->db->query($sql)->result_array();		
	}


	// 23-march-2018
	function getCandiateFamilyDetails($id)
	{
		$sql = "select * , if(dependent=1,'Yes','No') as  dependent , if(nominee=1,'Yes','No') as  nominee from tbl_candidate_family where empId='$id'";
		$result = parent::result_grid_array($sql);
		return $result;
	}
	function getCandiateEducationDetails($id)
	{
		$sql = "select * from tbl_candidate_education where empId='$id'";
		$result = parent::result_grid_array($sql);
		// pre($result);die;
		return $result;
	}
	//address query 17-April-18
	function getAddressDetails($eid=0)
	{

		$sql ="select a.same_address, a.id, a.address1, a.address2, a.state, a.city,a.zipcode, a.p_address1, a.p_address2, a.p_state, a.p_city,s.State_Name as pstate,ci.cityName as pcity, ps.State_Name as perstate, pci.cityName as percity from ".TABLE_CANDIDATE." a 
		
		LEFT JOIN ".TABLE_STATE." s on a.state=s.State_Id
		LEFT JOIN ".TABLE_CITY." ci on a.city=ci.cityId
		LEFT JOIN ".TABLE_STATE." ps on a.p_state=ps.State_Id
		LEFT JOIN ".TABLE_CITY." pci on a.p_city=pci.cityId
		where a.id=".$eid;

		return $result = $this->db->query($sql)->result_array();
	}
	//address query 17-April-18
	//personal details start
	function getPersonalDetails($id)
	{
		$sql ="select p.empDOB,p.empDOBactual, p.adharNo,p.blood_group,p.passport_no,p.dl_no,p.email,p.mobile,b.name as BloodGroupName from ".TABLE_CANDIDATE." p LEFT JOIN ".TABLE_BLOODGROUP." b on p.blood_group=b.id	where p.id=".$id;
		return $result = $this->db->query($sql)->result_array();
	}
	//personal details end
	//Bank Details start
	function getBankDetails($id)
	{
		$sql ="select * from ".TABLE_CANDIDATE." where id=".$id;
		return $result = $this->db->query($sql)->result_array();
	}
	//Bank Details end
	//menu list 
	function userMenuList(){
		$sql ="select * from ".TABLE_LEFTV2." where 1=1 and candidate_status=1 Order By id";
		return $result = $this->db->query($sql)->result_array();
	}	
	//menu list 
	//family details
	function familyDetail($id,$resultType='G')
	{
		if($id > 0)
		{
			$addsql .= " and empId=".$id;
		}
		$sql ="select id, empId, empRelation, personName, isLive, education, occupation, contactNo, dependent, nominee, nomineeOccupation, DATE_FORMAT(nomineeDOB,'%d-%b-%Y') nomineeDOB from ".TABLE_CANDIDATE_FAMILY."  where 1=1 ".$addsql."";
		
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
	//family details
	//education details
	function educationDetails($eid=0,$resultType='G')
	{
		$sql ="select * from ".TABLE_CANDIDATE_EDUCATION." where empId=".$eid." ";
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
	//education details
	//experience details
	function experienceDetail($id,$resultType='G')
	{
		if($id > 0)
		{
			$addsql .= " and empId=".$id;
		}
		$sql ="select id, empId, companyName, workFrom, workTo, designation, responsibilities, lastSalaryDrawn, reasonLeave, remarks, location from ".TABLE_CANDIDATE_EXP." where 1=1 ".$addsql."";
		
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
	//experience details
	//training details
	function trainingDetail($id,$resultType='G')
	{
		if($id > 0)
		{
			$addsql .= " and empId=".$id;
		}
		$sql ="select * from ".TABLE_CANDIDATE_TRAINING." where 1=1 ".$addsql."";
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
	//experience details
	//training details
	//professional contact details
	function professionalcnctDetail($id,$resultType='G')
	{
		if($id > 0)
		{
			$addsql .= " and empId=".$id;
		}
		$sql ="select * from ".TABLE_CANDIDATE_PROFESSIONAL_CNCT." where 1=1 ".$addsql."";
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
	
	//professional contact  details
	//personal contact details
	function personalcnctDetail($id,$resultType='G')
	{
		if($id > 0)
		{
			$addsql .= " and empId=".$id;
		}
		$sql ="select * from ".TABLE_CANDIDATE_PERSONAL_CNCT." where 1=1 ".$addsql."";
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
	
	//personal contact  details
	//Document Details
	function getDocumentDetails($id)
	{
		$sql ="select * from ".TABLE_CANDIDATE." where id=".$id;
		return $result = $this->db->query($sql)->result_array();
	}
	//Document Details
	//previous salary details
	function getPrevsalDetails($id)
	{
		$sql ="select * from ".TABLE_PREV_SAL." where empId=".$id;
		return $result = $this->db->query($sql)->row_array();
	}
	//previous salary details
	//aditional details
	function getadditionalDetails($id)
	{
		$sql ="select * from ".TABLE_ADDITIONAL." where empId=".$id;
		return $result = $this->db->query($sql)->row_array();
	}
	//aditional details

	function stateRoleCandPermissionList($empId){
		$permAccess = ''; 
		if(!empty($this->session->userdata('state_wise_access'))){
              $permAccess = $this->session->userdata('state_wise_access'); 
		}
		return $permAccess;	
	   }
}
