<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>


<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
        <div class="row">
          
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name">Team Name</label>
              <input type="text" placeholder="Team Name" class="form-control validate[required]" required id="team_name" name="team_name" value="<?php echo field_value($result, 'team_name');?>"/>
              <div id="responcemobile"></div>
            </div>
          </div>
		  
		  <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name">Department </label>
			  <?php $result22=$this->db->query("select id, name from tbl_mst_dept order by name asc")->result_array();?>
              <?php $reg =field_value($result, 'departId');?>
              <?php  //pre($result22);die;?>
              <select class="form-control validate[required]" name="departId" id="departId">
                <option value="">Select Department</option>
                <?php foreach($result22 as $resultdata){ ?>
                <option value="<?php echo $resultdata['id'];?>" <?php echo($resultdata['id']==$reg)?'selected':'';?>><?php echo $resultdata['name']; ?> </option>
                <?php }?>
            </select>
            
            </div>
          </div>
		  
        </div>
        <div class="modal-footer">
          
		   <input type="button" value="Submit" id="buttonClass" class="btn btn-primary"> 
        </div>
      </form>
    </div>
    <!--end .box --> 
  </div>
</div>
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
		
		
			$.post("<?php echo site_url('helpdesk/check_team_name');?>",{team_name:$('#team_name').val(),departId:$('#departId').val()},
		
			function (data)
			{
				
				if(data.length>0)
				{
					
					if(jQuery.trim(data)!='0')
					{
						jQuery('#team_name').validationEngine('showPrompt', '* This Team & Department already exists', 'fail');
					}
					else
					{
						//alert("tt");
						var formdata = $("#trainingform").serialize();
						$.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/insert_update_team',
							data: formdata,
							success:function (data){
								//alert(data);return;
								if(data==1)
								{
									
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
								else
								{
									if($("#id").val()>0)
									{
										jQuery.noticeAdd({text:"Update Failed"});
									}
									else
									{
										jQuery.noticeAdd({text:"Added Failed"});
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