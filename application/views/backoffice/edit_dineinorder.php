<?php 
$this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
<!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar');
 
if($this->input->post())
{
    foreach ($this->input->post() as $key => $value){
        if(!is_array($value)){
            $$key = @htmlspecialchars($this->input->post($key));
        }
    } 
}else
{
    $FieldsArray = array('entity_id','order_id','user_id','restaurant_id','coupon_id','table_id','total_rate','subtotal','tax_rate','tax_type','coupon_discount','coupon_name', 'coupon_amount', 'restaurant_name', 'user_name', 'table_number', 'item_detail', 'coupon_type','extra_comment', 'service_fee_type','service_fee');

    foreach ($FieldsArray as $key) {
        if($key!='item_detail'){
            $$key = @htmlspecialchars($editorder_detail->$key);
        }
    }
}
if(isset($editorder_detail) && $editorder_detail !="")
{
    $add_label     = $this->lang->line('title_admin_orderdineedit');        
    $form_action   = base_url().ADMIN_URL.'/'.$this->controller_name."/edit_dinein_order_details/".str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($editorder_detail->entity_id));

    $menu_item = array('1'=>'');
    if($editorder_detail->item_detail && !empty($editorder_detail->item_detail))
    {
        $item_detail = $editorder_detail->item_detail;
    }
}
else
{
    $add_label    = $this->lang->line('title_admin_orderadd');       
    $form_action      = base_url().ADMIN_URL.'/'.$this->controller_name."/add";
    $menu_item = 1;
}
$restaurant_id = isset($_POST['restaurant_id'])?$_POST['restaurant_id']:$restaurant_id;
$menu_detail     = $this->order_model->getOrderItem($restaurant_id);
$Menucategory =  $this->order_model->getItemCategory($restaurant_id);
?>

<div class="page-content-wrapper">
        <div class="page-content">            
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('dine_in').' '.$this->lang->line('orders'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name?>/dine_in_orders"><?php echo $this->lang->line('dine_in').' '.$this->lang->line('orders'); ?></a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $add_label;?> 
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $add_label;?> (<?=$entity_id?>)</div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $form_action;?>" id="form_add_dinein" name="form_add_dinein" method="post" class="form-horizontal" enctype="multipart/form-data" >
                                <div id="iframeloading" style= "display: none;" class="frame-load">
                                     <img src="<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif" alt="loading" />
                                </div>
                                <div class="form-body"> 
                                    <?php if(!empty($Error)){?>
                                    <div class="alert alert-danger"><?php echo $Error;?></div>
                                    <?php } ?>                                    
                                    <?php
                                    if(isset($_SESSION['page_Error']))
                                    { ?>
                                        <div class="alert alert-danger">
                                             <?php echo $_SESSION['page_Error'];
                                             unset($_SESSION['page_Error']);
                                             ?>
                                        </div>
                                    <?php } ?>                                  
                                    <?php if(validation_errors()){?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors();?>
                                    </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id; ?>">
                                                <label class="control-label" style="padding-left:15px; padding-top: 0px;min-height:1px"><?php echo $this->lang->line('user') ?></label>: <label class="align-middle"> <?=$user_name;?></label>
                                            </div>

                                            <div class="col-md-6 text-center">
                                                <input type="hidden" name="restaurant_id" value="<?=$restaurant_id?>" id="restaurant_id">
                                                <label class="control-label" style="padding-left:15px; padding-top: 0px;min-height:1px"><?php echo $this->lang->line('restaurant') ?></label>: <label class="align-middle"> <?=$restaurant_name;?></label>
                                            </div>
                                            <div class="col-md-2 col-md-offset-1">
                                                <input type="hidden" name="table_id" id="table_id" value="<?php echo $table_id; ?>">
                                                <label class="control-label" style="padding-left:15px; padding-top: 0px;min-height:1px"><?php echo $this->lang->line('table_no') ?></label>: <label class="align-middle"> <?=$table_number;?></label>
                                            </div>
                                        </div>
                                    </div>

                                    <?php 
                                     /*echo "<pre>"; print_r($menu_item);
                                     echo "<br>++++++++++++++++++++++<br>";
                                     echo "<pre>"; print_r($item_detail); exit;*/
                                    ?> 

                                    <div class="form-group">
                                        <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?></label>
                                        <div class="col-md-9">
                                            <div class="table-container">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr role="row" class="heading">
                                                        <th><?php echo $this->lang->line('item')?>#</th>
                                                        <th><?php echo $this->lang->line('item_name')?></th>
                                                        <th><?php echo $this->lang->line('quantity')?></th>
                                                        <th><?php echo $this->lang->line('total_rate')?></th>
                                                        <th><?php echo $this->lang->line('action')?></th>
                                                    </tr>
                                                </thead>                                        
                                                <tbody>
                                                    <?php if(!empty($item_detail)){
                                                    foreach ($item_detail as $key => $value) { ?>
                                                        <tr role="row" class="heading">
                                                            <td><?php echo $key+1; ?></td>
                                                            <td><?php echo $value['item_name']; ?>
                                                                <ul class="ul-disc">
                                                                <?php if (!empty($value['addons_category_list'])) {
                                                                    foreach ($value['addons_category_list'] as $key => $cat_value) { ?>
                                                                        <li><span><?php echo $cat_value['addons_category']; ?>
                                                                        <?php if (!empty($cat_value['addons_list'])) {
                                                                            foreach ($cat_value['addons_list'] as $key => $add_value) { ?>
                                                                                <?php echo $add_value['add_ons_name']; ?>  <?php echo $order_details[0]['currency_symbol']; ?><?php echo $add_value['add_ons_price']; ?>
                                                                            <?php }
                                                                        } ?>
                                                                        </span></li>
                                                                    <?php }
                                                                } ?>
                                                                </ul>
                                                            </td>
                                                            <td><?php echo $value['qty_no']; ?></td>
                                                            <td><?php echo $value['itemTotal']; ?></td>
                                                            <td>
                                                            <?php if(count($item_detail)>1) { ?>
                                                                <a onclick="deleteOrderItem(<?=$value['item_id']?>,<?=$value['order_flag']?>,<?=$entity_id;?>)"  title="<?=$this->lang->line('reject')?>" class="delete btn btn-sm default-btn margin-bottom theme-btn"><i class="fa fa-ban" aria-hidden="true"></i></a>
                                                             <? } else { ?>   
                                                                -
                                                             <?php } ?>   
                                                            </td>
                                                        </tr>
                                                    <?php } } 
                                                     ?>
                                                </tbody>
                                            </table>
                                            </div>
                                        </div>

                                        <?php for($i=0,$inc=1;$i<count($menu_item);$inc++,$i++){ ?>
                                            <div class="clone" id="cloneItem<?php echo $inc ?>">
                                                <label class="control-label col-md-3 clone-label"><?php echo $this->lang->line('menu_item') ?><span class="required">*</span></label>
                                                <div class="col-md-2">
                                                    <select name="item_id[<?php echo $inc ?>]" class="form-control item_id validate-class" id="item_id<?php echo $inc ?>" onchange="getItemPrice(this.id,<?php echo $inc ?>,this.value)">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option> 
                                                        <?php if($entity_id){
                                                            if(!empty($menu_detail)){
                                                            foreach ($menu_detail as $key => $value) { ?>
                                                                <optgroup label=<?php echo $Menucategory[$key]->cat_name ?>>
                                                                <?php foreach ($value as $ky => $val) { ?>
                                                                     <option value="<?php echo $val->entity_id ?>" data-id="<?php echo $val->price ?>"  data-addons="<?php echo $val->check_add_ons ?>" ><?php echo $val->name ?></option>  
                                                                <?php } ?> 
                                                                </optgroup> 
                                                        <?php } } }?> 
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <input type="text" name="qty_no[<?php echo $inc ?>]" id="qty_no<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]['qty_no'])?$menu_item[$i]['qty_no']:'' ?>" maxlength="3" data-required="1" onkeyup="qty(this.id,<?php echo $inc ?>,<?=$menu_item[$i]['qty_no'];?>)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>"/>
                                                </div>
                                                <!-- base price changes start -->
                                                <div class="col-md-2">
                                                    <input type="text" placeholder="<?php echo $this->lang->line('base_price') ?>" name="base_price[<?php echo $inc ?>]" id="base_price<?php echo $inc ?>" value="<?php echo isset($menu_item[$i]['rate'])?$menu_item[$i]['rate']:'' ?>" maxlength="20" data-required="1" class="form-control base_price validate-class" readonly="" />
                                                </div>
                                                <!-- base price changes end -->
                                                <div class="col-md-2">
                                                    <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" name="rate[<?php echo $inc ?>]" id="rate<?php echo $inc ?>" value="" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                </div>
                                                <div class="col-md-1 remove"><?php if($entity_id && $inc > 1){ ?><div class="item-delete" onclick="deleteItem(<?php echo $inc ?>)"><i class="fa fa-remove"></i></div><?php } ?></div>

                                                <!-- div to display addons (start)-->
                                                <div class="col-md-12 addOns_wrapcls" id="addOns_wrap<?php echo $inc; ?>" style="margin-top: 12.5px; margin-bottom: 12.5px; padding: 0px;display: none;">
                                                    <label class="control-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?><span class="required">*</span></label>
                                                    <div class="col-md-9" id="addOns<?php echo $inc; ?>"></div>
                                                </div>
                                                <!-- div to display addons (end) -->
                                            </div>
                                        <?php } ?>
                                        <div id="Optionplus" onclick="cloneItem()"><div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div></div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon') ?></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="coupon_name" readonly id="coupon_name" value="<?php echo $coupon_name; ?>">
                                            <input type="hidden" class="form-control" name="coupon_id" readonly id="coupon_id" value="<?php echo $coupon_id; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_discount') ?></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="coupon_amount" id="coupon_amount" value="<?php echo ($coupon_amount)?$coupon_amount:'' ?>" maxlength="10" data-required="1" class="form-control" readonly="" style="width:79%;display:inline-block;"/><label class="coupon-type" style="display:inline-block;padding-left: 10px;"><?php echo ($coupon_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="coupon_type" id="coupon_type" value="<?php echo $coupon_type; ?>">
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('service_tax') ?></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="tax_rate" id="tax_rate" value="<?php echo $tax_rate ?>" maxlength="10" data-required="1" class="form-control" style="width:79%;display:inline-block;" readonly=""/><label class="amount-type" style="display:inline-block;padding-left: 10px;"><?php echo ($tax_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="tax_type" id="tax_type" value="<?php echo $tax_type; ?>">
                                        </div>
                                    </div> 

                                     <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('service_fee') ?></label>
                                        <div class="col-md-5">
                                            <input type="text" data-value="" name="service_fee" id="service_fee" value="<?php echo $service_fee ?>" maxlength="10" data-required="1" class="form-control" readonly="" style="width:79%;display:inline-block;"/><label class="service-type" style="display:inline-block;padding-left: 10px;"><?php echo ($service_fee_type == 'Percentage')?'%':'' ?></label>
                                            <input type="hidden" name="service_fee_type" id="service_fee_type" value="<?php echo $service_fee_type; ?>">
                                        </div>
                                    </div> 

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('sub_total') ?> <span class="currency-symbol"></span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="subtotal" id="subtotal" value="<?php echo ($subtotal)?$subtotal:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                            <input type="hidden" name="subtotal_old" id="subtotal_old" value="<?php echo ($subtotal)?$subtotal:''; ?>" maxlength="10" class="form-control" readonly=""/>
                                        </div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('total_rate') ?> <span class="currency-symbol"></span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="total_rate" id="total_rate" value="<?php echo ($total_rate)?$total_rate:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                            <input type="hidden" name="total_rate_old" id="total_rate_old" value="<?php echo ($total_rate)?$total_rate:''; ?>" maxlength="10" data-required="1" class="form-control" readonly=""/>
                                        </div>
                                    </div>
                                    <?php if(!empty($extra_comment)){ ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('view_comment') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="extra_comment" id="extra_comment" value="<?php echo ($extra_comment)?$extra_comment:''; ?>"  class="form-control" readonly=""/>
                                            </div>
                                        </div> 
                                    <?php } ?>
                                </div>    
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success default-btn theme-btn"><?php echo $this->lang->line('update_order') ?></button>&ensp;
                                        <a class="btn btn-danger default-btn theme-btn" href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name;?>/dine_in_orders"><?php echo $this->lang->line('back_to_dinein') ?></a>
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
    <!-- END CONTENT -->
</div>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
jQuery(document).ready(function() {       
    Layout.init(); // init current layout
    //Added on 19-10-2020
    $('.sumo').SumoSelect({search: true, searchText: "<?php echo $this->lang->line('search'); ?>"+ ' ' + "<?php echo $this->lang->line('here'); ?>..."});
});
$(function() {
    var date = new Date();
    $('#order_date').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        startDate : date
    });
});
//clone items
function cloneItem(){
    var divid = $(".clone:last").attr('id'); 
    var getnum = divid.split('cloneItem');
    var oldNum = parseInt(getnum[1]);
    var newNum = parseInt(getnum[1]) + 1;
    newElem = $('#' + divid).clone().attr('id', 'cloneItem' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
    newElem.find('#item_id'+oldNum).attr('id', 'item_id' + newNum).attr('name', 'item_id[' + newNum +']').attr('onchange','getItemPrice(this.id,'+newNum+',this.value)').prop('selected',false).attr('selected',false).val('').removeClass('error');
    newElem.find('#rate'+oldNum).attr('id', 'rate' + newNum).attr('name', 'rate[' + newNum +']').val('').removeClass('error');

    //base price changes start
    newElem.find('#base_price'+oldNum).attr('id', 'base_price' + newNum).attr('name', 'base_price[' + newNum +']').val('').removeClass('error');
    //base price changes end
    newElem.find('#qty_no'+oldNum).attr('id','qty_no'+newNum).attr('name','qty_no['+newNum+']').attr('onkeyup','qty(this.id,'+newNum+',1)').val(1).removeClass('error');
    //addons changes start
    newElem.find('#addOns_wrap'+oldNum).attr('id', 'addOns_wrap' + newNum).removeClass('error');
    newElem.find('#addOns'+oldNum).empty();
    newElem.find('#addOns'+oldNum).attr('id', 'addOns' + newNum).removeClass('error');
    //addons changes end
    newElem.find('.error').remove();
    newElem.find('.clone-label').css('visibility','hidden');
    $(".clone:last").after(newElem);
    $('#cloneItem' + newNum +' .remove').html('<div class="item-delete" onclick="deleteItem('+newNum+')"><i class="fa fa-remove"></i></div>');
    validateDynamicMenu();
}
function deleteItem(id){
    $('#cloneItem'+id).remove();
    calculation();
}
//get item price
function getItemPrice(id,num,value){
    var element = $('#'+id).find('option:selected'); 
    var myTag = element.attr("data-id"); 
     // addons changes (start)
    var checkAddOns = element.attr("data-addOns");
    if(checkAddOns == "1") {
        var html = '<label class="control-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?><span class="required">*</span></label><div class="col-md-9" id="addOns'+num+'"></div>';
        $("#addOns_wrap"+num).empty().append(html);
        $("#addOns_wrap"+num).css("display","block");
        $('#qty_no' + num).prop("readonly", false);
        $('#qty_no' + num).val(1);
        // $('#qty_no' + num).val('');
        // $('#rate' + num).val('');
        //base price changes start
        if(myTag){
            $('#rate' + num).val(myTag);
            $('#base_price' + num).val(myTag);
        }
        //base price changes end
    } else {
        //var html = '<label class="control-label col-md-3"><?php //echo $this->lang->line('add_add_ons') ?><span class="required">*</span></label><div class="col-md-9" id="addOns'+num+'"></div>';
        $("#addOns_wrap"+num).empty();
        $("#addOns_wrap"+num).css("display","none");
        $('#rate'+num).val(myTag);
        //base price changes start
        $('#base_price'+num).val(myTag);
        //base price changes end
        $('#qty_no' + num).prop("readonly", false);
        $('#qty_no' + num).val(1);
    }
    // addons changes (end)
    //$('#rate'+num).val(myTag);
    calculation();
    getAddonsList(value,num);
}
// addons changes (start)
function getAddonsList(menu_entity_id,num){
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : '<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/getAddonsList',
      data : {'entity_id':menu_entity_id,'num':num},
      success: function(response) {
        $('#addOns'+num).empty().append(response);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
}
// addons changes (end)
function qty(id,num,frmqty) {
    $('#'+id).keyup(function(){
        this.value = this.value.replace(/[^0-9]/g,'');
    });
    var element = $('#item_id'+num).find('option:selected'); 
    var myTag = element.attr("data-id");
    var qtydata = parseInt($('#qty_no' + num).val());
    if(isNaN(qtydata)){    
        qtydata = 0;
        //changes for base price start
        if(myTag && frmqty==0){
            qtydata = 1;
        }
        //changes for base price end
    }
    //alert(myTag);
    var total = parseFloat(qtydata * myTag);
    
    if(!isNaN(total)){    
        $('#rate'+num).val(total.toFixed(2));
    }
    //changes for addons start
    var price = 0;
    var elements = $('#addOns'+num).find('input:checked');
    //changes for base price start
    if(myTag){
        price += parseFloat(myTag);
    }
    //changes for base price end
    elements.each(function()
    {
        if(!isNaN($(this).attr('data-price')) && (!isNaN($(this).attr('value'))) && $(this).attr('value') != '' && $(this).attr('data-price') != ''){
            var temp_addons_id = $(this).attr('value');
            var qt = parseInt($('#add_qty_no_'+num+''+temp_addons_id).val());
            price += parseFloat(qt) * parseFloat($(this).attr('data-price')); 
            $('#rate'+num).val(parseFloat(price).toFixed(2));
        }
    });

    if(frmqty==1 || frmqty==2) // myTag == '' &&  - removed this as part of base price changes
    {
       var total = parseFloat(qtydata * price).toFixed(2);
       if(!isNaN(total)){    
            $('#rate'+num).val(total);
        }
    }
    //Code added for the insurance calculation :: 26-09-2020 :: Start
    var pricecal = total;
    pricecal = parseFloat(pricecal).toFixed(2);
    // changes for addons end
    calculation();
}
// addons changes (start)
function calculate_rate(num,addons_id,chkradio,catname)
{
    if(chkradio=='1')
    {
        $('.radioclassq_'+num+''+catname).val('');
        $('.radioclassqp_'+num+''+catname).prop("readonly", true);
    }

    var value = $('#add_qty_no_'+num+''+addons_id).val();
    // console.log('value =' + value);
    
    // console.log('addons =' + $('#addons_id_'+num+''+addons_id).prop("checked"));
    if(value=='' && ($('#addons_id_'+num+''+addons_id).prop("checked") == true))
    {
        $('#add_qty_no_'+num+''+addons_id).val('1');
    }
    else if($('#addons_id_'+num+''+addons_id).prop("checked") == false)
    {
        $('#add_qty_no_'+num+''+addons_id).val('');
    } 

    var val = 0;
    var elements = $('#addOns'+num).find('input:checked'); // find checked checkbox
    if(elements) {
        $('#qty_no'+num).val('');
    }
    elements.each(function()
    {
        if(!isNaN($(this).attr('value')) && $(this).attr('value') != '')
        {
            var temp_addons_id = $(this).attr('value'); // it will return add-on_ids
            val = parseInt($('#add_qty_no_'+num+''+temp_addons_id).val()); // get value of addon_quantity added
            $('#qty_no'+num).val(parseInt(val)); //set it to parent quantity
        }
    });

   qty('qty_no'+num,num,0); // to count final rate
}
// addons changes (end)
//calculate total rate
function calculation()
{
    var type = $('#coupon_type').val(); 
    var amount = $('#coupon_amount').val(); 

    var sing = (type == "Percentage") ? "%" : '';
    $('.coupon-type').html(sing);
    var sum = 0;
    $('.rate').each(function(){
        if(!isNaN($(this).val()) && $(this).val() != ''){
            sum += parseFloat($(this).val()); 
        }
    });

    //Code for Old sub total :: Start
    var subtotal_old =$('#subtotal_old').val();
    if(!isNaN(subtotal_old) && subtotal_old != '')
    {
       sum = sum+parseFloat(subtotal_old);
    }
    //Code for Old sub total :: End

    $('#subtotal').val(sum.toFixed(2));
    //coupon
    var cpn_amt = 0;
    if(type == 'Percentage' && amount != ''){
        var cpn = parseFloat(sum*amount)/100;
        cpn_amt = cpn;
    }else if(type == 'Amount' && amount != ''){
        cpn_amt =  amount;
    }
    //tax
    var tax = $('#tax_rate').val();
    var taxval = 0;
    if($('.amount-type').html() == '' && !isNaN(tax) && tax != '')
    {
        //sum = sum + parseFloat(tax); 
        taxval = tax;
    }
    else if(!isNaN(tax) && tax != '')
    {
        var taxval = Number(parseFloat(sum*tax)/100).toFixed(2);
        //sum += parseInt(taxs);
    }

    //service fee
    var service_fee = $('#service_fee').val();
    var service_feeval = 0;
    if($('.service-type').html() == '' && !isNaN(service_fee) && service_fee != '')
    {
        //sum = sum + parseFloat(tax); 
        service_feeval = service_fee;
    }
    else if(!isNaN(service_fee) && service_fee != '')
    {
        var service_feeval = Number(parseFloat(sum*service_fee)/100).toFixed(2);
        //sum += parseInt(taxs);
    }

    if(!isNaN(cpn_amt) && cpn_amt > 0){
        sum = sum - parseFloat(cpn_amt);
    }
    if(!isNaN(service_feeval) && service_feeval>0){
        sum = sum + parseFloat(service_feeval);
    }
    if(!isNaN(taxval) && taxval>0){
        sum = sum + parseFloat(taxval);
    }

    if(!isNaN(sum)){
        $('#total_rate').val(sum.toFixed(2));
    }
}

jQuery(document).ready(function() {
    validateDynamicMenu();   
});    
function validateDynamicMenu(){
    $('#form_add_dinein').find('.validate-class').each(function(){
        $(this).rules("add", 
        {
            required: true
        });
    });
}
//validation for menu item
/*$('#form_add_dinein').bind('submit',function(e){ 
    $('.validate-class').each(function(){
        var id = $(this).attr('id');
        if($('#'+id).val() == ''){
            $('#'+id).attr('required',true);
            $('#'+id).addClass('error');
        }
    });
});*/

function format_indonesia_currency(amt) {
    var number = amt;       
    return  n =  number.toLocaleString('id-ID', {currency: 'IDR'});
}
//Code for item delete from current order :: Start
function deleteOrderItem(item_id,order_flag,entity_id)
{
    bootbox.confirm({
        message: "<?php echo $this->lang->line('reject_item'); ?>",
        buttons: {
            confirm: {
                label: "<?php echo $this->lang->line('ok'); ?>",
            },
            cancel: {
                label: "<?php echo $this->lang->line('cancel'); ?>",
            }
        },
        callback: function (deleteConfirm) {
            if (deleteConfirm) { 
                jQuery.ajax({
                  type : "POST",
                  dataType : "html",
                  url : BASEURL+"backoffice/order/ajaxOrderItemDelete",
                  data : {'entity_id':entity_id,'item_id':item_id,'order_flag':order_flag},
                  success: function(response) { 
                    //grid.getDataTable().fnDraw(); 
                   location.reload();
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
//Code for item delete from current order :: End
$(document).ready(function(){
    $('#form_add_dinein').submit(function() {
        $('.validate-class').each(function(){
            var id = $(this).attr('id');
            if($('#'+id).val() == ''){
                $('#'+id).attr('required',true);
                $('#'+id).addClass('error');
            }
        });
        $('.addon-validate').each(function() {
            var checkbox_count = $(this).find('.check_addons').filter(':checked').length;
            var radio_count = $(this).find('.radio_addons').filter(':checked').length;
            if(checkbox_count == 0){
                $(this).find('.check_addons').each(function () {
                    $(this).rules("add", { 
                        required:true
                    });
                });
            }
            if(radio_count == 0){
                $(this).find('.radio_addons').each(function () {
                    $(this).rules("add", { 
                        required:true
                    });
                });
            }
        });
        if(jQuery('#form_add_dinein').valid()) {
            form.submit();
        }else{
            return false;
        }
    });
});
</script>
<?php $this->load->view(ADMIN_URL.'/addons_max_selection');?>
<?php $this->load->view(ADMIN_URL.'/footer');?>