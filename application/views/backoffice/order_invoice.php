<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<style type="text/css">
body {
	font-family: Arial
}
.pdf_main {
	background: #fff;
	margin-left: 25px;
	margin-right: 25px;
}
.clearfix {
	clear: both;
}
ul, li {
	list-style: none;
	margin: 0px;
	padding: 0px;
}
.head-main {
	float: left;
	width: 100%;
	margin-bottom: 30px;
}
.pdf_main .logo {
	float: left;
	padding-top: 24px;
	width: 50%;
}
.pdf_main .logo:hover {
	opacity: 1;
}
.pdf_main .head-right {
	float: right;
	width: 50%;
	display: inline-block;
}
.pdf_main .quote-title {
	float: right;
	text-align: right;
	width: 100%;
	padding-bottom: 15px;
}
.pdf_main .col-li {
	float: left;
	display: inline-block;
	text-align: center;
	padding: 0 10px;
	font-size: 12px;
	font-weight: 700;
}
.pdf_main .col-li span {
	font-weight: 400;
}
.pdf_main .col-li .icon {
	display: block;
	padding-bottom: 5px;
}
.pdf_main .main-container {
	float: left;
	width: 100%;
}
.pdf_main .head-main h3 {
	text-align: right;
	margin-bottom: 20px;
	float: right;
}
.pdf_main .head-right li.last, .pdf_main .head-right li:last-child {
	padding-right: 0px;
}
.bill-ship-details {
	margin: 0 -4%;
	clear: both;
}
.pdf_main .colm {
	float: left;
	padding: 0 4%;
	width: 40%;
}
.pdf_main .footer {
	background-color: #0076c0;
	float: left;
	text-align: center;
	display: block;
	padding: 12px 0 0px;
	box-sizing: border-box;
	margin-top: 30px;
	width: 650px;
}
.foot-li {
	color: #fff;
	font-size: 12px;
	font-weight: bold;
}
.foot-li.last {
	border-right: none;
}
.pdf_main table {
	border: 2px #bebcbc solid;
	border-collapse: collapse;
}
.pdf_main table tbody td {
	border: none !important;
}
.pdf_main table th {
	border: none !important;
}
.pdf_main .pdf_table {
	margin-bottom: 50px;
	margin-bottom: 30pt;
}
.pdf_main .pdf_table p {
	color: #000000;
	font-size: 11px;
	font-weight: 400;
	margin-bottom: 10px;
}
.bill-ship-details .colm h3 {
	border-bottom: 2px solid #000000;
	font-size: 16px;
	padding-bottom: 7px;
	margin-bottom: 12px;
}
.bill-ship-details p {
	font-size: 13px;
	color: #000
}
.pdf_main .pdf_table table td[colspan="3"] {
	padding-top: 24px;
}
.pdf_main .pdf_table thead th, .pdf_main .pdf_table tfoot td.grand-total,.div-thead {
	color: #ffffff;
	font-size: 14px;
	background-color: #17161a !important;
}
.div-thead-black{
  color: #ffffff;
  font-size: 14px;
  background-color: #ffffff;
}
.pdf_main .pdf_table {
	margin-bottom: 50px;
	margin-bottom: 30pt;
}
.pdf_main .pdf_table p {
	color: #000000;
	font-size: 11px;
	font-weight: 400;
	margin-bottom: 10px;
}
.signature h4.signature-heading {
	font-size: 15px;
	display: inline-block;
	margin: 0px;
}
.signature .signature-line {
	border-bottom: 1px solid black;
	display: inline-block;
	vertical-align: middle;
	width: 311px;
	margin-left: 8px;
}
.black-theme.pdf_main .pdf_table thead th {
  color: #ffffff;
  font-size: 16px;
  background-color: #000000;
  text-align:left;
}
.black-theme.pdf_main tfoot td.grand-total {
	color: #ffffff;
	background-color: #000000;
}
.black-theme.pdf_main .footer {
	background-color: #000000;
	border-bottom: 3px #000000 solid;
}
.black-theme.pdf_main .footer li {
	border-right: 0;
}
.black-theme.pdf_main table tbody td {
	border: none !important;
}
.black-theme.pdf_main table th {
	border: none !important;
}
.lenth-sec {
	margin-left: 5px;
}
.lenth-sec > label {
	font-weight: 400;
}
.lenth-sec {
	height: 31px;
	vertical-align: top;
}
tr, td, th {
	border: 1px solid #bebcbc;
}
/*.pdf_main {
	margin-left: 38px;
	margin-right: 38px
}*/
.table-style tr td, .table-style tr td{padding-top:4px; padding-bottom:4px;}
.table-style tr .border-line{padding-bottom:7px;}
.segment-main {
  width: 100%;
  border: 2px solid #bebcbc;
  font-size: 12px;
}
.header-rightbar .col-li {
    float: none;
    width: auto;
    margin: 0 9px;
    display: flex;
    display: none;
}
</style>
</head>
<body>
<div class="pdf_main">
    <div class="head-main">
	    <div class="logo"> <img src="./assets/admin/img/logo.png" alt="" width="200" /> </div>
	    <div class="head-right" style="float: right;">
	      <div class="quote-title"><img src="./assets/admin/img/quote-text-img.png" width="122" alt="" /></div>
	      <div class="header-right" style="display:flex;">
	      <div class="col-li" style="float:left;width: 125px"> 
	      	<span class="icon"><img src="./assets/admin/img/note-icon.png" width="50" alt="" /></span>
	        <p><?php echo $order_records->order_delivery; ?> <?php echo $this->lang->line('order') ?># <span><?php echo $order_records->entity_id; ?></span></p>
	        <?php
	        $restaurant_detail = unserialize($menu_item->restaurant_detail);	        
	        if(!empty($restaurant_detail->table_number)){
	        	$table_number = $restaurant_detail->table_number;
	        }
	        else {
	        	$table_number = $order_records->table_number;
	        }
	        ?>
	        <?php if(!empty($table_number)){ ?>
	        	<div><?php echo $this->lang->line('table_no') ?># <span><?php echo $table_number; ?></span></div>
	      	<?php } ?>
	      </div>
	      <div class="col-li" > <span class="icon"><img src="./assets/admin/img/calender.png" width="50"  alt="" /></span>
	        <p><?php echo $this->lang->line('date') ?>: <span><?php $date = $this->common_model->datetimeFormat($this->common_model->getZonebaseDate($order_records->order_date,$user_timezone)); echo $date; ?>
	          </span></p>
	      </div>
	    </div>
	      <div class="col-li" style="width:100%; text-align: left;padding-left: 15px;">
	        <?php
	        if(!empty($order_records->transaction_id)){
	        	?><div><?php echo $this->lang->line('transaction_id') ?># <span><?php echo $order_records->transaction_id; ?></span></div>
	        	<?php
	    	}	        
	    	?>
	      </div>
	    </div>
    </div>
	<div class="main-container">
		<div class="bill-ship-details">
	      <div class="colm" style="float:left">
	        <h3><?php echo $this->lang->line('bill_to'); ?></h3>
	        <?php $user_detail = unserialize($menu_item->user_detail);
	        if(!empty($user_detail)){ ?>
	        <p><?php echo $user_detail['first_name'].' '.$user_detail['last_name'].'<br>' .$user_detail['address'].'<br> '.$user_detail['landmark'].'<br>'.$user_detail['city'].' '.$user_detail['zipcode'] ?></p>
	        	<?php if(!empty($menu_item->user_mobile_number)){
		        	?><p><?php echo "+".$menu_item->user_mobile_number; ?></p><?php
		    	}
	       	}else{ ?>
	       		<p><?php echo $this->lang->line('order_by_restaurant'); ?></p>
	       	<?php } ?>
	      </div>
	      <div class="colm last">
	        <h3><?php echo $this->lang->line('title_admin_restaurant') ?></h3>
	        <?php
        		$res_phn_no = '';
        		if($order_records->r_phone_number){
            	$res_phn_no = $order_records->r_phone_number;
            	if($order_records->r_phone_code){
                $res_phn_no = '+'.$order_records->r_phone_code.$order_records->r_phone_number;
            	}
        		}
    			?>
	        <?php $restaurant_detail = unserialize($menu_item->restaurant_detail);	        
	        if(!empty($restaurant_detail)){ ?>
	        <p><?php echo $restaurant_detail->name.'<br>' .$restaurant_detail->address.'<br> '.$restaurant_detail->landmark.' '.$restaurant_detail->city.' '.$restaurant_detail->zipcode ?></p>
	        <p><?php echo $res_phn_no ?></p>
	  	    <?php }else{ ?>
	  	    <p><?php echo $order_records->name ?></p>
	  	    <?php	} ?>
	      </div>
	    </div>
	    <div class="clearfix" style="clear:both; height:10px"></div>
	</div>
	<div class="segment-main">
		<!-- Header -->
        <div class="div-thead">
          	<div>
          		<div style="text-align:left;width:5%;float:left;padding:5px 0 5px 10px;">#</div>
	            <div style="text-align:left;width:35%;float:left;padding:5px 0 5px 10px;"><?php echo $this->lang->line('item') ?></div>
				<div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0"><?php echo $this->lang->line('discount') ?></div>
	            <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0"><?php echo $this->lang->line('price') ?></div>
	            <div style="text-align:center;width:10%;float:left;padding:5px 0 5px 0"><?php echo $this->lang->line('quantity') ?></div>
	            <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0"><?php echo $this->lang->line('total') ?></div>
            </div>
        </div>
        <!-- body -->
        <div>
        	<?php $item_detail = unserialize($menu_item->item_detail);
        	//echo '<pre>'; print_r($item_detail); exit;
        	 if(!empty($item_detail)){  $i = 1;
        	 	
        		foreach($item_detail as $key => $value){ 
				$addons_name_list = '';
				$Subtotal = 0;
        			if($value['is_customize'] == 1){
						
			            foreach ($value['addons_category_list'] as $k => $val) {
			                $addons_name = '';							
			                foreach ($val['addons_list'] as $m => $mn) {
			                    $addons_name .= $mn['add_ons_name'].', ';
								$Subtotal = $Subtotal + $mn['add_ons_price'];
			                }
			               
			                	$addons_name_list .= '<p><b>'.$val['addons_category'].'</b>:'.substr($addons_name, 0, -2).' ('.$mn['add_ons_price'].')</p>';
			            	
			            }
			    	}
					$actual_price = $value['rate'] + $Subtotal;
			    	$price = ($value['offer_price'])?$value['offer_price']:$value['rate'];
					$discount_price = 	($value['offer_price'])?$value['rate'] * $value['qty_no'] - $value['offer_price'] * $value['qty_no']: 0;
					?>
	            <div style="border-bottom:0px;">
	            	<div style="text-align:left;width:5%;float:left;padding:5px 0 5px 10px"><?php echo $i ?></div>
		            <div style="text-align:left;width:35%;float:left;padding:5px 0 5px 10px"><?php 
		            echo $value['item_name'];
		            echo $addons_name_list; 
		            if(!empty($value['comment'])){
                        ?><div><b><?php echo $this->lang->line('item_comment')?>:</b> <?php echo $value['comment']; ?></div><?php
                    }
		            ?></div>
					 <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0" class="center"><?php echo ($discount_price)?$restaurant_detail->currency_symbol.number_format_unchanged_precision($discount_price,$restaurant_detail->currency_code):'-'; ?></div>
		            <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0" class="center"><?php echo $restaurant_detail->currency_symbol; ?><?php echo ($Subtotal)?number_format_unchanged_precision($actual_price,$restaurant_detail->currency_code):number_format_unchanged_precision($actual_price,$restaurant_detail->currency_code) ?></div>
		            <div style="text-align:center;width:10%;float:left;padding:5px 0 5px 0" class="center"><?php echo $value['qty_no'] ?></div>
		            <div style="text-align:center;width:15%;float:left;padding:5px 0 5px 0" class="center"><?php echo $restaurant_detail->currency_symbol; ?><?php echo ($Subtotal)?number_format_unchanged_precision($actual_price * $value['qty_no'],$restaurant_detail->currency_code):number_format_unchanged_precision($price * $value['qty_no'],$restaurant_detail->currency_code); ?></div>
	           </div>
           <?php $i++;}  } ?>
        </div>
	</div>
	<!-- Footer part for Price -->
    <table border="3" cellpadding="10" cellspacing="0" width="100%" class="table-style">
          <tr>
            <td rowspan="9" style="width: 60%"><?php //echo ($order_records->extra_comment)?$this->lang->line('comment').': '.$order_records->extra_comment:'' ?></td>
            <td class="align-right" style="width: 15%"><strong><?php echo $this->lang->line('sub_total') ?></strong></td>
            <td class="align-left" style="width: 20%"><?php echo $restaurant_detail->currency_symbol; ?><?php echo number_format_unchanged_precision($order_records->subtotal,$restaurant_detail->currency_code) ?></td>
          </tr>
        <?php if($this->session->userdata('AdminUserType')=='MasterAdmin') { ?> 
          <?php if($order_records->tax_rate && intval($order_records->tax_rate)>0) {
	          	if($order_records->tax_type == 'Percentage'){
	                $tax_amount = round(($order_records->subtotal * $order_records->tax_rate) / 100,2); 
	            } else {
	                $tax_amount = $order_records->tax_rate; 
	            }
	            $tax_amount = round($tax_amount,2);
          ?>
          <tr>
            <td class="align-right"><strong><?php echo $this->lang->line('service_tax');?>&nbsp;<?php echo ($order_records->tax_type == 'Percentage') ? '('.$order_records->tax_rate.'%)' : '';?></strong></td>
            <td class="align-left">+<?php echo $restaurant_detail->currency_symbol; ?><?php echo number_format_unchanged_precision($tax_amount,$restaurant_detail->currency_code); ?></td>
          </tr>
          <?php } ?>
          <?php if($order_records->service_fee && intval($order_records->service_fee)>0) {
	          	if($order_records->service_fee_type == 'Percentage'){
	                $service_amount = round(($order_records->subtotal * $order_records->service_fee) / 100,2); 
	            } else {
	                $service_amount = $order_records->service_fee;
	            }
	            $service_amount = round($service_amount,2);
          ?>
          <tr>
            <td class="align-right"><strong><?php echo $this->lang->line('service_fee');?>&nbsp;<?php echo ($order_records->service_fee_type == 'Percentage') ? '('.$order_records->service_fee.'%)' : '';?></strong></td>
            <td class="align-left">+<?php echo $restaurant_detail->currency_symbol; ?><?php echo number_format_unchanged_precision($service_amount,$restaurant_detail->currency_code); ?></td>
          </tr>
          <?php } ?>
          <?php if($order_records->creditcard_fee && intval($order_records->creditcard_fee)>0) {
				if($order_records->creditcard_fee_type == 'Percentage'){
					$creditcard_fee_amount = round(($order_records->subtotal * $order_records->creditcard_fee) / 100,2); 
				} else {
					$creditcard_fee_amount = $order_records->creditcard_fee;
				}
				$creditcard_fee_amount = round($creditcard_fee_amount,2); ?>
				<tr>
					<td class="align-right"><strong><?php echo $this->lang->line('creditcard_fee');?>&nbsp;<?php echo ($order_records->creditcard_fee_type == 'Percentage') ? '('.$order_records->creditcard_fee.'%)' : '';?></strong></td>
					<td class="align-left">+<?php echo $restaurant_detail->currency_symbol; ?><?php echo number_format_unchanged_precision($creditcard_fee_amount,$restaurant_detail->currency_code); ?></td>
				</tr>
          <?php } ?>
          <?php if($order_records->delivery_charge > 0) { ?>
        		<tr>
        			<td class="align-right"><strong><?php echo $this->lang->line('delivery_charge'); ?></strong></td>
            	<td class="align-left">+<?php echo currency_symboldisplay(number_format((float)$order_records->delivery_charge,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
        		</tr>
        	<?php } ?>
 		<?php if($coupon_array && !empty($coupon_array)){
            foreach ($coupon_array as $cp_key => $cp_value) { ?>
            <tr>
                <td class="align-right"><strong><?php echo $this->lang->line('coupon_discount').' '.'('.$cp_value['coupon_name'].')'?></strong></td>
                <td class="align-left">-<?php echo currency_symboldisplay(number_format((float)$cp_value['coupon_discount'],2,'.',''),$currency->currency_symbol); ?>
                </td>
            </tr>    
            <?php }
        } 
        else if($order_records->coupon_discount > 0) { ?>
          <tr>
            <td class="align-right"><strong><?php echo $this->lang->line('coupon_discount').' '.'('.$order_records->coupon_name.')'?></strong></td>
            <td class="align-left">-<?php echo currency_symboldisplay(number_format((float)$order_records->coupon_discount,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
          </tr>
        <?php } ?>
	        <?php if($wallet_history) { ?>
            <tr>
                <td class="align-right"><strong><?php echo $this->lang->line('wallet_discount'); ?></strong></td>
                <td class="align-left">-<?php echo currency_symboldisplay(number_format((float)$wallet_history->amount,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
            </tr>
	        <?php } ?>
	        <?php if(!is_null($order_records->tip_amount) && !empty($order_records->tip_amount)) { ?>
            <tr>
                <td class="align-right"><strong><?php echo $this->lang->line('driver_tip'); ?></strong></td>
                <td class="align-left">+<?php echo currency_symboldisplay(number_format((float)$order_records->tip_amount,2,'.',''),$restaurant_detail->currency_symbol); ?></td>
            </tr>
	        <?php } ?>
          <tr>
            <td class="align-right grand-total"><strong><?php echo $this->lang->line('total')?></strong></td>
            <td class="align-left grand-total"><?php echo $restaurant_detail->currency_symbol; ?><?php echo number_format_unchanged_precision($order_records->total_rate,$restaurant_detail->currency_code); ?></td>
          </tr>
        <?php } ?>
    </table>
    <!-- Footer part for Price end -->
</div>