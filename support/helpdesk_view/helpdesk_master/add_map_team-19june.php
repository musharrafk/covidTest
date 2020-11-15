<!--suraj-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<script>
 $(function() {
    $( "#tag" ).autocomplete({
        source: 'autocomplete.php'
    });
 });
</script>

<div id="myModal" class="reveal-modal" >
  <div class="row">
    <div class="col-md-12">
      <form action="" method="post" name="trainingform" id="trainingform" class="form-validate form_class" enctype="multipart/form-data" >
        <input type="hidden" name="id" id="id" >
        <div class="row">
          
          <div class="col-md-12">
            <div class="form-group">
              <label class="control-label" for="name">Team Name</label>
              <input type="text" placeholder="Team Name" class="form-control validate[required]" id="team_name" name="team_name" />
              <div id="responcemobile"></div>
            </div>
          </div>
		  <div class="col-md-12">
            <div class="form-group">
			<label class="control-label" for="name">Employee List</label>
                <select id="framework" name="framework[]" multiple class="form-control" >
				  <option value="Codeigniter">Codeigniter</option>
				  <option value="CakePHP">CakePHP</option>
				  <option value="Laravel">Laravel</option>
				  <option value="YII">YII</option>
				  <option value="Zend">Zend</option>
				  <option value="Symfony">Symfony</option>
				  <option value="Phalcon">Phalcon</option>
				  <option value="Slim">Slim</option>
				</select>
                


                                
                           
                
            </div>
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
					check_request_name();
				}
			}
		});				


	});
	function check_request_name()
	{
		
		
		$.post("<?php echo site_url('helpdesk/check_request_name');?>",{team_name:$('#team_name').val(),id:$('#id').val()},
		
			function (data)
			{
				
				if(data.length>0)
				{
					
					if(jQuery.trim(data)!='0')
					{
						jQuery('#req_type').validationEngine('showPrompt', '* This Request Type already exists', 'fail');
					}
					else
					{
						//alert("tt");
						var formdata = $("#trainingform").serialize();
						$.ajax({
							type: 'POST',
							url: '<?php echo site_url();?>helpdesk/insert_update_request',
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

<script type="text/javascript" >
      jQuery(document).ready(function() {
        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());
        });
        // For select 2
        $(".select2").select2();
        $('.selectpicker').selectpicker();
        //Bootstrap-TouchSpin
        $(".vertical-spin").TouchSpin({
            verticalbuttons: true,
            verticalupclass: 'ti-plus',
            verticaldownclass: 'ti-minus'
        });
        var vspinTrue = $(".vertical-spin").TouchSpin({
            verticalbuttons: true
        });
        if (vspinTrue) {
            $('.vertical-spin').prev('.bootstrap-touchspin-prefix').remove();
        }
        $("input[name='tch1']").TouchSpin({
            min: 0,
            max: 100,
            step: 0.1,
            decimals: 2,
            boostat: 5,
            maxboostedstep: 10,
            postfix: '%'
        });
        $("input[name='tch2']").TouchSpin({
            min: -1000000000,
            max: 1000000000,
            stepinterval: 50,
            maxboostedstep: 10000000,
            prefix: '$'
        });
        $("input[name='tch3']").TouchSpin();
        $("input[name='tch3_22']").TouchSpin({
            initval: 40
        });
        $("input[name='tch5']").TouchSpin({
            prefix: "pre",
            postfix: "post"
        });
        // For multiselect
        $('#pre-selected-options').multiSelect();
        $('#optgroup').multiSelect({
            selectableOptgroup: true
        });
        $('#public-methods').multiSelect();
        $('#select-all').click(function() {
            $('#public-methods').multiSelect('select_all');
            return false;
        });
        $('#deselect-all').click(function() {
            $('#public-methods').multiSelect('deselect_all');
            return false;
        });
        $('#refresh').on('click', function() {
            $('#public-methods').multiSelect('refresh');
            return false;
        });
        $('#add-option').on('click', function() {
            $('#public-methods').multiSelect('addOption', {
                value: 42,
                text: 'test 42',
                index: 0
            });
            return false;
        });
        $(".ajax").select2({
            ajax: {
                url: "https://api.github.com/search/repositories",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });
    });
</script>
