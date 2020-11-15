<!--suraj-->
<script>
function getAlluser()
	{
		
		var id = document.getElementById('teamId').value;
		
		$.ajax({
			type: "POST",
			url: '<?php echo site_url('helpdesk/get_emplist').'/';?>'+id,
			data: id='cat_id',
			success: function(data){
			   // alert(data);
				$('#reporting').html(data);
		},
				});
	}

</script>

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); //print_r($departId." ".$id);die; ?>


<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" >
		<input type="hidden" name="ids" id="ids" value="<?php echo $id; ?>" >
		<input type="hidden" name="departId" id="departId" value="<?php echo $departId; ?>" >
        <div class="row add_map_team add_map-scrl">
          
          <div class="col-md-12">
            <div class="form-group">
               <label class="control-label" for="name">Team Name</label>
             <?php $result22=$this->db->query("select id,team_name from brand_support_team where id='".$id."' and status=1 order by team_name asc")->result_array();?>
              <?php $reg =field_value($result, 'teamId');?>
              <?php  // pre($result[0]['teamId']);die;?>
              <select class="form-control validate[required]" name="teamId" id="teamId" onChange='getAlluser(this.values)'>
                
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$id)?'selected':'';?>><?php echo $result22['team_name']; ?> </option>
                <?php }?>
            </select>
              
              <div id="responcemobile"></div>
            </div>
          </div>
		  <div class="clearfix"></div>
		  <div class="col-md-12">
            <div class="form-group employee-list-1" id="reporting" >
			<label class="control-label" for="name">Employee List</label>
			<label class="control-label" for="name" style="float:right">Status</label>
            
            
            <ul>
		<?php	 
			foreach ($result as $userData) { 
			$empId = $userData['empId'];
			$fullname = $userData['empFname']." ".$userData['empMname'].$userData['empLname'];
			
			$mapTeam="select id,status from brand_map_team where teamId='".$id."' and empId = '".$empId."'";
			//echo "select id,status from helpdesk_map_team where teamId='".$id."' and empId = '".$empId."'";
			$mapT=$this->db->query($mapTeam)->result_array();
			$mapTeam = $mapT[0][id];
			$status = $mapT[0][status];
			$img='';
			
			
			if($mapTeam > 0)
			{
				//echo $status ;
			if($status == 1) { $img ='active-ico.png';  }  if($status == 0){ $img='inactive-ico.png'; }	
		?>
            <li>
			<span style="float:left"><input type="checkbox" name="chk[]" id="chk" class="checkbox" checked  value="<?php echo $empId; ?>" Disabled >&nbsp;&nbsp; 
			
		<?php	echo $fullname; ?> </span>
			<span style="float:right">
			<a href="javascript:void(0);" onClick="update_status_team('<?php echo $mapTeam ?>','<?php echo $status ?>')">
			<img src="images/<?php echo $img; ?>" height="15" title="Update Status" style="margin-right:10px;"></a>
			</span>
            </li>
            
			
			
		<?php } else { ?>
        
			
            <li>
			<input type="checkbox" name="chk[]" id="chk" class="checkbox" value="<?php echo $empId; ?>" >&nbsp;&nbsp; 
		<?php echo $fullname; } } ?>	
        </li>
            </div>
            
            </ul>
          </div>
		  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="check_request_name();" >Submit</button>
        </div>
      </form>
    </div>
    <!--end .box --> 
  </div>
</div>



<script type="text/javascript">
$(document).ready(function(){
	//get_teammap();
	});
	
	function check_request_name()
	{
			var formdata = $("#trainingform").serialize();
			$.ajax({
				type: 'POST',
				url: '<?php echo site_url();?>helpdesk/insert_update_map',
				data: formdata,
				success:function (data){
					//alert(data);return;
					
						if($("#id").val()>0)
						{
							jQuery.noticeAdd({text:"updated successfully"});
							$("#list").trigger("reloadGrid");
							editDialog.dialog("close");
						}
						else
						{
							jQuery.noticeAdd({text:"added successfully"});
							$("#list").trigger("reloadGrid");
							editDialog.dialog("close");
						}
					}
		
			});
}



	
function update_status_team(id,status)
{ 

//alert(status);return;
if(status ==1){
var response = confirm("Are you sure you want deactive user");
} else {
	var response = confirm("Are you sure you want active user");
}	
if ( response == true )
{
var ids = $('#ids').val();
var departId = $('#departId').val();
$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/update_teammap_status',
		   data: {id:id,status:status},
		   success:function (data){ //alert(data);
			editDialog.dialog("close");
			  
			// add_map_team();
			// $("#requestPanel").html(data);
			  // alert(data); 
			 //get_teammap()
			 add_map_team(ids,departId);
		   }
			});	
       
}
}


function add_map_team(id,departId)
{
	//var id = $('#ids').val();
	//var departId = $('#departId').val();

	//$('#request_name').val()
    $("#page_loader1").show();
    var dialogOpts = {
        title: (id!=''?'Team':'')+" Map with support group",
        modal: true,
        width: "700px",
        resize: "auto",
        close: dialogClosed,
		draggable: false,
		resizable: false
    };
    $("#page_loader1").hide();
        editDialog = $('<div></div>')
        .load("<?php echo site_url("helpdesk/add_edit_team1"); ?>", {edit:id,departId:departId})
        .dialog(dialogOpts);
    editDialog.dialog('open');
    return false;
}	
			
	
</script>

<script>
$(document).ready(function() {
	var wfheight = $(window).height();        
 $('#myModal').height(wfheight-325);
$('#myModal').perfectScrollbar()
});
</script>
