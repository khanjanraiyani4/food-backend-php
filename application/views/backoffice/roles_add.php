<?php $this->load->view(ADMIN_URL.'/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/css/style.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
	<!-- BEGIN sidebar -->
	<?php $this->load->view(ADMIN_URL.'/sidebar');?>
	<!-- END sidebar -->
	<?php if($this->input->post()) {
		foreach ($this->input->post() as $key => $value) {
			$$key = @htmlspecialchars($this->input->post($key));
		}
	} else {
		$FieldsArray = array('role_id','role_name');
		foreach ($FieldsArray as $key) {
			$$key = @htmlspecialchars($edit_detail->$key);
		}
	}
	if(isset($edit_detail) && $edit_detail !="") {
		$page_label    = $this->lang->line('edit').' '.$this->module_name;
		$form_action   = base_url().ADMIN_URL.'/'.$this->controller_name."/edit/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($role_id));
	} else {
		$page_label    = $this->lang->line('add').' '.$this->module_name;
		$form_action   = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
	} ?>
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE header-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
						<?php echo $this->lang->line('manage_roles'); ?>
					</h3>
					<ul class="page-breadcrumb breadcrumb">
						<li>
							<i class="fa fa-home"></i>
							<a href="<?php echo base_url().ADMIN_URL; ?>/dashboard">
							<?php echo $this->lang->line('home') ?> </a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name.'/view'; ?>"><?php echo $this->lang->line('manage_roles') ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $page_label;?> 
                        </li>
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<!-- END PAGE header-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN VALIDATION STATES-->
					<div class="portlet box red">
						<div class="portlet-title">
							<div class="caption"><?php echo $page_label;?></div>
						</div>
						<div class="portlet-body form">
							<!-- BEGIN FORM-->
							<form action="<?php echo $form_action;?>" id="form_add_role" name="form_add_role" method="post" class="form-horizontal isautovalid" enctype="multipart/form-data" >
								<div class="form-body"> 
									<?php if(!empty($Error)) { ?>
										<div class="alert alert-danger"><?php echo $Error; ?></div>
									<?php } ?>
									<?php if(validation_errors()) { ?>
										<div class="alert alert-danger">
											<?php echo validation_errors(); ?>
										</div>
									<?php } ?>
									<div class="form-group">
										<label class="control-label col-md-3"><?php echo $this->lang->line('role_name'); ?><span class="required">*</span></label>
										<div class="col-md-4">
											<input type="hidden" name="role_id" id="role_id" value="<?php echo $role_id ?>" />
											<input type="hidden" id="call_from" name="call_from" value="CI_callback" />
											<?php $is_readonly = ($role_name == 'Master Admin' || $role_name == 'Restaurant Admin' || $role_name == 'Branch Admin') ? 'readonly' : ''; ?>
											<input type="text" name="role_name" id="role_name" oninput="checkRoleNameExist(this.value);" value="<?php echo $role_name ?>" <?php echo $is_readonly; ?> maxlength="249" class="form-control required"/>
											<div id="role_exist" class="text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-3"><?php echo $this->lang->line('role_access'); ?><span class="required">*</span></label>
										<div class="col-md-4">
											<div id="tree_2" class="tree-demo"></div>
											<div style="display:none;" class="error" id="selectRoleAccess"></div>
										</div>
									</div>                            
									<input type="hidden" name="access_ids" id="accessIds" value="">
								</div>
								<div class="form-actions fluid">
									<div class="col-md-offset-3 col-md-9">
										<button type="submit" name="submitPage" id="submitPage" value="Submit" class="btn btn-success default-btn danger-btn theme-btn"><?php echo $this->lang->line('submit'); ?></button>
										<a class="btn btn-danger default-btn danger-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name.'/view';?>"><?php echo $this->lang->line('cancel'); ?></a>
									</div>
								</div>
							</form>
							<!-- END FORM-->
						</div>
					</div>
					<!-- END VALIDATION STATES-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/jstree.min.js"></script>
<script>
var allAccessIds = [];
jQuery(document).ready(function() {
	Layout.init(); // init current layout
	$('#tree_2').jstree({
		'plugins': ["wholerow", "checkbox", "types"],
		'core': {
			"themes" : {
				"responsive": false
			},
			'data': {
				'url' : function (node) {
					return BASEURL+'<?php echo ADMIN_URL ?>/role/ajaxTreeView';
				},
				'data' : function (node) {
					return { 'role_id' : '<?php echo !empty($role_id) ? $role_id : ''; ?>' };
				}
			}
		},
		"types" : {
			"default" : {
				"icon" : "fa fa-folder icon-warning icon-lg"
			},
			"file" : {
				"icon" : "fa fa-file icon-warning icon-lg"
			}
		}
	});
	$('#tree_2').on('ready.jstree', function (e, data) {
		$('a.jstree-anchor').each(function( index ) {
			allAccessIds.push($( this ).parent('li').attr('id'));
		});
	}).jstree();
});
$("#form_add_role").submit(function( event ) {
	var selectedElms = $('#tree_2').jstree("get_selected", true);
	if(selectedElms=="") {
		$('#selectRoleAccess').show();
		$('#selectRoleAccess').html("<?php echo $this->lang->line('field_required'); ?>");
		return false;
	} else {
		var accessIds = [];
		var nCount = 0;
		$(selectedElms).each(function( index ){
			accessIds.push(selectedElms[index].id);
		});
		var strAccessIds = ($('#j1_1 > div').hasClass('jstree-wholerow-clicked')) ? allAccessIds.join(",") :accessIds.join(",");
		$('#accessIds').val(strAccessIds);
	}
});
jQuery('#form_add_role').validate({
	rules:{
		role_name: {
			required: true,
		}
	}
});
function checkRoleNameExist(value) {
    var role_id = $('#role_id').val();
    $.ajax({
    type: "POST",
    url: BASEURL+"<?php echo ADMIN_URL ?>/role/checkRoleNameExist",
    data: 'role_name=' + value +'&call_from=ajax_call&role_id='+role_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#role_exist').show();
        $('#role_exist').html("<?php echo $this->lang->line('role_exist'); ?>");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#role_exist').html("");
        $('#role_exist').hide();        
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#role_exist').show();
      $('#role_exist').html(errorThrown);
    }
  });
}
</script>