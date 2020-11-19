<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class report_model extends parent_model {
	var $base_tbl = TABLE_CANDIDATE;
	var $u_column = 'id';
	
function candiateList($region=0,$joblocation=0,$designation=0,$emptype=0,$costtype=0,$letterissued=0)
	{
		if($region){ 
		$addsql =" AND r.id=".$region."";
		}
		if($designation){ 
		$addsql .=" AND o.designation=".$designation."";
		}
		if($joblocation){
		$addsql .=" AND o.jobLocation=".$joblocation."";
		}
		if($emptype){
		$addsql .=" AND o.empType=".$emptype."";
		}
		if($costtype){
		$addsql .=" AND o.costType=".$costtype."";
		}
		if($letterissued){
		$addsql .=" AND o.offerLetterIssue=".$letterissued."";
		}
		
		 $sql = "select o.*,concat(o.empTitle,' ',o.empFname,' ',o.empLname) as name, DATE_FORMAT(o.joiningDate,'%d-%b-%Y') as joiningDate, c.cityName as city,cl.name as clients,p.name as projects,d.name as designation,r.id as regionid,r.name as region  from ".$this->base_tbl." o
		 LEFT JOIN ".TABLE_CITY." c on o.jobLocation=c.cityId
		  LEFT JOIN ".TABLE_STATE." s on c.state=s.State_Id
		 LEFT JOIN ".TABLE_REGION." r on s.region=r.id
		 LEFT JOIN ".TABLECLIENTS." cl on o.clients=cl.id
		 LEFT JOIN ".TABLEPROJECT." p on o.projects=p.id
		 LEFT JOIN ".TABLE_MASTER_DESIGNATION." d on o.designation=d.id
		  where o.status=1 ".$addsql." order by o.id DESC ";
  		  //where o.status=1 AND o.basic>0 ".$addsql." order by o.id DESC";
		
		$result=$this->db->query($sql)->result_array();
		//pre($result);die;
		return $result;
	}

	



}