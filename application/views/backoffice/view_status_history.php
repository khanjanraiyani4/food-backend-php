<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('status')?> <?php echo $this->lang->line('list')?></h4>
      </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('status')?> <?php echo $this->lang->line('list')?></div>
                            <div class="actions"></div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th><?php echo $this->lang->line('order')?>#</th>
                                            <th><?php echo $this->lang->line('status')?></th>
                                            <th><?php echo $this->lang->line('date/time')?></th>
                                            <th><?php echo $this->lang->line('changed_by')?></th>
                                        </tr>
                                    </thead>                                        
                                    <tbody>
                                        <?php if(!empty($history)){
                                        foreach ($history as $key => $value) {
                                          
                                        /*if($value->order_status=='preparing' && strtolower($value->status_created_by)=='driver')    
                                        {
                                            $ostatuslng = 'Accepted by Driver';
                                        }*/
                                        if($value->order_status == "placed" && $order_history->status!='1')
                                        {
                                            $ostatuslng = $this->lang->line('placed');
                                        }
                                        if(($value->order_status == "placed" && $order_history->status=='1') || $value->order_status == "accepted" || $value->order_status == "accepted_by_restaurant")
                                        {
                                            $ostatuslng = $this->lang->line('accepted');
                                        }
                                        if($value->order_status == "rejected")
                                        {
                                            $ostatuslng = $this->lang->line('rejected');
                                        }
                                        if($value->order_status == "delivered"){
                                            $ostatuslng = $this->lang->line('delivered');
                                        }
                                        if($value->order_status == "onGoing")
                                        {
                                            $ostatuslng = $this->lang->line('onGoing');
                                            if($order_history->order_delivery == "PickUp")
                                            {
                                                $ostatuslng = $this->lang->line('order_ready');
                                            }
                                        }
                                        
                                        if($value->order_status == "cancel"){
                                            $ostatuslng = $this->lang->line('cancel');
                                        }
                                        if($value->order_status == "ready")
                                        {
                                            $ostatuslng = $this->lang->line('order_ready');
                                            if($order_history->order_delivery == "DineIn")
                                            {
                                                 $ostatuslng = $this->lang->line('served');
                                            }
                                        }
                                        if($value->order_status == "complete"){
                                            $ostatuslng = $this->lang->line('complete');
                                        }
                                        /*if($value->order_status == "preparing"){
                                            $ostatuslng = $this->lang->line('preparing');
                                        }*/
                                        if($value->order_status == "pending"){
                                            $ostatuslng = $this->lang->line('pending');
                                        }
                                        if($ostatuslng=='')
                                        {
                                            $ostatuslng= ucfirst($value->order_status);
                                        }
                                         ?>
                                        <?php $status_created_by = '';
                                            if($value->status_created_by == 'DoorDash' || $value->status_created_by == 'Relay') {
                                                $status_created_by = $value->status_created_by;
                                            } else if($value->status_created_by == 'auto_cancelled') {
                                                $status_created_by = 'Auto Cancelled';
                                            } else {
                                                $status_created_by = ($value->user_full_name) ? $value->user_full_name : $value->status_created_by;
                                                $status_created_by = ($value->user_full_name && trim($value->user_full_name) != '') ? $value->user_full_name : $value->status_created_by;
                                                if($status_created_by == 'MasterAdmin') {
                                                    $status_created_by = 'Master Admin';
                                                }
                                            } ?>
                                            <tr role="row" class="heading">
                                                <td><?php echo $value->order_id; ?></td>
                                                <td><?php echo $ostatuslng; ?></td>
                                                <td><?php echo ($value->time)?$this->common_model->getZonebaseDateMDY($value->time):''; ?></td>
                                                <td><?php echo $status_created_by; ?></td>
                                            </tr>
                                        <?php } } else { ?>
                                            <tr><td colspan="4"><?php echo $this->lang->line('not_found')?></td></tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
        </div>
    </div>
</div>