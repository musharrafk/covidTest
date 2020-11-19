<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class referal_model extends parent_model {
var $base_tbl = TABLE_REFERAL;
	var $u_column = 'id';

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
			$sql .= $rules->{'field'}.' '; // field name
			$sql .= $this->decodeGridOP($rules->{'op'},$rules->{'data'}).' '; // op, val
			$sql .= $objJson->{'groupOp'}.' '; // and, or
                    }
		}

		$sql = rtrim($sql, $objJson->{'groupOp'}.' ');
		return $sql.') ';
	}
	function get_referal_details($id=0, $resultType='G')
	{
		$role = explode(',',$this->session->userdata('admin_role_id')); 
		
		$region = explode(',',$this->session->userdata('admin_region')); 
		
		//echo  $resultType;
		
		if($id)
		{
			$addsql .= " and r.id='".$id."'";
		}
		if($region['0']){
		$addsql .=" AND r.id in(".$this->session->userdata('admin_region').")";
		}
		if($this->input->post('filters')!='') // search filters
		{
			$addsql .= " and ".self::decodeFilters($this->input->post('filters'));
		}
		$sql = "select r.*,DATE_FORMAT(r.calldate,'%d-%b-%Y') as calldate, DATE_FORMAT(r.isCreated,'%d-%b-%Y %H:%i:%s') as isCreated, s.State_Name, c.cityName, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as updatedBy, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as referedByname, re.name as region  from ".TABLE_REFERAL." r
		 LEFT JOIN ".TABLE_CITY." c on r.city=c.cityId
		 LEFT JOIN ".TABLE_STATE." s on r.state=s.State_Id
		 LEFT JOIN ".TABLE_REGION." re on s.region=re.id
		 LEFT JOIN ".TABLE_EMP." e on r.updatedBy=e.empId
		 LEFT JOIN ".TABLE_EMP." em on r.referedBy=em.empId
		 where 1=1 ".$addsql."";
  		 
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
	function csv()
	{
	if($region['0']){
		$addsql .=" AND re.id in(".$this->session->userdata('admin_region').")";
		}
		
		$sql = "select r.*,DATE_FORMAT(r.calldate,'%d-%b-%Y') as calldate, DATE_FORMAT(r.isCreated,'%d-%b-%Y %H:%i:%s') as isCreated, s.State_Name, c.cityName, concat(e.empTitle,' ',e.empFname,' ',e.empLname) as updatedBy, concat(em.empTitle,' ',em.empFname,' ',em.empLname) as referedBy, re.name as region   from ".TABLE_REFERAL." r
		 LEFT JOIN ".TABLE_CITY." c on r.city=c.cityId
		 LEFT JOIN ".TABLE_STATE." s on r.state=s.State_Id
		 LEFT JOIN ".TABLE_REGION." re on s.region=re.id
		 LEFT JOIN ".TABLE_EMP." e on r.updatedBy=e.empId
		 LEFT JOIN ".TABLE_EMP." em on r.referedBy=em.empId
		 where 1=1 ".$addsql."";
  			return $result = $this->db->query($sql)->result_array();
		
	}
		
	
	

}