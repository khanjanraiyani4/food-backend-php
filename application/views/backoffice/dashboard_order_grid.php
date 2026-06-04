					<div class="portlet box red order_dashboard">
						<div class="portlet-title">
							<div class="caption"><?php echo $this->lang->line('order') ?></div>
							<div class="actions">
								<a href="<?php echo base_url().ADMIN_URL?>/order/view" class="btn default btn-xs purple-stripe"><?php echo $this->lang->line('view_all') ?></a>
							</div>
						</div>
						<div class="portlet-body">
							<table class="table table-striped table-bordered table-hover" id="datatable_ajax">
								<thead>
									<tr>
										<th><?php echo $this->lang->line('orderid') ?></th>
										<th><?php echo $this->lang->line('customer') ?></th>
										<th><?php echo $this->lang->line('order_total') ?></th>
										<th><?php echo $this->lang->line('status') ?></th>
										<th><?php echo $this->lang->line('date') ?></th>
									</tr>
								</thead>
								<tbody>
									<?php if(!empty($orders)) {
										$i = 1;
										foreach  ($orders as $key => $val) { 
											$currency_symbol = $this->common_model->getCurrencySymbol($val->currency_id); ?>
											<tr>
												<td data-title="<?php echo $this->lang->line('orderid') ?>">
													<?php $href = base_url().ADMIN_URL.'/order/view/order_id/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)); ?>
													<a alt="<?php echo $this->lang->line('orders') ?>" title="<?php echo $this->lang->line('orders')?>" href="<?php echo $href; ?>" style="text-decoration:underline;" ><?php echo $val->entity_id; ?></a>
												</td>
												<td data-title="<?php echo $this->lang->line('customer') ?>"><?php echo $val->user_name ?></td>
												<td data-title="<?php echo $this->lang->line('order_total') ?>"><?php echo currency_symboldisplay(number_format_unchanged_precision($val->rate,$currency_symbol->currency_code),$currency_symbol->currency_symbol); ?></td>
												<td data-title="<?php echo $this->lang->line('status') ?>"><?php echo ucfirst($val->ostatus); ?></td>
												<td data-title="<?php echo $this->lang->line('date') ?>"><?php echo (isset($val->order_date) && date('d-m-Y',strtotime($val->order_date))!=='01-01-1970')?($this->common_model->datetimeFormat($val->order_date)):''; ?></td>
											</tr>
										<?php $i++; } 
									} ?>
								</tbody>
							</table>
						</div>
					</div>