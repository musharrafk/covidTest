<!--suraj-->

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); //print_r($departId." ".$id);die; ?>

<style>
body {
        color: #404E67;
        background: #F5F7FA;
		font-family: 'Open Sans', sans-serif;
	}
	.table-wrapper {
		width: 700px;
		margin: 30px auto;
        background: #fff;
        padding: 20px;	
        box-shadow: 0 1px 1px rgba(0,0,0,.05);
    }
    .table-title {
        padding-bottom: 10px;
        margin: 0 0 10px;
    }
    .table-title h2 {
        margin: 6px 0 0;
        font-size: 22px;
    }
    .table-title .add-new {
        float: right;
		height: 30px;
		font-weight: bold;
		font-size: 12px;
		text-shadow: none;
		min-width: 100px;
		border-radius: 50px;
		line-height: 13px;
    }
	.table-title .add-new i {
		margin-right: 4px;
	}
    table.table {
        table-layout: fixed;
    }
    table.table tr th, table.table tr td {
        border-color: #e9e9e9;
    }
    table.table th i {
        font-size: 13px;
        margin: 0 5px;
        cursor: pointer;
    }
    table.table th:last-child {
        width: 100px;
    }
    table.table td a {
		cursor: pointer;
        display: inline-block;
        margin: 0 5px;
		min-width: 24px;
    }    
	table.table td a.add {
        color: #27C46B;
    }
    table.table td a.edit {
        color: #FFC107;
    }
    table.table td a.delete {
        color: #E34724;
    }
    table.table td i {
        font-size: 19px;
    }
	table.table td a.add i {
        font-size: 24px;
    	margin-right: -1px;
        position: relative;
        top: 3px;
    }    
    table.table .form-control {
        height: 32px;
        line-height: 32px;
        box-shadow: none;
        border-radius: 2px;
    }
	table.table .form-control.error {
		border-color: #f50000;
	}
	table.table td .add {
		display: none;
	}
	</style>

<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="brandPartnerform" id="brandPartnerform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="brand_id" id="brand_id" value="<?php echo $brand_id;  ?>">	
        <div class="row">
          
          <div class="col-md-12 helpdesk-master add_brand_partner">
		  <div class="row">
                  
                    <div class="col-sm-4">
                        <button type="button" class="btn btn-info add-new"><i class="fa fa-plus"></i> Add New</button>
                    </div>
                </div>
         
            <table class="table table-bordered partnertable">
               <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>                       
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
				<?php 
				   $count = 0;
				   if(!empty($result)){                    
                   foreach($result as $partnerData){
                 ?>
				   
                    <tr id="row<?php echo $count; ?>">
                        <td><?php echo $partnerData['partner_name']; ?></td>
                        <td><?php echo $partnerData['partner_email']; ?></td>                      
                        <td>  <input type="hidden" id="bid<?php echo $count; ?>" value="<?php echo $partnerData['id']; ?>">
							<button type="button" class="add addData<?php echo $count; ?>" data-bid="<?php echo $count; ?>"  title="Add" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
                            <button type="button" class="edit editData<?php echo $count; ?>" data-bid="<?php echo $count; ?>" title="Edit" ><i class="fa fa-pencil"></i></button>
                            <button type="button" class="delete" data-bid="<?php echo $count; ?>" title="Delete" ><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        </td>
                    </tr>
					<?php $count++; }  } ?>                      
                </tbody>
            </table>
          </div>
		  
		
        </div>
        
      </form>
    </div>
    <!--end .box --> 
  </div>
</div>

<script type="text/javascript">

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
<script type="text/javascript">
$(document).ready(function(){
    var count = <?php echo $count; ?>;
    $(".add-new").click(function(){ 
		$(this).attr("disabled", "disabled");
		var index = count;		
        var row = '<tr id="row'+count+'">'+
            '<td><input type="text" class="form-control" name="partner_name" id="partner_name'+count+'"></td>' +
            '<td><input type="text" class="form-control" name="partner_email" id="partner_email'+count+'"></td>' +
			'<td> <input type="hidden" id="bid'+count+'" value="">'+
			'<button type="button" class="add addData'+count+'" data-bid="'+count+'" title="Add" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>'+
            '<button type="button" class="edit editData'+count+'" data-bid="'+count+'" title="Edit"  ><i class="fa fa-pencil"></i></button>'+
            '<button type="button" class="delete" data-bid="'+count+'" title="Delete" ><i class="fa fa-trash-o" aria-hidden="true"></i></button>'+
           '</td>'
        '</tr>';
    	$(".partnertable").prepend(row);
		
		$(".addData"+count).show();
		$(".editData"+count).hide();
	
		count++; 
    });
	// Add row on add button click
    $(document).off('click', '.add');
	$(document).on("click", ".add", function(){
     
		var empty = false;
        
		// fetch value
		var bidVal        =   $(this).data('bid');
		var brandPartner  =   $('#bid'+bidVal).val();
		var brandId       =   $('#brand_id').val();	

		var input = $(this).parents("tr").find('input[type="text"]');
		
        var partnerName   =  $('#partner_name'+bidVal).val();
        var partnerEmail  =  $('#partner_email'+bidVal).val();
        input.each(function(){
			if(!$(this).val()){
				$(this).addClass("error");
				empty = true;
			} else{
                $(this).removeClass("error");
            }
		});
		$(this).parents("tr").find(".error").first().focus();
		if(!empty){
			input.each(function(){
				$(this).parent("td").html($(this).val());
			});
        // ajax request

            $('.addData'+bidVal).attr("disabled", "disabled");
            $.ajax({
            type: 'POST',
            url: '<?php echo site_url();?>helpdesk/save_update_partner_details',
            dataType: "json",
            data: {bpid:brandPartner,brandId:brandId,partnerName:partnerName,partnerEmail:partnerEmail},
            success:function (data){           
                    if(data.respCode == 200) {                
                        if(data.status == 1){                      
                            $('#bid'+bidVal).val(data.id); 
                        }
                            jQuery.noticeAdd({text:data.msg});	
                    } 
            }
            });	 
      
			$(this).parents("tr").find(".add, .edit").toggle();
			$(".add-new").removeAttr("disabled");
            $('.addData'+bidVal).removeAttr("disabled");
		}	
        
    });
	// Edit row on edit button click
    $(document).off('click', '.edit');
	$(document).on("click", ".edit", function(){
        var bidVal   =   $(this).data('bid');	
		$(this).closest('').find('.display_image').attr('id');	
        $(this).parents("tr").find("td:not(:last-child)").each(function(index){
			if(index == 0){
			   $(this).html('<input type="text" class="form-control"  name="partner_name" id="partner_name'+bidVal+'" value="' + $(this).text() + '">');
			} else if(index == 1){
			   $(this).html('<input type="text" class="form-control"  name="partner_email" id="partner_email'+bidVal+'" value="' + $(this).text() + '">');
			}			
		});	
        
		$(this).parents("tr").find(".add, .edit").toggle();
		$(".add-new").attr("disabled", "disabled");
    });
	// Delete row on delete button click
   
    $(document).off('click', '.delete');
	$(document).on("click", ".delete", function(){
		
		var bidVal        =   $(this).data('bid');
		var brandPartner  =   $('#bid'+bidVal).val();
        $(this).attr("disabled", "disabled");
		// ajax request
		$.ajax({
		   type: 'POST',
		   url: '<?php echo site_url();?>helpdesk/delete_brand_partner_details',
		   dataType: "json",
		   data: {bid:brandPartner},
		   success:function (data){ 
			     if(data.respCode == 200) {
                        jQuery.noticeAdd({text:data.msg});	
						$('#row'+bidVal).hide();
		                $(".add-new").removeAttr("disabled");
                        $(this).attr("disabled", "disabled");
                 } 
		   }
	     });  
    });
});

$(document).ready(function() {
	var wfheight = $(window).height();        
            $('.add_brand_partner').height(wfheight-325);

$('.add_brand_partner').perfectScrollbar()
});
</script>
	
