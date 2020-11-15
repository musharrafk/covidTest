<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>


<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="<?php echo site_url();?>helpdesk/insert_update_ticket"  method="post" name="mrfcandidateform" id="mrfcandidateform"  class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" value="<?php echo field_value($result, 'id')?>">
        <div class="row">
          
		  <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name">Subject</label>
             <input type="text" placeholder="Subject" class="form-control validate[required]" id="subject" name="subject" value="<?php echo field_value($result, 'subject');?>"/>
            </div>
          </div>
		  
		  <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name">Request  </label>
			   <?php $result22=$this->db->query("select id, req_type from helpdesk_msr_request where status=1")->result_array();?>
              <?php $reg =field_value($result, 'type');?>
                <select class="form-control validate[required]" name="type" id="type">
                <option value="">Select Request </option>
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$reg)?'selected':'';?>><?php echo $result22['req_type']?> </option>
                <?php }?>
              </select>
              <div id="responcemobile"></div>
            </div>
          </div>
		  
		  <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name">Status </label>
               <?php $result22=$this->db->query("select id, s_type from helpdesk_status where status=1")->result_array();?>
              <?php $reg =field_value($result, 'id');?>
              <?php //pre($result22);die;?>
              <select class="form-control validate[required]" name="status" id="status">
                
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$reg)?'selected':'';?>><?php echo $result22['s_type']?> </option>
                <?php }?>
              </select>
            </div>
          </div>
		  
		   <div class="col-md-6">
            <div class="form-group">
              <label class="control-label" for="name">Priority </label>
               <?php $result22=$this->db->query("select id, priority_type from helpdesk_priority where status=1")->result_array();?>
              <?php $reg =field_value($result, 'id');?>
              <?php //pre($result22);die;?>
              <select class="form-control validate[required]" name="priority" id="priority">
                <option value="">Select Priority </option>
                <?php foreach($result22 as $result22){ ?>
                <option value="<?php echo $result22['id'];?>" <?php echo($result22['id']==$reg)?'selected':'';?>><?php echo $result22['priority_type']?> </option>
                <?php }?>
              </select>
              <div id="responcemobile"></div>
            </div>
          </div>
		   <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name">Message</label>
			  <?php $data= array(
                    'name'        => 'message',
                    'id'          => 'message',
                    'toolbarset'  => 'Default',
                    'basepath'    => base_url().'application/plugins/fckeditor/',
                    //'width'       => '560',
                    'height'      => '320'
                    );
                    echo form_fckeditor( $data, $result['message']);
                    ?>
	         <!--<textarea cols="50" rows="3"  class="form-control validate[required]" id="message" name="message" value=""/><?php echo field_value($result, 'message');?></textarea> -->
            </div>
          </div>
		  <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name">Attach</label>
					<input type="file" name="attach" id="attach" />
					<span style="float:right"> <button type="submit" class="btn btn-primary" id="addCandMrf">Submit</button></span>
          </div>
		</div>
      </div>
      </form>
    </div>
    <!--end .box --> 
  </div>
</div>

<script type="text/javascript">

	$(document).ready(function(){

	
	$('#mrfcandidateform').submit(function(evt) {
             evt.preventDefault();
              $("#addCandMrf").prop('disabled', true);
            var mrfId = $('#mrfId').val();
			var formData = new FormData(this);
			$("#page_loader1").show();
					//var formdata = $("#mrfcandidateform").serialize();
						$.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/insert_update_ticket',
							data: formData,
							cache:false,
                            contentType: false,
                            processData: false,
							success:function (data){
								if(data==1)
								{
									if($("#job_Id").val()>0)
									{
										jQuery.noticeAdd({text:"Candidate updated successfully"});
										$("#list").trigger("reloadGrid");
										editDialog.dialog("close");
									}
									else
									{
										jQuery.noticeAdd({text:"Candidate added successfully"});
										$("#list").trigger("reloadGrid");
										editDialog.dialog("close");                    
                    $('#counterCandidate'+mrfId).html(parseInt($('#counterCandidate'+mrfId).html()) + 1);
									}
								}
								else
								{
									if($("#job_Id").val()>0)
									{
										jQuery.noticeAdd({text:"Candidate Update Failed"});
									}
									else
									{
										jQuery.noticeAdd({text:"Candidate Added Failed"});
									}
								}
								$("#page_loader1").hide();
							   $("#addCandMrf").prop('disabled', false);
							}
						});
			
	});
	
	//Call city With Ajax
    $("#currentState").change(function(){
        var selectedState = $("#currentState").val();		
	  if(selectedState!=''){		
		 $.post("<?php echo site_url("mrf/ajaxCity")?>",{sid:selectedState},
				function(data){
				//alert(data);
					if (data != ""){
					$("#currentLocation").html(data);
					
					}
				});
			
		} else {
			$("#currentLocation").html('<option>Select Job Location</option>');
		 }
       });
	});
	
	$('#empDOB').datepicker({
	    changeMonth : true,
		changeYear  : true ,
		yearRange   : '1960:2019',
		maxDate: new Date()
		});
		
function isNumberValue(event) {
		var key = window.event ? event.keyCode : event.which;
		if (event.keyCode === 8 || event.keyCode === 46) {
			return true;
		} else if ( key < 48 || key > 57 ) {
			return false;
		} else {
			return true;
		}
};
 	
</script>
<script>

$(document).ready(function() {
	 var wfheight = $(window).height();
 $('#myModal').height(wfheight-170);	
$('#myModal').perfectScrollbar()
});
</script>

