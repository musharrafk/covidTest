<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class confirmation_model extends parent_model {

	function decodeFilters($filters)
	{
		$sql = ' (';
		$objJson = json_decode($filters);
              //  pre($objJson);
		foreach($objJson->{'rules'} as $rules)
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
			$sql .= $rules->{'field'}.' '; // field name
			$sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' '; // op, val
			$sql .= $objJson->{'groupOp'}.' '; // and, or
		}
	}

	$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
	return $sql.') ';
}


function get_employee_details($empId=0, $resultType='G')
{

	$addsql = '';
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
	}
	if($empId > 0)
	{
		$addsql .= " and e.empId=".$empId;
	}
	if($this->input->post('status')!='')
	{
		$addsql .= " and e.status='".$this->input->post('status')."' ";
	}
	else
	{
			//$addsql .= " and e.status='1' ";
	}

		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
				$sql = "select conf.recomandation, conf.empId as log, e.empId, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, d.name, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ, DATE_FORMAT(DATE_ADD(e.empDOJ,INTERVAL 6 MONTH), '%d %b, %Y')as completedDate, de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as manager,em.empEmailOffice as manageremail, if(ser.empType='2','Probation','Confirm') as empStatus, conf.managerFeedback, conf.hrFeedback, if(conf.recomandation='2','Extenstion','Confirmation')as recomandation, DATE_FORMAT(DATE_ADD(conf.effectiveFrom,INTERVAL 3 MONTH), '%d %b, %Y')as extensionDate, DATEDIFF((DATE_FORMAT(DATE_ADD(conf.effectiveFrom,INTERVAL 3 MONTH), '%Y-%m-%d')),now()) as nofodays, sal.ctc  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_SALARYBREAKUP." sal on e.empId=sal.empId
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		Left Join ".TABLE_CONFIRMATION_REQUEST." conf on e.empId=conf.empId
		where 1=1 ".$addsql." and  e.empDOJ <=DATE_ADD(Now(), INTERVAL - 5 MONTH) and  e.joiningFor='1'  and e.status='1' ";
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
	
	
function get_details($empId=0, $resultType='G')
{

	$addsql = '';
	$region = explode(',',$this->session->userdata('admin_region')); 
	if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
	}
	if($empId > 0)
	{
		$addsql .= " and e.empId=".$empId;
	}
	if($this->input->post('status')!='')
	{
		$addsql .= " and e.status='".$this->input->post('status')."' ";
	}
	else
	{
			//$addsql .= " and e.status='1' ";
	}

		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select e.joiningFor, e.empDesination, e.empId,e.empEmailOffice, d.name, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName,  DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ,  DATE_FORMAT(DATE_ADD(e.empDOJ,INTERVAL 6 MONTH), '%Y-%m-%d')as effectiveFrom, e.empEmailPersonal, e.empEmailOffice , de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation, pr.name as projects, cl.name as clients, em.empId as managerId, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as manager,em.empEmailOffice as manageremail, emt.name as employeeType, sal.ctc, cr.empId as exist  from ".TABLE_EMP." e
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SALARYBREAKUP." sal on e.empId=sal.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		Left Join ".TABLE_CONFIRMATION_REQUEST." cr on e.empId=cr.empId
		where 1=1 ".$addsql." and e.empId!=10000";
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
	
function get_employee_confirmation_log($empId=0)
{
	//$empId='11853';
				/*echo $sql = "select conf.empId, conf.recomandation,   conf.managerFeedback, conf.hrFeedback, if(conf.recomandation='2','Extenstion','Confirmation')as recomandation, DATE_FORMAT(DATE_ADD(conf.effectiveFrom,INTERVAL 3 MONTH), '%d %b, %Y')as extensionDate, DATEDIFF((DATE_FORMAT(DATE_ADD(conf.effectiveFrom,INTERVAL 3 MONTH), '%Y-%m-%d')),now()) as nofodays, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName from ".TABLE_CONFIRMATION_REQUEST_LOG." conf 
				inner Join ".TABLE_EMP." e on conf.empId=e.empId
				where 1=1 and conf.empId=".$empId;*/
		
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		 $sql = "select conf.empId, conf.recomandation,  concat(e.empTitle,' ',e.empFname,' ',e.empLname) as empName, d.name, DATE_FORMAT(e.empDOJ,'%d-%b-%Y') empDOJ, DATE_FORMAT(conf.requestDate, '%d %b, %Y')as requestDate, DATE_FORMAT(DATE_ADD(e.empDOJ,INTERVAL 6 MONTH), '%d %b, %Y')as completedDate, de.name as desination,r.name as region,s.State_Name as state,c.cityName as jobLocation, if(ser.empType='2','Probation','Confirm') as empStatus, conf.managerFeedback, conf.hrFeedback, if(conf.recomandation='2','Extenstion','Confirmation')as recomandation, DATE_FORMAT(DATE_ADD(conf.effectiveFrom,INTERVAL 3 MONTH), '%d %b, %Y')as extensionDate, DATEDIFF((DATE_FORMAT(DATE_ADD(conf.effectiveFrom,INTERVAL 3 MONTH), '%Y-%m-%d')),now()) as nofodays, sal.ctc  from ".TABLE_CONFIRMATION_REQUEST_LOG." conf
		Left Join ".TABLE_EMP." e on conf.empId=e.empId
		Left Join ".TABLE_DEPT." d on e.empDept=d.id
		Left Join ".TABLE_MASTER_DESIGNATION." de on e.empDesination=de.id
		Left Join ".TABLE_CITY." c on e.jobLocation=c.cityId
		Left Join ".TABLE_STATE." s on c.state=s.State_Id
		Left Join ".TABLE_REGION." r on s.region=r.id
		Left Join ".TABLEPROJECT." pr on e.projects=pr.id
		Left Join ".TABLECLIENTS." cl on e.clients=cl.id
		Left Join ".TABLE_SALARYBREAKUP." sal on e.empId=sal.empId
		Left Join ".TABLE_EMP." em on e.reportingTo=em.empId
		Left Join ".TABLE_SERVICE." ser on e.empId=ser.empId
		Left Join ".TABLE_EMPTYPE." emt on ser.empType=emt.id
		where 1=1 ".$addsql." and conf.empId=".$empId;
		//echo $sql; 

		$result = parent::result_grid_array($sql);
		//pre($result);die;
		return $result;
	}
		
	


	

	
}