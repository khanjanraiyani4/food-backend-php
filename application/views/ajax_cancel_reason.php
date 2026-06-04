<style type="text/css">
  .cancel-btn{
    padding-top: 18px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 3px;
  }
  .cancel-btn .btn{
    padding: 0.4rem 0.4rem;
  }
  @media (max-width: 767px){
    .cancel-btn{
    padding-top: 18px;
    font-size: 14px;
    font-weight: 400;
    border-radius: 3px;
  }
  .cancel-btn .btn{
    font-size: 14px;
    padding: 3px 4px;
  }
  }
  input[type=text] {
    width:100%;
    border: none;
    border-bottom: 2px solid;
    padding-top:10px;
}
.modal-main.order-detail-popup .modal-dialog {
    max-width: 700px;
}
</style>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('select_cancel_reason') ?></h4>
        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <?php if($is_cancel_order == 'yes'){ ?>
          <form action='' id="cancel_reason_form" name="cancel_reason_form" method="post" class="form-horizontal float-form">
            <?php foreach ($cancel_order_reasons as $key => $value) { ?>
            <div class="radio-btn-list">
              <label>
              <input type="radio" name="filter_reason" class="radio_addons" id="filter_<?=$value['entity_id']?>" value="<?=$value['reason']?>"  onclick="removeInput(<?php echo $value['entity_id'] ?>)">
              <span><?php echo $value['reason']; ?></span></label>
            </div>
            <?php  } ?>
            <div class="radio-btn-list">
              <label>
              <input type="radio" checked="checked" name="filter_reason" class="radio_addons" id="all" value="all">
              <span><?php echo $this->lang->line('other') ?></span></label>
              <input type="text" name="other_reason" id="other_reason" style="color: black" class="display-yes" placeholder="<?php echo $this->lang->line('enter_reason') ?>">
              <span id="reason" class="error"></span>
            </div>
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $this->session->userdata('UserID'); ?>">
          <div class="cancel-btn">
            <button type="submit" class="btn" onclick="return cancel_order_reason(<?php echo $order_id; ?>,<?php echo $this->session->userdata('UserID'); ?>)"><?php echo $this->lang->line('submit') ?></button>
          </div>
        </form>
      <?php } ?>
    </div>
  </div>
</div>
<script>
  function removeInput(key){
    $('#other_reason').hide();
    $('#other_reason').val('');
    $('#other_reason').removeClass('error');
    $('.error').hide();
  }
  $('#all').on('click',function(){
    $('#other_reason').show();
  });
</script>