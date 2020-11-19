<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class assets_model extends parent_model
{

	public function get_assets_list($resultType='G')
	{
		$addSql = "  ";
        $uId = $this->session->userdata('admin_id');
        $uRole = $this->session->userdata('role');
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
        $sql = "SELECT assets_master.asset_name,assets_master.id,assets_master.status,assets_details.*,
                DATE_FORMAT(assets_details.purchase_date,'%d-%m-%Y') as purchase_date,
                DATE_FORMAT(assets_details.created_at,'%d-%m-%Y') as created_at,
                CONCAT((tbl_emp_master.empFname),(' '),(tbl_emp_master.empLname)) as emp_name,
                tbl_emp_master.empId as emp_id
                FROM assets_master 
                INNER JOIN assets_details ON assets_details.asset_id = assets_master.id
                LEFT JOIN tbl_emp_master ON assets_details.emp_id = tbl_emp_master.empId
                -- LEFT JOIN tbl_mst_city ON tbl_emp_master.jobLocation = tbl_mst_city.cityId
                WHERE 1=1 AND FIND_IN_SET( ".$uRole.",assets_master.emp_role) ".$addSql;
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

	public function get_assets($value='')
	{
		$this->db->select('*');
		$this->db->from('assets_master');
		$this->db->where('status',1);
        $this->db->where('find_in_set("'.$this->session->userdata('role').'", emp_role) <> 0');
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			$result = $query->result_array();
		}else{
			$result = array();
		}
		return $result;
	}


    public function assets_details($asset_id)
    {
        $this->db->where('id',$asset_id);
        $query = $this->db->get('assets_details');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $result = $result[0];
        }else{
            $result = array();
        }
        return $result;
    }
    public function save_asset_data($assetArr)
    {   
        // print_r($assetArr);die();
        $res = $this->db->insert('assets_details',$assetArr);
        if ($res) {
            return $res;
        }else{
            return false;
        }
    }
    public function update_asset_data($assetArr,$asset_id)
    {
        $this->db->where('id',$asset_id);
        $this->db->update('assets_details',$assetArr);
        if ($this->db->affected_rows() == true) {
            return true;
        }else{
            return false;
        }
    }

    public function get_employee_assign($resultType='G')
    {
        $addSql = " ";
        $uId = $this->session->userdata('admin_id');
        $uRole = $this->session->userdata('role');
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
        $sql = "SELECT 
            -- assets_master.asset_name,
            -- assets_master.id,assets_master.status,
            -- assets_details.*,
            -- DATE_FORMAT(endDate,'%d-%m-%Y')
            assets_employee_details.*,
            DATE_FORMAT(assets_employee_details.assign_date,'%d-%m-%Y') as assign_date,
            -- DATE_FORMAT(assets_employee_details.receive_date,'%d-%m-%Y') as receive_date,
            DATE_FORMAT(assets_employee_details.receive_from,'%d-%m-%Y') as receive_from,
            CONCAT((tbl_emp_master.empFname),(' '),(tbl_emp_master.empLname)) as emp_name,
            tbl_emp_master.empId as emp_id,tbl_emp_master.status as emp_status,
            tbl_mst_city.cityId,
            tbl_mst_city.cityName as location
            -- DATE_FORMAT(assets_employee_details.receive_from,'%d-%M-%Y') as receive_from
            -- tbl_mst_city','tbl_mst_city.cityId=tbl_emp_master.jobLocation
            FROM assets_employee_details 
            LEFT JOIN asset_maped ON assets_employee_details.emp_id = asset_maped.emp_id
            LEFT JOIN assets_details ON asset_maped.asset_detail_id = assets_details.id 
            LEFT JOIN assets_master ON assets_details.asset_id = assets_master.id
            -- LEFT JOIN assets_details ON assets_master.id = assets_details.asset_id 
            LEFT JOIN tbl_emp_master ON assets_employee_details.emp_id = tbl_emp_master.empId
            LEFT JOIN tbl_mst_city ON tbl_emp_master.jobLocation = tbl_mst_city.cityId
            WHERE 1=1 AND FIND_IN_SET( ".$uRole.",assets_master.emp_role) ".$addSql." GROUP BY asset_maped.emp_id";
               
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


    public function assign_assets_details($assign_id)
    {
        $this->db->select("CONCAT((tbl_emp_master.empFname),(' '),(tbl_emp_master.empLname)) as emp_name");
        $this->db->select("tbl_emp_master.empId as emp_id");
        $this->db->select('assets_employee_details.*');
        $this->db->from('assets_employee_details');
        $this->db->join('tbl_emp_master','tbl_emp_master.empId = assets_employee_details.emp_id','LEFT');
        $this->db->where('id',$assign_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $result = $result[0];
        }else{
            $result = array();
        }
        return $result;
    }

    public function fetch_all_employees($value='')
    {
        // $this->db->select('*');
        $this->db->select("CONCAT((tbl_emp_master.empFname),(' '),(tbl_emp_master.empLname)) as emp_name");
        $this->db->select("tbl_emp_master.empId as emp_id");
        $this->db->where('status',1);
        $query = $this->db->get('tbl_emp_master');
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;
    }
    public function assgin_employees_details()
    {
        $this->db->select('assets_employee_details.*');
        $this->db->from('assets_employee_details');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            // $result = $result[0];
        }else{
            $result = array();
        }
        return $result;
    }
    public function assets_details_by_asset_id($asset_id,$emp_id='')
    {
        $this->db->select('assets_details.*');
        $this->db->select('assets_master.asset_name');
        // $this->db->select('assets_employee_details.emp_id as empId');
        // $this->db->select('assets_assigned_map.assigned_assset as New_ID');
        $this->db->from('assets_details');
        $this->db->join('assets_master','assets_details.asset_id = assets_master.id');
        // $this->db->join('assigned_assets_id_map','assets_details.asset_id = assigned_assets_id_map.asset_id','LEFT');
        // $this->db->join('assets_employee_details','assigned_assets_id_map.assigned_detail_id = assets_employee_details.id','LEFT');
        // $this->db->join('assets_assigned_map','assigned_assets_id_map.assigned_detail_id = assets_assigned_map.assigned_detail_id','LEFT');
        $this->db->where_in('assets_details.asset_id',$asset_id);
        // $this->db->where('assets_employee_details.emp_id',$emp_id);
        // $this->db->group_by('assets_details.id'); 
        // $this->db->where('assets_details.status',1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            // $result = $result[0];
        }else{
            $result = array();
        }
        return $result;
    }
    public function assets_details_by_asset_id4($asset_id,$emp_id='',$details)
    {
        $uids = implode(",", $details);
        if (!empty($uids)) {
            $con = "and (assets_details.status=1 OR assets_details.id IN( ".$uids."))";
            // $join = "left join assets_employee_details on ";
        }else{
            $con = "and (assets_details.status=1)";
        }
        $sql = "SELECT assets_details.*,assets_master.asset_name  FROM `assets_details` left join assets_master on assets_master.id=assets_details.asset_id WHERE assets_details.asset_id IN(".implode(",", $asset_id).")   ". $con."";
        $result = $this->db->query($sql)->result_array(); 
        return $result;
    }

    public function assetsAssignedDetailed($emp_id='')
    {   
        $sql = "SELECT GROUP_CONCAT(am.asset_detail_id) as asset_detail_id  FROM `assets_employee_details` as aed  left join asset_maped as am on aed.emp_id=am.emp_id  left join assets_details ad on am.asset_detail_id=ad.id left join assets_master as ams on ams.id=ad.asset_id WHERE am.status=1 and am.emp_id=".$emp_id." GROUP BY am.emp_id";

         $result = $this->db->query($sql)->result_array(); 
         $assign_array=explode(",",$result[0]['asset_detail_id']);
         return  $assign_array;
    }
   
    public function assets_details_by_asset_id1($asset_id)
    {
        $this->db->select('assigned_assets_id_map.asset_id');
        $this->db->select('assets_employee_details.emp_id as empID');
        $this->db->select('assets_assigned_map.assigned_assset as ID1');
        $this->db->from('assigned_assets_id_map');
        $this->db->join('assets_employee_details','assigned_assets_id_map.assigned_detail_id = assets_employee_details.id','INNER');
        $this->db->join('assets_assigned_map','assigned_assets_id_map.assigned_detail_id = assets_assigned_map.assigned_detail_id','INNER');
        $this->db->where_in('assigned_assets_id_map.asset_id',$asset_id);
        $this->db->group_by('assets_assigned_map.assigned_assset'); 
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result; 
    }
    public function assets_details_by_asset_id2($asset_id)
    {
        // return $asset_id;
        $this->db->select('assets_assigned_map.assigned_detail_id');
        $this->db->select('assets_assigned_map.assigned_assset');
        $this->db->select('assets_employee_details.emp_id as empID');
        $this->db->select('assets_employee_details.asset_tag');
        // $this->db->select('assets_details.*');
        $this->db->select('assets_master.asset_name');
        $this->db->from('assets_assigned_map');
        $this->db->join('assets_employee_details','assets_assigned_map.assigned_detail_id = assets_employee_details.id','LEFT');
        $this->db->join('assets_details','assets_assigned_map.assigned_assset = assets_details.id','LEFT');
        $this->db->join('assets_master','assets_details.asset_id = assets_master.id','LEFT');
        $this->db->where_in('assets_assigned_map.assigned_assset',$asset_id);
        // $this->db->where('assets_assigned_map.assigned_detail_id',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result; 
    }

    public function save_assign_asset_data($assigned_asssets='')
    {
        $this->db->insert('assets_employee_details',$assigned_asssets);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }else{
            return false;
        }      
    }
    public function update_assign_asset_data($assetTag,$id)
    {
        $this->db->where('id',$id);
        $this->db->update('assets_employee_details',$assetTag);
         if ($this->db->affected_rows() == true) {
            return true;
        }else{
            return false;
        }      
    }
    public function beforeOldEmpUpdate($id)
    {
        $this->db->select('assets_employee_details.*');
        $this->db->from('assets_employee_details');
        $this->db->where('assets_employee_details.id',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $result = $result[0];
        }else{
            $result = array();
        }
        return $result;
    }
    public function beforeStatusUpdate($id='',$emp_id='')
    {
        $this->db->select('assets_employee_details.emp_id');
        $this->db->select('asset_maped.asset_detail_id');
        // $this->db->select('asset_maped.emp_id as empId');
        // $this->db->select('assets_master.asset_name');
        $this->db->from('assets_employee_details');
        $this->db->join('asset_maped','assets_employee_details.emp_id = assets_employee_details.emp_id','LEFT');
        // $this->db->join('assets_details','assets_assigned_map.assigned_assset = assets_details.id','LEFT');
        // $this->db->join('assets_master','assets_details.asset_id = assets_master.id','LEFT');
        // $this->db->where_in('assets_assigned_map.assigned_assset',$asset_id);
        $this->db->where('assets_employee_details.id',$id);
        $this->db->where('asset_maped.emp_id',$emp_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $response = $query->result_array();
            
        }else{
            return $response = array();
        }      
    }
    public function getAssignEmployeeDetail($empId)
    {
        $this->db->select("CONCAT((tbl_emp_master.empFname),(' '),(tbl_emp_master.empLname)) as emp_name");
        $this->db->select("tbl_emp_master.empId as emp_id");
        $this->db->select('tbl_mst_city.cityId');
        $this->db->select('tbl_mst_city.cityName as location');
        $this->db->from('tbl_emp_master');
        $this->db->join('tbl_mst_city','tbl_mst_city.cityId=tbl_emp_master.jobLocation');
        $this->db->where('tbl_emp_master.empId',$empId);
        // $this->db->where('tbl_mst_city.status',1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $response = $query->result_array();
            return $response[0];
        }else{
            return $response = array();
        }
    }
    
    public function save_assigned_asset_id_info($assignAssetArr,$id)
    {

        if (count($assignAssetArr) > 0) {
            $this->db->select('*');
            $this->db->where('assigned_detail_id',$id);
            $query = $this->db->get('assigned_assets_id_map');
            if ($query->num_rows() > 0) {
            $this->db->where('assigned_detail_id',$id);
            $this->db->delete('assigned_assets_id_map');
            }
            $this->db->insert_batch('assigned_assets_id_map',$assignAssetArr);
            return true;
        }        
    } 
    public function save_assigned_assets_info($assignAssetArr,$id)
    {

        if (count($assignAssetArr) > 0) {
            $this->db->select('*');
            $this->db->where('assigned_detail_id',$id);
            $query = $this->db->get('assets_assigned_map');
            if ($query->num_rows() > 0) {
            $this->db->where('assigned_detail_id',$id);
            $this->db->delete('assets_assigned_map');
            }
            $this->db->insert_batch('assets_assigned_map',$assignAssetArr);
            return true;
        }        
    } 
    public function save_assigned_assets_info1($assignAssetArr,$emp_id,$assets_detail_id='',$unchecked_assets='')
    {
        // print_r($assets_detail_id);
        // die();
     
        if($unchecked_assets!='' && count($unchecked_assets)>0 ){
            foreach($unchecked_assets as $id){
              $this->db->where('asset_detail_id',$id);
              $this->db->where('emp_id',$emp_id);
              $this->db->delete('asset_maped');   
            }
                  
        }
        if (count($assignAssetArr) > 0) {
            foreach($assets_detail_id as $id){ 
                $this->db->select('*');
                // $this->db->where('asset_detail_id',$id);
                $this->db->where('emp_id',$emp_id);
                $query = $this->db->get('asset_maped');
                if ($query->num_rows() > 0) {
                    $this->db->where('asset_detail_id',$id);
                    $this->db->where('emp_id',$emp_id);
                    $this->db->delete('asset_maped');
                }
            }
            $this->db->insert_batch('asset_maped',$assignAssetArr);
            return true;
        } 
        return true;       
    }
    public function update_assigned_assets_info($updateArr,$id='')
    {
        $this->db->update_batch('assets_details', $updateArr, 'id');    
        return true;   
    }

    public function update_assets_mapped($assignAssetArr,$emp_id)
    {
        
         if (count($assignAssetArr) > 0) {
            $this->db->select('*');
            // $this->db->where('asset_detail_id',$id);
            $this->db->where('emp_id',$emp_id);
            $query = $this->db->get('asset_maped');
                if ($query->num_rows() > 0) {
                // $this->db->where('asset_detail_id',$id);
                    foreach($assignAssetArr as $asset_detail_id){
                        $this->db->set('status', 2);
                        $this->db->where('emp_id',$emp_id);
                        $this->db->where('asset_detail_id',$asset_detail_id);
                        $this->db->update('asset_maped');
                    }
                    
                }
          
            return true;
        } 
    }

    public function assignedInfoMap1($emp_id)
    {
        $this->db->select('asset_maped.*');
        $this->db->select('assets_employee_details.*');
        // $this->db->select('assets_employee_details.emp_id as empId');
        $this->db->select('assets_details.*');
        $this->db->select('assets_master.asset_name,assets_master.emp_role');
        $this->db->from('asset_maped');
        $this->db->join('assets_employee_details','asset_maped.emp_id = assets_employee_details.emp_id','LEFT');
        $this->db->join('assets_details','asset_maped.asset_detail_id = assets_details.id','INNER');
        $this->db->join('assets_master','assets_details.asset_id = assets_master.id','INNER');
        $this->db->where('asset_maped.emp_id',$emp_id);
        $this->db->where('find_in_set("'.$this->session->userdata('role').'", assets_master.emp_role) <> 0');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;       
    }
    public function assignedInfoMap2($emp_id)
    {
        $this->db->select('asset_maped.*');
        // $this->db->select('assets_employee_details.*');
        // $this->db->select('assets_employee_details.emp_id as empId');
        $this->db->select('assets_details.*');
        $this->db->select('assets_master.asset_name');
        $this->db->from('asset_maped');
        // $this->db->join('assets_employee_details','assets_assigned_map.assigned_detail_id = assets_employee_details.id','INNER');
        $this->db->join('assets_details','asset_maped.asset_detail_id = assets_details.id','INNER');
        $this->db->join('assets_master','assets_details.asset_id = assets_master.id','INNER');
        $this->db->where('asset_maped.emp_id',$emp_id);
         // $this->db->where('asset_maped.status',1);
        $this->db->group_by('assets_details.asset_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;       
    }


    public function assignedInfoAdmin($emp_id)
    {
        $this->db->select('asset_maped.*');
        $this->db->select('assets_employee_details.*');
        // $this->db->select('assets_employee_details.emp_id as empId');
        $this->db->select('assets_details.*');
        $this->db->select('assets_master.asset_name,assets_master.emp_role');
        $this->db->from('asset_maped');
        $this->db->join('assets_employee_details','asset_maped.emp_id = assets_employee_details.emp_id','LEFT');
        $this->db->join('assets_details','asset_maped.asset_detail_id = assets_details.id','INNER');
        $this->db->join('assets_master','assets_details.asset_id = assets_master.id','INNER');
        $this->db->where('asset_maped.emp_id',$emp_id);
        
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;       
    }
    // public function assignedInfoMap($id)
    // {
    //     $this->db->select('assets_assigned_map.*');
    //     $this->db->select('assets_employee_details.*');
    //     $this->db->select('assets_employee_details.emp_id as empId');
    //     $this->db->select('assets_details.*');
    //     $this->db->select('assets_master.asset_name');
    //     $this->db->from('assets_assigned_map');
    //     $this->db->join('assets_employee_details','assets_assigned_map.assigned_detail_id = assets_employee_details.id','INNER');
    //     $this->db->join('assets_details','assets_assigned_map.assigned_assset = assets_details.id','INNER');
    //     $this->db->join('assets_master','assets_details.asset_id = assets_master.id','INNER');
    //     $this->db->where('assets_assigned_map.assigned_detail_id',$id);
    //     $query = $this->db->get();
    //     if ($query->num_rows() > 0) {
    //         $result = $query->result_array();
    //     }else{
    //         $result = array();
    //     }
    //     return $result;       
    // }

    public function assignedInfoMap($id)
    {
        $this->db->select('asset_maped.*');
        $this->db->select('assets_employee_details.*');
        $this->db->select('assets_employee_details.emp_id as empId');
        $this->db->select('assets_details.*');
        $this->db->select('assets_master.asset_name');
        $this->db->from('asset_maped');
        // $this->db->join('assets_employee_details','asset_maped.asset_detail_id = assets_employee_details.id','INNER');
        $this->db->join('assets_details','assets_assigned_map.assigned_assset = assets_details.id','INNER');
        $this->db->join('assets_master','assets_details.asset_id = assets_master.id','INNER');
        $this->db->where('asset_maped.asset_detail_id',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;       
    }

    public function assignedInfoAssetIdMap($id)
    {
        $this->db->select('assigned_assets_id_map.*');/*
        $this->db->select('assets_employee_details.*');
        $this->db->select('assets_details.*');*/
        $this->db->from('assigned_assets_id_map');
        // $this->db->join('assets_employee_details','assigned_assets_id_map.assigned_detail_id = assets_employee_details.id','INNER');
        // $this->db->join('assets_details','assigned_assets_id_map.assigned_assset = assets_details.id','INNER');
        $this->db->where('assigned_assets_id_map.assigned_detail_id',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        }else{
            $result = array();
        }
        return $result;       
    }
    public function assignedUserDetail($id)
    {
        $this->db->select('assets_employee_details.*');/*
        $this->db->select('assets_employee_details.*');
        $this->db->select('assets_details.*');*/
        $this->db->from('assets_employee_details');
        // $this->db->join('assets_employee_details','assigned_assets_id_map.assigned_detail_id = assets_employee_details.id','INNER');
        // $this->db->join('assets_details','assigned_assets_id_map.assigned_assset = assets_details.id','INNER');
        $this->db->where('id',$id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            $result = $result[0];
        }else{
            $result = array();
        }
        return $result;       
    }
    public function existsAssigned($emp_id)
    {
        $this->db->select('assets_employee_details.*');/*
        $this->db->select('assets_employee_details.*');
        $this->db->select('assets_details.*');*/
        $this->db->from('assets_employee_details');
        // $this->db->join('assets_employee_details','assigned_assets_id_map.assigned_detail_id = assets_employee_details.id','INNER');
        // $this->db->join('assets_details','assigned_assets_id_map.assigned_assset = assets_details.id','INNER');
        $this->db->where('emp_id',$emp_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $result = $query->result_array();
            // $result = $result[0];
        }else{
            $result = array();
        }
        return $result;       
    }

    public function get_employee_assign_list($resultType='G')
    {
        $addSql = " ";
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
         $sql = "SELECT 
            -- assets_master.asset_name,
            -- assets_master.id,assets_master.status,
            -- assets_details.*,
            -- DATE_FORMAT(endDate,'%d-%m-%Y')
            assets_employee_details.*,
            DATE_FORMAT(assets_employee_details.assign_date,'%d-%m-%Y') as assign_date,
            DATE_FORMAT(assets_employee_details.receive_from,'%d-%m-%Y') as receive_from,
            CONCAT((tbl_emp_master.empFname),(' '),(tbl_emp_master.empLname)) as emp_name,
            tbl_emp_master.empId as emp_id,tbl_emp_master.status as emp_status,
            tbl_mst_city.cityId,
            tbl_mst_city.cityName as location
            -- DATE_FORMAT(assets_employee_details.receive_from,'%d-%M-%Y') as receive_from
            -- tbl_mst_city','tbl_mst_city.cityId=tbl_emp_master.jobLocation
            FROM assets_employee_details 
            -- LEFT JOIN assets_master ON assets_employee_details.asset_id = assets_master.id
            -- LEFT JOIN assets_details ON assets_master.id = assets_details.asset_id 
            LEFT JOIN tbl_emp_master ON assets_employee_details.emp_id = tbl_emp_master.empId
            LEFT JOIN tbl_mst_city ON tbl_emp_master.jobLocation = tbl_mst_city.cityId
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
    }

    public function status_update($id,$dataArr)
    {
        $this->db->where('id',$id);
        $this->db->update('assets_employee_details',$dataArr);
        if ($this->db->affected_rows() == true) {
            return true;
        }else{
            return false;
        }
    }
}