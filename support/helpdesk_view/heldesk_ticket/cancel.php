<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
		<input type="hidden" name="empRid" id="empRid" value="<?php echo field_value($result, 'empRid')?>">
        <div class="row">
          
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name">Training Name</label>
              <input type="text" placeholder="Training Name" class="form-control validate[required]" id="training_name" name="training_name" value="<?php echo field_value($result, 'training_name');?>"/>
              <div id="responcemobile"></div>
            </div>
          </div>
		   <div class="form-group">
               <label class="control-label" for="name">Apply</label>
              
              <?php $reg =field_value($result, 'status');?>
              <?php  //pre($reg);die;?>
              <select class="form-control" name="Status" id="Status" required>
                 
				<option value="2" <?php echo($reg=='2')?'selected':'';?>>
				Cancel 
				</option>
				
              
              </select>
              <div id="responcemobile"></div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
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
		
			var formdata = $("#trainingform").serialize();
				$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>training/insert_update_training_emp_reg',
		   data: formdata,
		   success:function (data){
			  alert(data);
			    if(data==1)
				{
					//alert(data);
					if($("#id").val()>0)
					{
						jQuery.noticeAdd({text:"Apply added successfully"});
						$("#list").trigger("reloadGrid");
						editDialog.dialog("close");
					}
					else
					{
						jQuery.noticeAdd({text:"Training Updated successfully"});
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
	  }
	});
		
		

	});
	
</script>