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
						<?php echo $this->lang->line('contact_inquiries_management'); ?>
					</h3>
					<ul class="page-breadcrumb breadcrumb">
						<li>
							<i class="fa fa-home"></i>
							<a href="<?php echo base_url().ADMIN_URL?>/dashboard">
							<?php echo $this->lang->line('home'); ?> </a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<?php echo $this->lang->line('contact_inquiries'); ?>
						</li>
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<!-- END PAGE header-->			
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box red">
						<div class="portlet-title">
							<div class="caption">
								<?php echo $this->lang->line('contact_inquiries'); ?>
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
											<th><?php echo $this->lang->line('first_name'); ?></th>
											<th><?php echo $this->lang->line('last_name'); ?></th>
											<th><?php echo $this->lang->line('email')?></th>
											<th><?php echo $this->lang->line('restaurant')?></th>
											<th><?php echo $this->lang->line('phone_number')?></th>
											<th><?php echo $this->lang->line('message')?></th>
											<th><?php echo $this->lang->line('date/time')?></th>
											<th><?php echo $this->lang->line('action'); ?></th>
										</tr>
										<tr role="row" class="filter">
											<td></td>
											<td><input type="text" class="form-control form-filter input-sm" name="first_name"></td>
											<td><input type="text" class="form-control form-filter input-sm" name="last_name"></td>
											<td><input type="text" class="form-control form-filter input-sm" name="email"></td>
											<td><input type="text" class="form-control form-filter input-sm" name="rest_name"></td>
											<td><input type="text" class="form-control form-filter input-sm" name="phone_number"></td>
											<td></td>
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
				null,
				{ "bSortable": false },
				{ "bSortable": false },
				{ "bSortable": false },
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
			//"bStateSave": true,
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
				oData.aaSorting = [[ 5, "desc" ]];
			},
			"bServerSide": true,
			"sAjaxSource": "ajaxview",
			"aaSorting": [[ 5, "desc" ]] // set first column as a default sort by asc
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
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>