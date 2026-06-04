<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo $this->lang->line('additional_comment')?></h4>
      </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('additional_comment')?></div>
                            <div class="actions"></div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container table-scrollable">
                                <table class="table table-striped table-bordered table-hover">
                                    <?php if(!empty($additional_comment)) { ?>
                                        <tr>
                                            <td><?php echo $additional_comment; ?></td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr><?php echo $this->lang->line('not_found')?></tr>
                                    <?php } ?>
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