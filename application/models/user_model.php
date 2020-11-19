<?php

class user_model extends parent_model {

function login($data)
	{
		//print_r($data);die;
		$this->db->select(['id','first_name','last_name','role_id','token_id as token','email_id']);
    	$this->db->where('email_id',$data->email);
	    $this->db->where('password',md5(trim($data->password)));
		$this->db->where('is_active', 1);
		$this->db->from(TABLE_USER);
		$query = $this->db->get();
		$result=$query->row_array();
		
		
		
		return $result;
	}
	
function getItemList($data)
	{//echo "test";
		//print_r($data);
		if($data['itemId']!="")
		{
			$addSql = 'id='.$data['itemId'];
			
	    
		
		}
	
		
$this->db->select('*');
    	//$this->db->where('token_id',$token);
		$this->db->from(TABLE_TEST);
		$query = $this->db->get();
		$result=$query->result_array();
		//print_r($result);

		return $result;

	}
	function AddtoCart($data)
	{
		
		//print_r($data);
		
		 $res = $this->query_insert('tbl_order_details',$data);
        //$id  =  $this->db->insert_id();
        //if ($res) {
          //  return $id;
        //}else{
         //   return false;
       // }
		//$this->query_insert(TABLE_CART, $data);
        // Return the id of inserted row
       // return $idOfInsertedData = $this->db->insert_id();
	//$this->db->insert(TABLE_CART,$data);

}

function getorders($data){
	
	//print_r($data);die;
	 $where=" and ord.user_id='".$data->user_id."'  and ord.order_status='".$data->order_status."' ";
	$sql="select tst.id,ord.itemId,ord.`first_name`,ord.last_name,ord.created, tst.sr_no, tst.itemId,tst.itemName,tst.labName,tst.minPrice from  tbl_order_details as ord left join tbl_test_details as tst on   ord.itemId=tst.`id` left join tbl_mst_users  usr on ord.user_id =usr.`id` where 1=1 ".$where."";
	//echo $sql;die;
	$result = $this->db->query($sql)->result_array();	
		    	
			return  $result;
		}
function  deleteItem($data)
{
	
	print_r($data);
	$this->db->where("itemId",$data);
    $this->db->delete("tbl_order_details");
     //$sql ="delete from tbl_order_details where id=".$data."";
	 //$result = $this->db->query($sql);
	 echo "test";die;
	 
}	

function palceOrder($data){
	
	//print_r($data);
	
	$updatArr = array(
        'first_name' => $data->patient_name,
        'contact' => $data->patient_contact,
        'age' => $data->patient_age,
		'order_status'=>1
);//print_r($updatArr);die;
	foreach(json_decode($data->order) as $data1)
	
	{
		//echo $where="itemId='".$data1->id."' ";
        //$this->query_update("tbl_order_details1", $updatArr, $where);
       $this->db->where('itemId', $data1->id);
       $this->db->update('tbl_order_details', $updatArr);
		
		
	}
	echo 1;die;
	
}
	
}