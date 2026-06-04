<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/css/datepicker.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/admin/plugins/daterangepicker/css/daterangepicker.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
	<!-- BEGIN sidebar -->
	<?php $this->load->view(ADMIN_URL.'/sidebar');?>
	<!-- END sidebar -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE header-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">
						<?php echo $this->lang->line('user_log_management'); ?>
					</h3>
					<ul class="page-breadcrumb breadcrumb">
						<li>
							<i class="fa fa-home"></i>
							<a href="<?php echo base_url().ADMIN_URL?>/dashboard">
							<?php echo $this->lang->line('home'); ?> </a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<?php echo $this->lang->line('user_log'); ?>
						</li>
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<!-- END PAGE header-->
			<?php //export user logs :: start
			if(in_array('user_log~view',$this->session->userdata("UserAccessArray"))) { ?>
			<div class="row">
					<div class="col-md-12">
						<!-- BEGIN EXAMPLE TABLE PORTLET-->
						<div class="portlet box red">
							<div class="portlet-title">
								<div class="caption"><?php echo $this->lang->line('export_logs') ?></div>
							</div>
							<div class="portlet-body form">
								<div class="form-body">
									<?php if(isset($_SESSION['not_found'])) { ?>
										<div class="alert alert-danger">
											<?php echo $_SESSION['not_found'];
											unset($_SESSION['not_found']); ?>
										</div>
									<?php } ?>
									<form action="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/export_logs" id="export_logs" name="export_logs" method="post" class="horizontal-form" enctype="multipart/form-data" >
										<div class="row">
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label"><?php echo $this->lang->line('from_date') ?></label>
													<input type="text" class="form-control date-picker" readonly name="start_date" id="start_date" placeholder="<?php echo $this->lang->line('from') ?>">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label class="control-label"><?php echo $this->lang->line('to_date') ?></label>
													<input type="text" class="form-control date-picker" readonly name="end_date" id="end_date" placeholder="<?php echo $this->lang->line('to') ?>">
												</div>
											</div>
											<div class="col-md-2">
												<button type="submit" style="position: absolute;top: 30px;" name="submitPage" id="submitPage" value="Generate" class="btn btn-success default-btn danger-btn theme-btn"><i class="fa fa-download"></i> <?php echo $this->lang->line('download') ?></button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<!-- END EXAMPLE TABLE PORTLET-->
					</div>
				</div>
			<?php }
			//export user logs :: end ?>
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box red">
						<div class="portlet-title">
							<div class="caption">
								<?php echo $this->lang->line('user_log'); ?>
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-container">
								<?php if(isset($_SESSION['page_MSG'])) { ?>
									<div class="alert alert-success">
										<?php echo $_SESSION['page_MSG'];
										unset($_SESSION['page_MSG']); ?>
									</div>
								<?php } ?>
								<div id="delete-msg" class="alert alert-success hidden">
									<?php echo $this->lang->line('success_delete');?>
								</div>
								<table class="table table-striped table-bordered table-hover" id="datatable_ajax">
									<thead>
										<tr role="row" class="heading">
											<th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
											<th><?php echo $this->lang->line('updated_by'); ?></th>
											<th><?php echo $this->lang->line('logs'); ?></th>
											<th><?php echo $this->lang->line('date/time')?></th>
											<th><?php echo $this->lang->line('action'); ?></th>
										</tr>
										<tr role="row" class="filter">
											<td></td>
											<td><input type="text" class="form-control form-filter input-sm" name="full_name_search"></td>
											<td><input type="text" class="form-control form-filter input-sm" name="action_search"></td>
											<td>
                                                <input type="text" class="form-control form-filter input-sm order-date-picker" name="created_date_search" id="created_date_search"  placeholder="<?php echo $this->lang->line('select_date'); ?>">
                                            </td>
											<td style="white-space: nowrap;">
												<button id="searchcountry" class="btn btn-sm red filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
												<button class="btn btn-sm red filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
											</td>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/scripts/bootstrap-datepicker.js"></script>
<!-- daterangepicker(start) -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/daterangepicker/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/daterangepicker/js/daterangepicker.min.js"></script>
<!-- daterangepicker(end) -->
<script>
var grid;
var datepicker_format = "<?php echo datepicker_format; ?>";
var daterangepicker_format = "<?php echo daterangepicker_format; ?>";
var user_log_count = <?php echo ($user_log_count)?$user_log_count:0; ?>;
jQuery(document).ready(function() {
	Layout.init();
	grid = new Datatable();
	grid.init({
		src: $("#datatable_ajax"),
		onSuccess: function(grid) {
		},
		onError: function(grid) { 
		},
		dataTable: {
			"sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
			"aoColumns": [
				{ "bSortable": false },
				null,
				null,
				null,
				{ "bSortable": false }
			],
			"sPaginationType": "bootstrap_full_number",
			"oLanguage":{
				"sProcessing": sProcessing,
				"sLengthMenu": sLengthMenu,
				"sInfo": sInfo,
				"sInfoEmpty":'', //sInfoEmpty,
				"sGroupActions":sGroupActions,
				"sAjaxRequestGeneralError": sAjaxRequestGeneralError,
				"sEmptyTable": sEmptyTable,
				"sZeroRecords":sZeroRecords,
				"oPaginate": {
					"sPrevious": sPrevious,
					"sNext": sNext,
					"sPage": sPage,
					"sPageOf":sPageOf,
					"sFirst": sFirst,
					"sLast": sLast
				}
			},
			"bStateSave": true,
			"fnStateSave": function (oSettings, oData) {
				if(oSettings.aoData.length == 0 && user_log_count != 0 && oData.iStart >= user_log_count){
					oData.iStart = 0;
					localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
					location.reload();
					//grid.getDataTable().fnDraw();
				} else {
					localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
				}
			},
			"fnStateLoad": function (oSettings) {
				var data = localStorage.getItem('DataTables_' + window.location.pathname);
				return JSON.parse(data);
			},
			"fnStateLoadParams": function (oSettings, oData) {
				oData.aaSorting = [[ 4, "desc" ]];
			},
			"bServerSide": true,
			"sAjaxSource": "ajaxview"
		}
	});
	$('#datatable_ajax_filter').addClass('hide');
	$('input.form-filter, select.form-filter').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#searchcountry').click();
		}
	});
	var startdateval = "<?php echo date_format(date_create(date('Y-m-01 00:00:00')." -2 months"),'Y-m-d'); ?>";
	$(".date-picker").datepicker( {
		//format: "dd-mm-yyyy",
		format: datepicker_format,
		startDate: new Date(startdateval),
		endDate: '+0d',
		/*startView: "months", 
		minViewMode: "months",*/
		autoclose: true    
	});
	$('.order-date-picker').focus(function() {
		$(this).daterangepicker({
			opens: 'left',
			// startDate: moment().subtract(10, 'day'),
			startDate: moment(),
			endDate: moment(),
			ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				//'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
				'This Year': [moment().startOf('year'), moment().endOf('year')],
				'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
			},
			locale: {
				format: daterangepicker_format
			}
		}, function(start, myDate, label) {
		});
	});

	$('.order-date-picker').on('cancel.daterangepicker', function(ev, picker) {
		$('.order-date-picker').val('');
	});
});
jQuery('#export_logs').validate({
  rules:{
    start_date: {
      required:true
    },
    end_date: {
      required:true
    },
  },
  errorPlacement: function (error, element) {
      var elm = $(element);
      if(elm.next('p').length > 0){
          error.insertAfter(elm.next('p'));
      } else {
          error.insertAfter(elm);
      }
  }
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>