<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" class="ids" value="<?php echo field_value($result, 'id')?>">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name">Team Name</label>
             <?php $result22=$this->db->query("select id,team_name from helpdesk_support_team where status=1 order by team_name asc")->result_array();?>
              <?php $reg =field_value($result, 'teamId');?>
              <?php // pre($result[0]['teamId']);die;?>
              <select class="form-control teamIds validate[required]"  name="teamId" id="teamId">
                <option value="">Select Team</option>
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$reg)?'selected':'';?>><?php echo $result22['team_name']; ?> </option>
                <?php }?>
            </select>
              
            </div>
          </div>
		  
		   <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name">Priority </label>
			  <?php $result22=$this->db->query("select id, priority_type from helpdesk_priority order by priority_type asc")->result_array();?>
              <?php $reg =field_value($result, 'priority');?>
              <?php  //pre($result22);die;?>
              <select class="form-control prioritys validate[required]" name="priority" id="priority">
                <option value="">Select Priority</option>
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$reg)?'selected':'';?>><?php echo $result22['priority_type']; ?> </option>
                <?php }?>
            </select>
            
            </div>
          </div>
		
		  
		   <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name">Request Name</label>
              <input type="text" placeholder="Request Name" class="form-control request_names validate[required]" id="request_name" name="request_name" value="<?php echo field_value($result, 'request_name');?>"/>
              <div id="responcemobile"></div>
            </div>
          </div> 
		  
		    
		  <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name">TAT</label>
              <input type="number" placeholder="HH" class="form-control validate[required]" id="tat" name="tat" min="1" max="72" value="<?php echo field_value($result, 'tat');?>"/>
              <div id="responcemobile"></div>
            </div>
          </div>
		  
         
          </div>
        </div>
        <div class="modal-footer">
		<!--<input type="button" value="Submit" id="buttonClass" class="btn btn-primary"> -->
          <button type="button" class="btn btn-primary" onclick="check_training_name()" >Submit</button>
        </div>
      </form>
    </div>
    <!--end .box --> 
  </div>
</div>


<script type="text/javascript">

	
	function check_training_name()
	{
		
		//alert($('.ids').val());
		$.post("<?php echo site_url('helpdesk/check_request_name');?>",{request_name:$('.request_names').val(),teamId:$('.teamIds').val(),id:$('.ids').val()},
		//alert($('#id').val());return;
			function (data)
			{
				//alert(data);
				if(data.length>0)
				{
					
					if(jQuery.trim(data)!='0')
					{
						jQuery('#request_name').validationEngine('showPrompt', '* This Request name already exists', 'fail');
					}
					else
					{
						var formdata = $("#trainingform").serialize();
						$.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/update_request',
							data: {request_name:$('.request_names').val(),teamId:$('.teamIds').val(),id:$('.ids').val()},
							success:function (data){
								//alert(data);return;
								if(data==1)
								{
									
									if($(".ids").val()>0)
									{
										jQuery.noticeAdd({text:"updated successfully"});
										$("#list").trigger("reloadGrid");
										editDialog.dialog("close");
										get_list();
										//location.reload();
									}
									else
									{
										jQuery.noticeAdd({text:"added successfully"});
										$("#list").trigger("reloadGrid");
										editDialog.dialog("close");
										get_list();
									}
								}
								else
								{
									if($(".ids").val()>0)
									{
										jQuery.noticeAdd({text:"Training Name Update Failed"});
									}
									else
									{
										jQuery.noticeAdd({text:"Training Name Added Failed"});
									}
								}

							}
						});

					}
				}

			}
		);
		
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
		   url: '<?php echo site_url();?>training/insert_update_training_name',
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





