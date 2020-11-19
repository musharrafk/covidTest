<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class modules_model extends parent_model {
	var $base_tbl = 'tbl_module';
	var $u_column = 'id';

	function get_designation($resultType='G')
	{
		$addSql = "  ";

		if($this->input->post('filters')!='') // search filters
		{
			$addSql .= " and ".parent::decodeFilters($this->input->post('filters'));
		}

		//select d.*, (select GROUP_CONCAT(m.name) from tbl_designation_access a inner join tbl_module m on a.moduleId=m.id where a.designationId=d.id ) as b from tbl_mst_designation d where 1=1

		//$sql = "select *, () as b from tbl_mst_designation where 1=1 ".$addSql;
		$sql = "select d.*, (select GROUP_CONCAT(m.name) from tbl_designation_access a inner join tbl_module m on a.moduleId=m.id where a.designationId=d.id and m.parentId!='0' order by m.setOrder) as b from tbl_mst_designation d where 1=1 ".$addSql;
		
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

	function get_module_details($id)
	{
		return $this->db->query("select * from tbl_module where id='".$id."'")->result_array();
	}
	
	function module_listing($sel=0)
	{
		$html = '';
		
		$i=0;
		foreach($this->db->query("select * from tbl_module where parentId='0' order by setOrder")->result_array() as $res1)
		{
			$html .= '<option value="'.$res1['id'].'" '.($res1['id']==$sel?'selected':'').'>'.$res1['name'].'</option>';
			//$html[$i][$res1['id']]=$res1['name'];
			foreach($this->db->query("select * from tbl_module where parentId='".$res1['id']."' order by setOrder")->result_array() as $res2)
			{
				$j=0;
				$html .= '<option value="'.$res2['id'].'" '.($res2['id']==$sel?'selected':'').'>&nbsp;&nbsp;&nbsp;'.$res2['name'].'</option>';
				//$html[$i][$j][$res2['id']]='&nbsp;&nbsp;&nbsp;'.$res2['name'];
				$j++;
			}
			$i++;
		}
		
		return $html;
	}
	
	function get_tree($parent_id = '0', $spacing = '', $exclude = '', $cat_tree_arr = '')
	{
		if (!is_array($cat_tree_arr))
			$cat_tree_arr = array();

		$cat_query = "select id, name, url, icon, setOrder from tbl_module where parentId = '" . $parent_id . "' AND status=1 order by setOrder";
		$resArr = $this->db->query($cat_query)->result_array();

		foreach ($resArr as $key=>$value) {
			if ($exclude != $value['id'])
				$cat_tree_arr[] = array('id' => $value['id'], 'url'=>$value['url'], 'p_id' => $parent_id, 'icon'=>$value['icon'], 'setOrder'=>$value['setOrder'], 'name' => $spacing . $value['name']);

			$cat_tree_arr = $this->get_tree($value['id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $cat_tree_arr);
		}

		return $cat_tree_arr;

	}

	function get_tree_grid()
	{
		$result = $this->get_tree();

		$sort_by = $this->input->post("sidx", TRUE );
		$sort_direction = $this->input->post("sord", TRUE );
		$num_rows = $this->input->post("rows", TRUE );

		$data->page = $this->input->post("page", TRUE );
		$data->records = count($result);
		$data->total = ceil($data->records/$this->input->post("rows", TRUE ));

		

		$data->rows = $result;
		//echo  json_encode($data);
		return $data;
	}
		function emp_access($empId, $des , $roleId)
	{
		 $roleAccess =  $this->get_role_access($roleId);
		 $empAccess  =  $this->get_emp_access($empId);
			
		 if($roleAccess !='' &&  $empAccess !=''){
			$roleAccessData = explode(",",$roleAccess);
			$empAccessData  = explode(",",$empAccess);
			$combAccessData = array_unique(array_merge($roleAccessData,$empAccessData));
			return implode(",",$combAccessData);
		 } else if($roleAccess !=''){
			return $roleAccess;
		 } else if($empAccess !=''){
			return 	$empAccess;
		 } else {
			return $this->get_default_access($des);
		 }
	}
	/* 21-july-2017changedfunction emp_access($empId, $des , $roleId)
	{
		
		$empAccess = $this->get_emp_access($empId);
		if($empAccess!='')
		{
			return 	$empAccess;
		}
		else
		{
			// check for role access
			$roleAccess = $this->get_role_access($roleId);
			if($roleAccess)
			{
				return $roleAccess;
			}	
			else{

				return $this->get_default_access($des);
			}	
		}
	} 21-july-17 changed*/
	
	function get_emp_access($id)
	{
		$res = $this->db->query("select moduleId as n from tbl_emp_access where empId='".$id."'")->result_array();
		$arr = array();
		foreach($res as $res)
		{
			$arr[]=$res['n'];
		}
		return implode(',',$arr);
	}
	
	function get_default_access($id)
	{	
		
	$res = $this->db->query("select moduleId as n from tbl_designation_access where designationId='".$id."'")->result_array();
	$arr = array();	
	foreach($res as $res)
	{
		$arr[]=$res['n'];
	}
	return implode(',',$arr);
}
function get_role_access($id)
	{	
	//echo "<pre>"; echo "select moduleId as n from tbl_role_access where roleId='".$id."'";die;	
	$res = $this->db->query("select moduleId as n from tbl_role_access where roleId='".$id."'")->result_array();
	
	$arr = array();
	
	foreach($res as $res)
	{
		$arr[]=$res['n'];
	}
	return implode(',',$arr);
}

function access_url($url)
{
			//$exp = explode(',',$url);
			//for($i=0;)
			//echo $url;die;
	if($url=='')
	{
		$sql = $this->db->query("select url from tbl_module where id ='0'")->result_array();
	}
	else{
		$sql = $this->db->query("select url from tbl_module where id IN(".$url.")")->result_array();
	}
	$arr = array();
	$r=1;
	foreach($sql as $sql)
	{
		if($sql['url']!='')
		{
			$arr[]=$sql['url'];
		}

		$r++;
	}
	$arr[]='dashboard';
			//$this->session->set_userdata('ccc',$arr);
	return $arr;
}


function get_modules_by_level($pid=0)
{  
	return $this->db->query("select * from tbl_module where parentId='".$pid."' AND status=1 order by setOrder")->result_array();
}
function getModuelId($url){
	$sql ="select id from tbl_module where url='".$url."'";
	$result = $this->db->query($sql)->result_array();
	return $result['0'];
}

//guest
function get_guest_access($id)
{
	$res = $this->db->query("select moduleId as n from tbl_guest_access where empId='".$id."'")->result_array();
	$arr = array();
	foreach($res as $res)
	{
		$arr[]=$res['n'];
	}
	return implode(',',$arr);
}



function dash($id,$des)
{ 
	if($this->session->userdata('candidate'))
	{
		return 'candidate/uploadDocument';
	}
	else
	{
		
		if($des!=3 and $des!=12  and  $this->session->userdata('admin_id')!=10000)
		{ 
			if(STRLEN($id)!=3)
			{ 
				
				$empAccesss = $this->get_emp_dash($id);
				
				if($empAccesss=='')
				{				
					return $this->get_default_dash($des);
				}
				else
				{
					return 	$empAccesss;
				}
			}
			else
			{ 
				$res=$this->db->query("select url from tbl_guest_access ga left join tbl_module mo on ga.moduleid=mo.id where url!='' and parentid!=0 and empId='".$id."' and url!='dashboard'")->result_array();
				$arr = array();
				foreach($res as $res)
				{
					$arr[]=$res['url'];
				}
				return $arr[0];
			}
		}
		else
		{
			return 'home';
		}
	}
	

}

function get_emp_dash($id)
{
		//echo ("select url from tbl_designation_access ga left join tbl_module mo on ga.moduleid=mo.id where empId='".$id."'");die;
	$res = $this->db->query("select url from tbl_emp_access ga left join tbl_module mo on ga.moduleid=mo.id where empId='".$id."'")->result_array();
	$arr = array();
	foreach($res as $res)
	{
		if($res['url']=='home')
			$flag=1;
		else
			$arr[]=$res['url'];
	}
	if($flag==1)
		return 'home';
	else
		return $arr[0];
}


function get_default_dash($id)
{	
	$flag='0';
	$res = $this->db->query("select url from tbl_designation_access ga left join tbl_module mo on ga.moduleid=mo.id where designationId='".$id."' and parentid!=0")->result_array();
	$arr = array();
	foreach($res as $res)
	{
		if($res['url']=='home')
			$flag=1;
		else	
		$arr[]=$res['url'];
	}
	
	if($flag==1)
		return 'home';
	else
		return "my_dashboard/announcement_list";
}
function get_tree_g($parent_id = '0', $spacing = '', $exclude = '', $cat_tree_arr = '')
{
	if (!is_array($cat_tree_arr))
		$cat_tree_arr = array();

	$cat_query = "select id, name, url, icon, setOrder from tbl_module where url !='dashboard' and parentId = '" . $parent_id . "' order by setOrder";
	$resArr = $this->db->query($cat_query)->result_array();

	foreach ($resArr as $key=>$value) {
		if ($exclude != $value['id'])
			$cat_tree_arr[] = array('id' => $value['id'], 'url'=>$value['url'], 'p_id' => $parent_id, 'icon'=>$value['icon'], 'setOrder'=>$value['setOrder'], 'name' => $spacing . $value['name']);

		$cat_tree_arr = $this->get_tree_g($value['id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $cat_tree_arr);
	}

	return $cat_tree_arr;

}

function module_head($url)
{
	$sql = "select name, icon from tbl_module where url='".$url."' ";
	$result = $this->db->query($sql)->row();
		//pre($result);die;
	echo '<img src="uploads/modules_icon/'.$result->icon.'"><span>'.$result->name.'</span>';
}

    // fetch all module list

	function check_module_permission($url)
	{	
		$resultData = array();
		$res = array();
		$res = $this->db->query("select id,url as module_url from tbl_module where url='".$url."'")->row_array();
		
		if(!empty($res) && count($res) > 0){
			$resultData = $res;
		} else {
			$resultData = $res;
		}
       return  $resultData;
	}
	
	function role_access_tsi($id)
	{	
//echo "<pre>"; echo "select moduleId as n from tbl_role_access where roleId='".$id."'";die;	
	$res = $this->db->query("select moduleId as n from tbl_role_access where roleId='".$id."'")->result_array();
	
	$arr = array();
	
	foreach($res as $res)
	{
		$arr[]=$res['n'];
	}
	return implode(',',$arr);
}

   function state_wise_access($empId){
		$this->db->select("GROUP_CONCAT(stateId SEPARATOR ',') as stateIds,pt.viewPermission",FALSE); 
		$this->db->from('employee_state_wise_permission as eswp');
		$this->db->join('permission_type as pt','eswp.empId = pt.empId and pt.type = 1','left');
		$this->db->where('eswp.empId', $empId);
		$this->db->where('eswp.type', 1);
		$query  =  $this->db->get(); 
		$result =  $query->row_array();
		return $result;
   }


}
