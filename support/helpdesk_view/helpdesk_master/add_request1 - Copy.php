<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); //print_r($result);  ?>

<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" value="<?php  print_r($result[0][id]); ?>">
        <div class="row">
          
          <div class="col-md-4">
            <div class="form-group">
              <label class="control-label" for="name">Team Name</label>
             <?php $result22=$this->db->query("select id,team_name from helpdesk_support_team order by team_name asc")->result_array();?>
              <?php $reg =field_value($result, 'teamId');?>
              <?php $tI = $result[0]['teamId'];?>
              <select class="form-control validate[required]" name="teamId" id="teamId">
                <option value="">Select Team</option>
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$tI)?'selected':'';?>><?php echo $result22['team_name']; ?> </option>
                <?php }?>
            </select>
              
            </div>
          </div>
		  
		   <div class="col-md-4">
            <div class="form-group">
              <label class="control-label" for="name">Priority </label>
			  <?php $result22=$this->db->query("select id, priority_type from helpdesk_priority order by priority_type asc")->result_array();?>
              <?php $reg =field_value($result, 'id');?>
             <?php $priority = $result[0]['priority'];?>
              <select class="form-control validate[required]" name="priority" id="priority">
                <option value="">Select Priority</option>
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$priority)?'selected':'';?>><?php echo $result22['priority_type']; ?> </option>
                <?php }?>
            </select>
            
            </div>
          </div>
		  
		   <div class="col-md-4">
            <div class="form-group">
              <label class="control-label" for="name">Request Name</label>
              <input type="text" placeholder="Request Name" class="form-control validate[required]" id="request_name" name="request_name" value="<?php print_r($result[0][request_name]); ?>"/>
              <div id="responcemobile"></div>
            </div>
          </div>
		  
		 
		  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="check_training_name1();">Submit</button> 
		  
		 
        </div>
      </form>
    </div>
    <!--end .box --> 
  </div>
</div>

<script type="text/javascript">

	$(document).ready(function(){

		jQuery("#trainingform").validationEngine('attach',{

			onValidationComplete: function(form, status){
				if(status)
				{
					check_training_name();
				}
			}
		});				


	});
	function check_training_name1()
	{
		//alert("llllll");
		alert($('#request_name').val());return;
		
		
		
	}
</script>

<script type="text/javascript">


$(document).ready(function () {
	/* Get the checkboxes values based on the class attached to each check box */
	$("#buttonClass").click(function() {
	    getValueUsingClass();
		
	});
	
	/* Get the checkboxes values based on the parent div id */
	$("#buttonParent").click(function() {
	    getValueUsingParentTag();
	});
});

	
	function getValueUsingClass()
	{
		
		
		
		
		$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/update_request',
		   data: {training_name:$('#training_name').val(),id:$('#id').val()},
		   success:function (data){
			  // alert(data);
			   if(data==1)
				{
					//alert(data);
					if($("#id").val()>0)
					{
						jQuery.noticeAdd({text:"Updated successfully"});
						$("#list").trigger("reloadGrid");
						editDialog.dialog("close");
						location.reload();
					}
					else
					{
						jQuery.noticeAdd({text:"Updated successfully"});
						$("#list").trigger("reloadGrid");
						editDialog.dialog("close");
					}
				}
				else
				{
						jQuery.noticeAdd({text:"Cancel successfully"});
						$("#list").trigger("reloadGrid");
						editDialog.dialog("close");
					}
			   
		   }
			});
		
		
		
		
		
		
	}
</script>
