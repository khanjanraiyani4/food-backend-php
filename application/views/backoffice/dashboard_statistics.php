			<div class="col-md-4">
				<!-- lifetimmesale start -->
				<div class="dashboard-stat grey-cascade">
					<div class="visual" style="height: 60px !important; width: 60px !important;">
					</div>
					<div class="details">
						<?php $currency_symbol = $this->common_model->getCurrencySymbol($sale[0]->currency_id); ?>
						<div class="number" style="text-align:center; "><?php echo currency_symboldisplay(number_format($sale[0]->total,'2','.',','),$currency_symbol->currency_symbol); ?></div>
						<div class="desc"><?php echo $this->lang->line('l_sale') ?></div>
					</div>
				</div>
				<!-- lifetiesale end -->
				<!-- for average sale start-->
				<div class="dashboard-stat purple-plum">
					<div class="visual" style="height: 60px !important; width: 60px !important;"></div>
					<div class="details">
						<?php if($currency_symbol=='') {
							$currency_symbol = $this->common_model->getCurrencySymbol($last_month[0]->currency_id);
						} ?>
						<div class="number" style="text-align:center; "><?php echo currency_symboldisplay(number_format($last_month[0]->last_month,'2','.',','),$currency_symbol->currency_symbol); ?></div>
						<div class="desc"><?php echo $this->lang->line('past_sale') ?></div>
					</div>
				</div>
				<!-- average sale end-->
				<!-- for tax start-->
				<div class="dashboard-stat blue-madison">
					<div class="visual" style="height: 60px !important; width: 60px !important;"></div>
					<div class="details">
						<?php if($currency_symbol=='') {
							$currency_symbol = $this->common_model->getCurrencySymbol($this_month[0]->currency_id); 
						} ?>
						<div class="number" style="text-align:center; "><?php echo currency_symboldisplay(number_format($this_month[0]->this_month,'2','.',','),$currency_symbol->currency_symbol); ?></div>
						<div class="desc"><?php echo $this->lang->line('current_sale') ?></div>
					</div>
				</div>
				<!-- tax end -->
			</div>
			<!-- Graph bar start -->
			<div class="col-md-8 col-sm-12 bar-strategy" style="height:200px;">
				<div class="portlet box red">
					<div class="portlet-title">
						<div class="caption"><?php echo $this->lang->line('orders') ?></div>
						<div class="actions" style="margin-top:0px !important;"> 
							<input type="hidden" type="date" class="form-control form-filter input-sm d-inline"name="daterange" id="daterange"  />
							<a href="javascript:void(0);" class="icon daterangeicon">
								<i class="fa fa-bars" style="color:white;font-size: 22px;line-height:normal;margin-top: 0px"></i>
							</a>
						</div>
					</div>
					<figure class="highcharts-figure">
						<div id="container"></div>
					</figure>
				</div>
			</div>
			<!-- Graph bar end -->

<script type="text/javascript">
// range for daterange
var datepicker_format = "<?php echo daterangepicker_format; ?>";
$(document).ready(function(){
    var start = moment().subtract(29, 'days');
    var end = moment();
    function cb(start, end) {
        $('input[name="daterange"]').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('input[name="daterange"]').val(start.format('MM/DD/YYYY') + ' - ' + end.format('MM/DD/YYYY'));
        var daterange = $('#daterange').val();
        load_monthwise_data(daterange);
    }
    $('.daterangeicon').daterangepicker({
        startDate: start,
        endDate: end,
        locale: {
          format: datepicker_format
        },
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        },
    }, cb);
    cb(start, end);
});
</script>