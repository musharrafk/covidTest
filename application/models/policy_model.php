<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class policy_model extends parent_model {
	var $base_tbl = TABLE_POLICY;
	var $base_tbl1 = TABLE_CATEGORY;
	var $u_column = 'id';


  
    function policyList()
    {
    	if($this->session->userdata('admin_id')!=10000){
			if($this->session->userdata('clients')){
			$addsql =" and clients=".$this->session->userdata('clients')."";
			}
		}
    	//$sql = "select p.description,p.id as id,p.policyTitle as policyTitle,c.categoryName from ".TABLE_POLICY." p LEFT JOIN ".TABLE_CATEGORY." c on p.Category=c.catId where 1=1 ".$addsql."";
		$sql = "select p.description,p.id as id,p.policyTitle as policyTitle from ".TABLE_POLICY." p where 1=1 ".$addsql."";
    	$result=$this->db->query($sql)->result_array();
    	return $result;
    	
    }



    function clientList()
    {
        $sql = "select * from ".TABLECLIENTS."  where 1=1 and status='1'  Order By name ASC";
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    function projectList()
    {
        $sql = "select * from ".TABLEPROJECT."  where 1=1  Order By name ASC";
        $result = $this->db->query($sql)->result_array();
        return $result;
    }
	    //old method
    function get_policy_details($resultType='G')
	    {
		    $addSql = "  ";

		    if($this->input->post('filters')!='') // search filters
		    {
			    $addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		    }
		    $sql="select pt.id as id,pt.policyTitle as name,pt.description as description,ct.name as clientsName,prt.name as projectsName,tcat.categoryName as categoryName from ".TABLE_POLICY." pt left join ".TABLECLIENTS." ct on pt.clients=ct.id left join ".TABLEPROJECT." prt on pt.projects=prt.id left join ".TABLE_CATEGORY." tcat  on pt.category=tcat.catId where 1=1 ".$addSql;
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


	    function duplicatePolicy($name)
	    {
		    /*$sql="select id from ".TABLE_POLICY." where policyTitle='".$name."'";
		    $result=$this->db->query($sql)->result_array();
		    return $result['0'];*/
	    }



            function listing($table, $col='', $colVal='')
    {
        $addSql = '';

        if($col!='' and $colVal!='')
        {
            $addSql .= " where ".$col."='".$colVal."'";
        }
        $tables.=" a left join ".TABLEPROJECT." b on a.projects=b.name";
        $sql = "select * from ".$table.$tables.$addSql;
        //pre($sql);
        return $this->db->query($sql)->result_array();
    }
     //10-jan-17
	 function handbookList()
    {
    	if($this->session->userdata('admin_id')!=10000){
		if($this->session->userdata('clients')){
		$addsql =" and clients=".$this->session->userdata('clients')."";
		}
		}
    		$sql = "select p.description,p.id as id,p.handbookTitle as policyTitle,c.categoryName from ".TABLE_HANDBOOK." p LEFT JOIN ".TABLE_CATEGORY." c on p.Category=c.catId where 1=1 ".$addsql."";
    		$result=$this->db->query($sql)->result_array();
    		return $result;
    	
    }
     //10-jan-17

    function getCompanyPolicyList(){
		$sql = "select * from ".TABLE_HANDBOOK."  where isDisplayed = 1";
        $result = $this->db->query($sql)->result_array();
        return $result;
	}


    }