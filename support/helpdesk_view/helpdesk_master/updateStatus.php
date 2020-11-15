<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
        <div class="row" >
          
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name">Request Name</label>
              <input type="text" placeholder="Support Team Type" class="form-control validate[required]" readonly id="request_name" name="request_name" value="<?php echo field_value($result, 'request_name');?>"/>
              <div id="responcemobile"></div>
            </div>
          </div>
		   <div class="form-group">
               <label class="control-label" for="name">Status</label>
              
              <?php $reg =field_value($result, 'status');?>
              <?php  //pre($reg);die;?>
              <select class="form-control" name="Status" id="Status">
                
                
                <option value="1" <?php echo($reg==0)?'selected':'';?>>
				<?php  echo "Active"; //echo $result22['training_name'] ?> 
				</option>
				<option value="0" <?php echo($reg==1)?'selected':'';?>>
				<?php  echo "Deactive"; //echo $result22['training_name'] ?> 
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
	get_list();
	});
	
	function get_list()

		{ 
		$.ajax({
				   type: 'POST',
				   url: '<?php echo site_url();?>helpdesk/get_add_edit_request',
				   data: {},
				   success:function (data){ //alert(data);
					  $("#requestPanel").html(data);
					  // alert(data); 
				   }
					});	
			   
		}
	$(document).ready(function(){

		 jQuery("#trainingform").validationEngine('attach',{

	  onValidationComplete: function(form, status){
		if(status)
		{
		
			var formdata = $("#trainingform").serialize();
				$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/insert_update_request',
		   data: formdata,
		   success:function (data){
			    if(data==1)
				{
					
					if($("#id").val()>0)
					{
						jQuery.noticeAdd({text:"Status updated successfully"});
						$("#list").trigger("reloadGrid");
						editDialog.dialog("close");
						
						
					}
					else
					{
						jQuery.noticeAdd({text:"Status updated successfully"});
						//$("#list").trigger("reloadGrid");
						editDialog.dialog("close");
						get_list();
					}
					
				}
			   
			   
				
				
		   }
			});
			
		
		}
	  }
	});
		
		

	});
	

	
	
</script>