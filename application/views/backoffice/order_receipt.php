<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title><?php echo $this->lang->line('site_title').' '.$this->lang->line('receipt'); ?></title>
    <style>
        @media print {
            @page {
                margin: 0 20px;
            }
            html,body{
                margin:0;
                padding:0;
            }
            #printContainer {
                text-align: justify;
            }
            .text-center{
                text-align: center;
            }
            .top-heading{
                text-align: center;
                margin: 5px 0px 8px 0px;
            }
            .top-heading img{
                max-width: 70%;
                object-fit: contain;
                height: 40px;
            }
            .hr-line{
                margin: 5px 0px;
            }
        }
    </style>
</head>
<body onload="window.print();">
<br>
<div id='printContainer'>
    <div class="top-heading">
        <img src="<?php echo base_url(); ?>/assets/admin/img/logo.png" alt="" />
    </div>
    <div align="left" style="margin-left: 10px;">
        <span> <b><?php echo $this->lang->line('email'); ?></b> : <?php echo PRINT_RECEIPT_EMAIL ?> <br> <b><?php echo $this->lang->line('phone'); ?></b> : <?php echo PRINT_RECEIPT_TELEPHONE ?> &ensp;&ensp;&ensp; <br> <b><?php echo $this->lang->line('website'); ?></b> : <?php echo PRINT_RECEIPT_WEBSITE ?></span><br>
    </div>
    <hr class="hr-line">
    <div class="text-center">
        <div>
            <p style="margin: 0px 0px 3px 0px;"><b><?php echo $this->lang->line('order'); ?> #</b><br>
            <span><?php echo $order_records->entity_id; ?></span>
            </p>
        </div>
        <div>
            <p style="margin: 0px 0px 3px 0px;"><b><?php echo $this->lang->line('date'); ?></b><br>
            <span> <?php $date = date("d-m-Y h:i A",strtotime($order_records->order_date)); 
                echo $date; ?></span>
            </p>
        </div>
    </div>
    <hr class="hr-line">
    <?php $restaurant_detail = unserialize($menu_item->restaurant_detail); ?>
    <div align="center">
        <b><?php echo $order_records->order_delivery; ?> <?php echo $this->lang->line('order'); ?></b>
        <?php if(!empty($restaurant_detail->table_number)){ 
                $table_number = $restaurant_detail->table_number;
            }else {
                $table_number = $order_records->table_number;
            }
        ?>
        <?php if(!empty($table_number)) { ?>
            <br><b><?php echo $this->lang->line('table_no'); ?> #<?php echo $table_number; ?></b>
        <?php } ?>
    </div>
    <?php
        $res_phn_no = '';
        if($order_records->r_phone_number){
            $res_phn_no = $order_records->r_phone_number;
            if($order_records->r_phone_code){
                $res_phn_no = '+'.$order_records->r_phone_code.$order_records->r_phone_number;
            }
        }
    ?>
    <?php if(!empty($restaurant_detail)){ ?>
        <div style="margin-top:5px;">
            <?php echo $restaurant_detail->name.'<br>' .$restaurant_detail->address.'<br>'.$res_phn_no ?>
        </div>
    <?php }else{ ?>
        <div>
            <?php echo $order_records->name ?>
        </div>
    <?php } ?> 
    <hr style="margin: 5px 0;"> 
    <div>
        <div style="text-align:left;width:5%;float:left;padding:5px 0 5px 10px"><b>#</b></div>
        <div style="text-align:left;width:25%;float:left;padding:5px 0 5px 10px"><b><?php echo $this->lang->line('item'); ?></b></div>
        <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 10px"><b><?php echo $this->lang->line('qty'); ?></b></div>
        <div style="text-align:right;width:10%;float:left;padding:5px 0 5px 0"><b><?php echo $this->lang->line('total'); ?></b></div>
    </div>
    <div>
        <?php $item_detail = unserialize($menu_item->item_detail);
        if(!empty($item_detail)){ 
            $Subtotal = 0; $i = 1; $qty_count = 0;
            foreach($item_detail as $key => $value){ 
                $addons_name_list = '';
                $price = 0;
                if($value['is_customize'] == 1){
                    foreach ($value['addons_category_list'] as $k => $val) {
                        $addons_name = '';
                        foreach ($val['addons_list'] as $m => $mn) {
                            $addons_name .= $mn['add_ons_name'].', ';
                            if($value['is_deal'] != 1){
                                $Subtotal = $Subtotal + $mn['add_ons_price'];
                                $price = $price + $mn['add_ons_price'];
                            }
                        }
                        if($value['is_deal'] != 1){
                            $addons_name_list .= '<p style="font-size:12px;margin:2px 0px;"><b>'.$val['addons_category'].'</b>:'.substr($addons_name, 0, -2).' ('.$mn['add_ons_price'].')</p>';
                        }else{
                            $addons_name_list .= '<p style="font-size:12px;margin:2px 0px;">'.substr($addons_name, 0, -2).' ('.$mn['add_ons_price'].')</p>';
                        }
                    }
                }
                $rate = ($price)?$price:$value['rate'];
                $Subtotal = $Subtotal+($rate * $value['qty_no']);
                 ?> 

                <div style="text-align:left;width:5%;float:left;padding:5px 0 5px 10px"> <?php echo $i.'.'; ?></div>
                <div style="text-align:left;width:25%;float:left;padding:5px 0 5px 10px"><?php echo $value['item_name'] .' ('.$value['rate'].')' ?><?php echo $addons_name_list; ?></div>
                <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 10px"><?php echo $value['qty_no'] ?></div>
                <div style="text-align:right;width:10%;float:left;padding:5px 0 5px 0"><?php echo currency_symboldisplay(number_format((float)$value['itemTotal'],2,'.',''),$restaurant_detail->currency_symbol); ?></div>
                <?php  $i++; 
                    $qty_count = $qty_count + $value['qty_no'];
            }  
        } ?> 
    </div>
    <hr class="hr-line">
    <table cellpadding="10" cellspacing="0" width="100%" >
        <tr>
            <td style="text-align:left;width:35%;padding:5px 0 5px 0px"><strong><?php echo $this->lang->line('sub_total'); ?></strong></td>
            <td style="text-align:right;width:20%;float:right;padding:5px 0 5px 0">
                <?php echo currency_symboldisplay(number_format((float)$order_records->subtotal,2,'.',''),$restaurant_detail->currency_symbol); ?>
            </td>
        </tr>
        <?php if($order_records->tax_rate != 0) { ?>
            <tr>
                <td style="text-align:left;width:35%;padding:5px 0 5px 0px"><strong><?php echo $this->lang->line('service_tax'); ?> <?php echo ($order_records->tax_type == 'Percentage')?'('.$order_records->tax_rate.'%)':''; ?></strong></td>
                <?php if($order_records->tax_type == 'Percentage'){
                        $tax_amountdis = ($order_records->subtotal * $order_records->tax_rate) / 100;
                    }else{
                        $tax_amountdis = $order_records->tax_rate; 
                    }
                    $tax_amountdis = round($tax_amountdis,2);
                ?>
                <td style="text-align:right;width:20%;float:right;padding:5px 0 5px 0">+<?php echo currency_symboldisplay(number_format((float)$tax_amountdis,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
            </tr>
        <?php } ?>
        <?php if($order_records->service_fee != 0) { ?>
            <tr>
                <td style="text-align:left;width:35%;padding:5px 0 5px 0px"><strong><?php echo $this->lang->line('service_fee'); ?> <?php echo ($order_records->service_fee_type == 'Percentage') ? '('.$order_records->service_fee.'%)' : '';?></strong></td>
                <?php if($order_records->service_fee_type == 'Percentage'){
                        $service_amount = ($order_records->subtotal * $order_records->service_fee) / 100;
                    } else {
                        $service_amount = $order_records->service_fee; 
                    }
                    $service_amount = round($service_amount,2);
                ?>
            <td style="text-align:right;width:20%;float:right;padding:5px 0 5px 0">+<?php echo currency_symboldisplay(number_format((float)$service_amount,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
            </tr>
        <?php } ?>
        <?php if($order_records->delivery_charge > 0) { ?>
        <tr>
            <td style="text-align:left;width:35%;padding:5px 0 5px 0px"><strong><?php echo $this->lang->line('delivery_charge'); ?></strong></td>
            <td style="text-align:right;width:20%;float:right;padding:5px 0 5px 0">+<?php echo currency_symboldisplay(number_format((float)$order_records->delivery_charge,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
        </tr>
        <?php } ?>
        <?php if($order_records->coupon_discount > 0) { ?>
            <tr>
                <td style="text-align:left;width:35%;padding:5px 0 5px 0px"><strong><?php echo $this->lang->line('coupon_discount').' '.'('.$order_records->coupon_name.')'?></strong></td>
                <td style="text-align:right;width:20%;float:right;padding:5px 0 5px 0">-<?php echo currency_symboldisplay(number_format((float)$order_records->coupon_discount,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
            </tr>
        <?php } ?>
        <?php if($wallet_history) { ?>
            <tr>
                <td style="text-align:left;width:35%;padding:5px 0 5px 0px"><strong><?php echo $this->lang->line('wallet_discount'); ?></strong></td>
                <td style="text-align:right;width:20%;float:right;padding:5px 0 5px 0">-<?php echo currency_symboldisplay(number_format((float)$wallet_history->amount,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
            </tr>
        <?php } ?>
        <?php if(!is_null($order_records->tip_amount) && !empty($order_records->tip_amount)) { ?>
            <tr>
                <td style="text-align:left;width:35%;padding:5px 0 5px 0px"><strong><?php echo $this->lang->line('driver_tip'); ?></strong></td>
                <td style="text-align:right;width:20%;float:right;padding:5px 0 5px 0">+<?php echo currency_symboldisplay(number_format((float)$order_records->tip_amount,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
            </tr>
        <?php } ?>
    </table>
    <hr class="hr-line">
    <table cellpadding="10" cellspacing="0" width="100%">      
          <tr>
            <td style="text-align:left;width:35%;padding:5px 0 5px 0px"><strong><?php echo $this->lang->line('total'); ?></strong></td>
            <td style="text-align:right;width:20%;float:left;padding:5px 0 5px 0"><?php echo currency_symboldisplay(number_format((float)$order_records->total_rate,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
          </tr>
    </table>
    <hr class="hr-line">
</div>
</body>
</html>