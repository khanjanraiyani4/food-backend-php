<?php
class Api_branch_admin_model extends CI_Model {
    function __construct()
    {
        parent::__construct();      
    }
    /***************** General API's Function *****************/
    public function getLanguages($current_lang){
        $result = $this->db->select('*')->get_where('languages',array('language_slug'=>$current_lang))->first_row();
        return $result;
    }
    //get order detail
    public function getOrderDetail($branch_admin_id,$count,$page_no = 1,$tabType = NULL, $language_slug,$user_timezone='UTC',$start_date,$end_date,$search_data)
    {
        if(!$language_slug){
            $default_lang = $this->common_model->getdefaultlang();
            $language_slug = $default_lang->language_slug;
        }
        //Code for check the branch admin or not :: Start
        $userarr = $this->db->select('user_type')->where('entity_id',$branch_admin_id)->get('users')->first_row();
        $user_type = $userarr->user_type;
        //Code for check the branch admin or not :: End

        $page_no = ($page_no > 0)?$page_no-1:0;
        $this->db->select("order_master.*,order_detail.*,order_driver_map.driver_id,status.order_status as ostatus,status.time,users.first_name,users.last_name,users.mobile_number,users.phone_code,users.image,driver_traking_map.latitude,driver_traking_map.longitude,restaurant_address.latitude as resLat,restaurant_address.longitude as resLong,restaurant.timings,restaurant.enable_hours,currencies.currency_symbol,currencies.currency_code,currencies.currency_id,table.table_number,tips.amount as driver_tip,tips.tip_percentage,CONCAT('+',COALESCE(restaurant.phone_code,''),COALESCE(restaurant.phone_number,'')) AS 'res_phone_number',restaurant.content_id as res_content_id,tips.tips_transaction_id,tips.refund_status as tips_refund_status,tips.refund_reason as tips_refund_reason,delayed_status_check.order_status as check_order_status,delayed_status_check.time as check_status_time, order_master.refunded_amount");
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_status as status','order_master.entity_id = status.order_id','left');
        $this->db->join('order_status as delayed_status_check','order_master.entity_id = delayed_status_check.order_id AND (delayed_status_check.order_status = "delivered" OR delayed_status_check.order_status = "complete" OR delayed_status_check.order_status = "rejected" OR delayed_status_check.order_status = "cancel")','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('tips','tips.order_id = order_master.entity_id AND tips.amount > 0','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id','left');
        //$this->db->join('driver_traking_map','order_driver_map.driver_id = driver_traking_map.driver_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = order_driver_map.driver_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        if(strtolower($user_type)!='restaurant admin')
        {
            $this->db->join('restaurant_branch_map','restaurant_branch_map.restaurant_content_id = restaurant.content_id','left');    
        }        
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->join('table_master as table','table.entity_id = order_master.table_id','left');
        if(!empty($tabType)) {
            if($tabType == "new") {
                $status_arr = array('placed');
                $this->db->where_in('order_master.order_status',$status_arr);
                $this->db->where('order_master.status','0');
                $this->db->order_by('order_master.created_date','asc');
            } else if($tabType == "inProgress") {
                //$where_string="(order_master.order_status IN('onGoing','preparing','pending','ready') OR (order_master.order_status='accepted' AND order_master.status='1'))";
                $where_string="(order_master.order_status IN('onGoing','pending','ready') OR (order_master.order_status='accepted' AND order_master.status='1'))";
                $this->db->where($where_string);
                $this->db->order_by('order_master.created_date','desc');
            } else if($tabType == "past") {
                $status_arr = array('delivered','cancel','complete','rejected');
                $this->db->where_in('order_master.order_status',$status_arr);
                $this->db->order_by('order_master.created_date','desc');
            } else if($tabType == "Delayed") {
                $this->db->where('order_master.is_delayed',1);
            }
            else if($tabType == "Refunded") {
                $this->db->like('order_master.refund_status','refunded');
                $this->db->order_by('order_master.created_date','desc');
            }
        }
        if(strtolower($user_type)!='restaurant admin')
        {
            $this->db->where('restaurant_branch_map.branch_admin_id',$branch_admin_id);
        }
        else
        {
            $this->db->where('restaurant.restaurant_owner_id',$branch_admin_id);
        }

        if($start_date!='' && $end_date !='') {
            /*$explode_date = explode('-',$date_filter);
            $from_date = $explode_date[0];
            $to_date = $explode_date[1];*/
            $this->db->where('Date(order_master.order_date) >=', date('Y-m-d',strtotime($start_date)));
            $this->db->where('Date(order_master.order_date) <=', date('Y-m-d',strtotime($end_date)));
        }
        if($search_data){
            $where = "(SUBSTRING(order_detail.user_detail, 1) LIKE '%".$this->common_model->escapeString($search_data)."%' OR SUBSTRING(order_detail.item_detail, 1) LIKE '%".$this->common_model->escapeString($search_data)."%')";
            $this->db->where($where);
        }
        /*$this->db->group_by(array("order_master.entity_id", "status.order_status"));*/
        $this->db->group_by('order_master.entity_id');
        
        // Added for pagination
        $this->db->limit($count,$page_no*$count);
        $result =  $this->db->get('order_master')->result();

        $items = array();
        $paymentpaidarr = array('stripe','paypal','applepay');
        $default_currency = get_default_system_currency();
        foreach ($result as $key => $value) {
            //delayed order changes :: start
            if($value->is_delayed == 0 && strtolower($value->order_delivery)!='dinein') {
                $markdelayedflag = 0;
                $compare_time_chk = ($value->check_status_time)?date('Y-m-d H:i:s',strtotime($value->check_status_time)):date('Y-m-d H:i:s');
                $compare_time_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($compare_time_chk,$user_timezone)));

                $scheduledorderclosetime_fordelayed = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_close_time"));
                $order_scheduled_date_fordelayed = ($value->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime_fordelayed,$user_timezone))) : NULL;
                $order_slot_close_time_fordelayed = ($value->slot_close_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime_fordelayed,$user_timezone))) : NULL;

                if($value->scheduled_date && $value->slot_close_time) {
                    $combined_scheduled_date = date('Y-m-d H:i:s', strtotime("$order_scheduled_date_fordelayed $order_slot_close_time_fordelayed"));
                    $scheduleddatetime = new DateTime($combined_scheduled_date);
                    $currentdatetime = new DateTime($compare_time_chk);

                    if($scheduleddatetime <= $currentdatetime) {
                        $markdelayedflag = 1;
                        $order_date_chk = $combined_scheduled_date;
                    }
                } elseif(!($value->scheduled_date && $value->slot_close_time)) {
                    $markdelayedflag = 1;
                    $order_date_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($value->order_date,$user_timezone)));
                }
                if($markdelayedflag == 1) {
                    //difference between current datetime and order datetime
                    $datetime_diff = $this->common_model->dateDifference($order_date_chk , $compare_time_chk);
                    $diff_year = $datetime_diff['diff_year'];
                    $diff_month = $datetime_diff['diff_month'];
                    $diff_day = $datetime_diff['diff_day'];
                    $diff_hr = $datetime_diff['diff_hr'];
                    $diff_min = $datetime_diff['diff_min'];
                    //convert system option auto cancellation minutes to compare
                    $convert_mins = $this->common_model->convertMinutes('check_delayed');
                    $compare_year = $convert_mins['compare_year'];
                    $compare_month = $convert_mins['compare_month'];
                    $compare_day = $convert_mins['compare_day'];
                    $compare_hour = $convert_mins['compare_hour'];
                    $compare_minute = $convert_mins['compare_minute'];

                    if($diff_year > $compare_year || $diff_month > $compare_month || $diff_day > $compare_day || $diff_hr > $compare_hour || $diff_min > $compare_minute) {
                        $this->db->set('is_delayed',1)->where('entity_id',$value->order_id)->update('order_master');
                        $value->is_delayed = 1;
                    }
                }
            }
            //delayed order changes :: end
            if(!empty($default_currency)){
                $currency_symbol = $default_currency;
            }else{
                $currency_symbol = $this->common_model->getCurrencySymbol($value->currency_id);
            }
            
            if(!isset($items[$value->order_id])){
                $items[$value->order_id] = array();
                //$items[$value->order_id]['preparing'] = '';
                $items[$value->order_id]['onGoing'] = '';
                $items[$value->order_id]['delivered'] = '';
            }
            if(isset($items[$value->order_id])) 
            {
                if($value->restaurant_id){
                    $rest_name = $this->db->select('image')->where('entity_id',$value->restaurant_id)->get('restaurant')->first_row();
                    if($rest_name && $rest_name->image){
                        $rest_image = (file_exists(FCPATH.'uploads/'.$rest_name->image)) ? image_url.$rest_name->image : default_img;
                    }else{
                        $rest_image = default_img;
                    }
                }
                $items[$value->order_id]['order_id'] = $value->order_id;
                $items[$value->order_id]['restaurant_id'] = $value->restaurant_id;
                $items[$value->order_id]['order_accepted'] = ($value->status == 1)?1:0;
                $items[$value->order_id]['accept_order_time'] =$this->common_model->getZonebaseTime($value->accept_order_time,$user_timezone);
                $restaurant_detail = unserialize($value->restaurant_detail);
                $items[$value->order_id]['restaurant_name'] = (isset($restaurant_detail->name))?$restaurant_detail->name:'';
                $items[$value->order_id]['restaurant_address'] = (isset($restaurant_detail->address))?$restaurant_detail->address:'';

                $items[$value->order_id]['restaurant_phone_number'] = (isset($value->res_phone_number))?$value->res_phone_number:'';
                $items[$value->order_id]['restaurant_landmark'] = (isset($restaurant_detail->landmark))?$restaurant_detail->landmark:'';
                $items[$value->order_id]['restaurant_image'] = $rest_image;
                $items[$value->order_id]['restaurant_zipcode'] = (isset($restaurant_detail->zipcode))?$restaurant_detail->zipcode:'';
                $items[$value->order_id]['restaurant_city'] = (isset($restaurant_detail->city))?$restaurant_detail->city:'';
                if($value->coupon_name){
                    $discount = array('label'=>$this->lang->line('discount').'('.$value->coupon_name.')','value'=>$value->coupon_discount,'label_key'=>"Discount");
                }else{
                    $discount = '';
                }
                /*wallet money changes start*/
                $wallet_history = $this->getRecordMultipleWhere('wallet_history',array('order_id' => $value->order_id,'debit' => 1));
                $wallet = ($wallet_history)?array('label'=>$this->lang->line('wallet_discount'),'value'=>$wallet_history->amount, 'label_key'=>'Wallet Discount'):'';
                /*wallet money changes end*/
                //Code for tax value :: Start
                $type = ''; $tax_rate = '';
                /*if($restaurant_detail->amount && $restaurant_detail->amount !='' && $restaurant_detail->amount_type && $restaurant_detail->amount_type !='')
                {
                    $type = ($restaurant_detail->amount_type == 'Percentage')?'%':'';
                    $tax_rate = $restaurant_detail->amount;
                }else{*/
                    if($value->tax_rate && $value->tax_rate !='')
                    {
                        $type = ($value->tax_type == 'Percentage')?'%':'';
                        $tax_rate = $value->tax_rate;
                    }
                /*}*/
                //Code for tax value :: End
                $text_amount = 0;
                if($type == '%'){
                    $text_amount = round(($value->subtotal * $tax_rate) / 100, 2);
                }else{
                    $text_amount = $tax_rate; 
                }
                //Begin::Code for service fee
                $s_fee_type = ''; $s_fee = '';
                /*if($restaurant_detail->service_fee !='' && $restaurant_detail->service_fee_type && $restaurant_detail->service_fee_type !=''){
                    $s_fee_type = ($restaurant_detail->service_fee_type == 'Percentage') ? '%' : '';
                    $s_fee = $restaurant_detail->service_fee;
                }else{*/
                    if($value->service_fee && $value->service_fee !=''){
                        $s_fee_type = ($value->service_fee_type == 'Percentage') ? '%' : '';
                        $s_fee = $value->service_fee;
                    }
                /*}*/
                $s_amount = 0;
                if($s_fee_type == '%'){
                    $s_amount = round(($value->subtotal * $s_fee) / 100, 2);
                }else{
                    $s_amount = $s_fee; 
                }
                if($language_slug == 'ar'){
                    $s_percent_text = ($s_fee_type == '%')?' ('.$s_fee_type.$s_fee.')':'';
                }else{
                    $s_percent_text = ($s_fee_type == '%')?' ('.$s_fee.$s_fee_type.')':'';
                }
                //End::Code for service fee
                if($language_slug == 'ar'){
                    $percent_text = ($type == '%')?' ('.$type.$tax_rate.')':'';
                }else{
                    $percent_text = ($type == '%')?' ('.$tax_rate.$type.')':'';
                }

                //Code for creditcard fee :: Start
                if($value->creditcard_fee && $value->creditcard_fee !='')
                {
                    $crd_fee_type = ($value->creditcard_fee_type == 'Percentage') ? '%' : '';
                    $crd_fee = $value->creditcard_fee;
                }
                $crd_amount = 0;
                if($crd_fee_type == '%'){
                    $crd_amount = round(($value->subtotal * $crd_fee) / 100, 2);
                }else{
                    $crd_amount = $crd_fee; 
                }
                if($language_slug == 'ar'){
                    $crd_percent_text = ($crd_fee_type == '%')?' ('.$crd_fee_type.$crd_fee.')':'';
                }else{
                    $crd_percent_text = ($crd_fee_type == '%')?' ('.$crd_fee.$crd_fee_type.')':'';
                }
                //Code for creditcard fee :: End
                $tip_percent_txt = ($value->tip_percentage)?' ('. $value->tip_percentage.'%)':'';

                //Code for multiple coupon :: Start
                $coupon_array = $this->common_model->getCoupon_array($value->order_id);
                $items[$value->order_id]['price'] = array();
                if(!empty($coupon_array))
                {
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total");
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax");
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('service_fee').$s_percent_text,'value'=>$s_amount,'label_key'=>"Service Fee");
                    if($value->transaction_id!='' && $value->transaction_id!=null)
                    {
                        $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('creditcard_fee').$crd_percent_text,'value'=>$crd_amount,'label_key'=>"Credit Card Fee");
                    }
                    if(!empty($coupon_array))
                    {
                        foreach($coupon_array as $cp_key => $cp_value){                        
                            $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('discount').'('.$cp_value['coupon_name'].')','value'=>abs($cp_value['coupon_discount']),'label_key'=>"Discount");
                        }
                    }
                    if($value->delivery_charge)
                    {
                        $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge");
                    }
                    if(!empty($wallet))
                    {
                        $items[$value->order_id]['price'][] = $wallet;
                    }
                    if($value->driver_tip)
                    {
                        $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('driver_tip').$tip_percent_txt,'value'=>$value->driver_tip,'label_key'=>"Driver Tip");
                    }
                    $items[$value->order_id]['price'][] = array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total");
                }//End
                else if($discount)
                {
                    $items[$value->order_id]['price'] = array(
                        array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                        array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax"),
                        array('label'=>$this->lang->line('service_fee').$s_percent_text,'value'=>$s_amount,'label_key'=>"Service Fee"),
                        ($value->transaction_id!='' && $value->transaction_id!=null)?array('label'=>$this->lang->line('creditcard_fee').$crd_percent_text,'value'=>$crd_amount,'label_key'=>"Credit Card Fee"):'',
                        $discount,
                        ($value->delivery_charge)?array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"):'',
                        // array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_amount,'label_key'=>"Coupon Amount"),
                        $wallet,
                        ($value->driver_tip)?array('label'=>$this->lang->line('driver_tip').$tip_percent_txt,'value'=>$value->driver_tip,'label_key'=>"Driver Tip"):'',
                        array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }
                else
                {
                    $items[$value->order_id]['price'] = array(
                    array('label'=>$this->lang->line('sub_total'),'value'=>$value->subtotal,'label_key'=>"Sub Total"),
                    array('label'=>$this->lang->line('service_tax').$percent_text,'value'=>$text_amount,'label_key'=>"Service Tax"),
                    array('label'=>$this->lang->line('service_fee').$s_percent_text,'value'=>$s_amount,'label_key'=>"Service Fee"),
                    ($value->transaction_id!='' && $value->transaction_id!=null)?array('label'=>$this->lang->line('creditcard_fee').$crd_percent_text,'value'=>$crd_amount,'label_key'=>"Credit Card Fee"):'',
                    ($value->delivery_charge)?array('label'=>$this->lang->line('delivery_charge'),'value'=>$value->delivery_charge,'label_key'=>"Delivery Charge"):'',
                    // array('label'=>$this->lang->line('coupon_amount'),'value'=>$value->coupon_amount,'label_key'=>"Coupon Amount"),
                    $wallet,
                    ($value->driver_tip)?array('label'=>$this->lang->line('driver_tip').$tip_percent_txt,'value'=>$value->driver_tip,'label_key'=>"Driver Tip"):'',
                    array('label'=>$this->lang->line('total'),'value'=>$value->total_rate,'label_key'=>"Total"),
                    );
                }
                $timing =  $value->timings;
                if($timing){
                   $timing =  unserialize(html_entity_decode($timing));
                   $newTimingArr = array();
                    $day = date("l");
                    foreach($timing as $keys=>$values) {
                        $day = date("l");
                        if($keys == strtolower($day)){
                            $close = 'close';
                            if($value->enable_hours=='1')
                            {
                                $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?$this->common_model->getZonebaseTime($values['open'],$user_timezone):'';
                                $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?$this->common_model->getZonebaseTime($values['close'],$user_timezone):'';
                                $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                $close = 'close';
                                if(!empty($values['open']) && !empty($values['close']))
                                {
                                    $close = $this->common_model->Checkopenclose($this->common_model->getZonebaseTime($values['open'],$user_timezone),$this->common_model->getZonebaseTime($values['close'],$user_timezone),$user_timezone);
                                }                            
                                $newTimingArr[strtolower($day)]['closing'] = strtolower(str_replace("Closed", "close", $close));
                            }
                            else
                            {
                                $newTimingArr[strtolower($day)]['open'] = '';
                                $newTimingArr[strtolower($day)]['close'] = '';
                                $newTimingArr[strtolower($day)]['off'] = 'close';
                                $newTimingArr[strtolower($day)]['closing'] = $close;
                            }
                        }
                    }
                    $items[$value->order_id]['timings'] = $newTimingArr[strtolower($day)];
                }
                //New Code add as per requested :: Start :: 23-10-2020
                if($value->order_status == "placed" && $value->status!='1')
                {
                    $order_statusval = 'placed';
                }
                else if(($value->order_status == "placed" && $value->status=='1') || $value->order_status == "accepted")
                {
                    $order_statusval = 'accepted';
                }
                else if($value->order_status == "rejected")
                {
                    $order_statusval = 'rejected';
                }
                else
                {
                    $order_statusval = $value->order_status;
                }
                $items[$value->order_id]['order_status_display'] = (strtolower($order_statusval) == "cancel") ? $this->lang->line('cancelled') : ((strtolower($order_statusval) == "complete")?$this->lang->line('completed'):$this->lang->line(strtolower($order_statusval)));
                // $items[$value->order_id]['order_status_display'] = ucfirst($value->order_status);
                if(strtolower($value->order_status)=='ongoing')
                {
                    if($value->order_delivery =='DineIn')
                    {
                       $items[$value->order_id]['order_status_display'] = $this->lang->line('food_is_ready');
                    }
                    else if($value->order_delivery =='PickUp')
                    {
                        $items[$value->order_id]['order_status_display'] = $this->lang->line('order_ready');
                    }
                    else if($value->order_delivery =='Delivery')
                    {
                        $items[$value->order_id]['order_status_display'] = $this->lang->line('on_going');
                    } 
                }
                else if(strtolower($value->order_status) == "ready")
                {
                    if($value->order_delivery =='DineIn')
                    {
                        $items[$value->order_id]['order_status_display'] = $this->lang->line('served');                        
                    }
                    else if($value->order_delivery =='PickUp')
                    {
                        $items[$value->order_id]['order_status_display'] = $this->lang->line('order_ready');
                    }
                }
                //New Code add as per requested :: End :: 23-10-2020
                $items[$value->order_id]['order_status'] = ucfirst($order_statusval);
                $items[$value->order_id]['payment_status'] = $value->payment_status;
                $items[$value->order_id]['cancel_reason'] = ($value->cancel_reason)?$value->cancel_reason:'';
                $items[$value->order_id]['reject_reason'] = ($value->reject_reason)?$value->reject_reason:'';
                $items[$value->order_id]['total'] = $value->total_rate;
                $items[$value->order_id]['extra_comment'] =$value->extra_comment;
                $items[$value->order_id]['delivery_instructions'] =$value->delivery_instructions;
                $items[$value->order_id]['placed'] = $this->common_model->getZonebaseDateMDY($value->order_date,$user_timezone);;
                // if($value->ostatus == 'preparing')
                // {
                //     $items[$value->order_id]['preparing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                // }
                if($value->ostatus == 'onGoing')
                {
                    $items[$value->order_id]['onGoing'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }
                if($value->ostatus == 'delivered')
                {
                    $items[$value->order_id]['delivered'] = ($value->time!="")?$this->common_model->getZonebaseTime($value->time,$user_timezone):'';                    
                }
                $items[$value->order_id]['order_date'] = $this->common_model->getZonebaseDateMDY($value->order_date,$user_timezone);

                $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_open_time"));
                $scheduledorderclosetime = date('Y-m-d H:i:s', strtotime("$value->scheduled_date $value->slot_close_time"));
                $order_scheduled_date = ($value->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime,$user_timezone))) : NULL;
                $order_slot_open_time = ($value->slot_open_time) ? date('g:i A', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime,$user_timezone))) : NULL;
                $order_slot_close_time = ($value->slot_close_time) ? date('g:i A', strtotime($this->common_model->getZonebaseDate($scheduledorderclosetime,$user_timezone))) : NULL;
                $items[$value->order_id]['scheduled_date'] = ($value->scheduled_date) ? $order_scheduled_date : '';
                $items[$value->order_id]['slot_open_time'] = ($value->slot_open_time) ? $order_slot_open_time : '';
                $items[$value->order_id]['slot_close_time'] = ($value->slot_close_time) ? $order_slot_close_time : '';
                $items[$value->order_id]['is_delayed'] = $value->is_delayed;
                $item_detail = unserialize($value->item_detail);
                $value1 = array();
                if(!empty($item_detail)){
                    $data1 = array();                    
                    $count = 0;
                    foreach ($item_detail as $key => $valuee) {
                        $this->db->select('image,is_veg,status');
                        $this->db->where('entity_id',$valuee['item_id']);
                        $data = $this->db->get('restaurant_menu_item')->first_row();
                        $customization = array();
                        $valueee = array();
                        
                        // get order availability count
                        if (!empty($data)) {
                            if($data->status == 0) {
                                $count = $count + 1;
                            }
                        }
                        $data1['image'] = (!empty($data) && $data->image != '')?$data->image:'';
                        $data1['is_veg'] = (!empty($data) && $data->is_veg != '')?$data->is_veg:'';
                        $valueee['image'] = (!empty($data) && $data->image != '')?image_url.$data1['image']:'';
                        $valueee['is_veg'] = (!empty($data) && $data->is_veg != '')?$data1['is_veg']:'';
                        
                        if($valuee['is_customize'] == 1){
                            foreach ($valuee['addons_category_list'] as $k => $val) {
                                $addonscust = array();
                                foreach ($val['addons_list'] as $m => $mn) {
                                    if($valuee['is_deal'] == 1){
                                        $addonscust[] = array(
                                            'add_ons_id'=>($mn['add_ons_id'])?$mn['add_ons_id']:'',
                                            'add_ons_name'=>$mn['add_ons_name'],
                                        );
                                    }else{
                                        $addonscust[] = array(
                                            'add_ons_id'=>($mn['add_ons_id'])?$mn['add_ons_id']:'',
                                            'add_ons_name'=>$mn['add_ons_name'],
                                            'add_ons_price'=>$mn['add_ons_price']
                                        );
                                       
                                    }
                                }
                                $customization[] = array(
                                    'addons_category_id'=>$val['addons_category_id'],
                                    'addons_category'=>$val['addons_category'],
                                    'addons_list'=>$addonscust
                                );
                            }
                        } else {
                            $customization = [];
                        }
                      
                        $itemTotalval = ($valuee['itemTotal']) ? $valuee['itemTotal'] : '';
                        $subTotalval = 0;
                        if($valuee['subTotal'] && $valuee['subTotal'] > 0) {
                            $subTotalval = $valuee['subTotal'];
                        }
                        $valueee['menu_id'] = $valuee['item_id'];
                        $valueee['name'] = $valuee['item_name'];
                        $valueee['quantity'] = $valuee['qty_no'];
                        $valueee['comment'] = $valuee['comment'];
                        $valueee['price'] = ($valuee['rate'])?$valuee['rate']:'';
                        $valueee['is_customize'] = $valuee['is_customize'];
                        $valueee['is_combo_item'] = $valuee['is_combo_item'];
                        $valueee['combo_item_details'] = $valuee['combo_item_details'];
                        $valueee['is_deal'] = $valuee['is_deal'];
                        $valueee['offer_price'] = ($valuee['offer_price'])?$valuee['offer_price']:'';
                        $valueee['subTotal'] = $subTotalval;
                        $valueee['itemTotal'] = $itemTotalval;
                        if(!empty($customization)){
                            $valueee['addons_category_list'] = $customization;
                        }
                        $value1[] =  $valueee;
                    } 
                }
                $user_detail = unserialize($value->user_detail);
                //code add to User detail :: Start :: 13-10-2020
                $users= array();
                if(!empty($user_detail))
                {
                    $user_detailother = $this->getUsers($value->user_id);
                    $phone_codeval = $user_detailother->phone_code;
                    if($phone_codeval!='')
                    {
                        $phone_codeval= str_replace("+","",$phone_codeval);
                        $phone_codeval = '+'.$phone_codeval;                        
                    }
                    $users['first_name'] =  $user_detail['first_name'];
                    $users['last_name'] =  ($user_detail['last_name'])?$user_detail['last_name']:'';
                    $users['mobile_number'] =  ($value->user_mobile_number)?'+'.$value->user_mobile_number:'';
                    $users['email'] =  ($user_detail['email'])?$user_detail['email']:$user_detailother->email;
                    $users['latitude'] =  ($user_detail['latitude'])?$user_detail['latitude']:'';
                    $users['longitude'] =  ($user_detail['longitude'])?$user_detail['longitude']:'';
                    $users['address'] =  ($user_detail['address'])?$user_detail['address']:'';
                    $users['address_label'] =  ($user_detail['address_label'])?$user_detail['address_label']:'';
                    $users['landmark'] =  ($user_detail['landmark'])?$user_detail['landmark']:'';
                    $users['zipcode'] =  ($user_detail['zipcode'])?$user_detail['zipcode']:'';
                    $users['city'] =  ($user_detail['city'])?$user_detail['city']:'';
                    $users['image'] = ($user_detailother->image)?image_url.$user_detailother->image:'';
                    $users['user_id'] = ($value->user_id)?$value->user_id:'';
                    //$items[$value->order_id]['users'] = $users;
                }
                else
                {
                    if($value->user_id !='' && $value->address_id!='')
                    {
                        $userdataArr = $this->getOrderUsers($value->user_id, $value->address_id);
                        if($userdataArr && count($userdataArr))
                        {
                            $users['first_name'] =  $userdataArr['first_name'];
                            $users['last_name'] =  ($userdataArr['last_name'])?$userdataArr['last_name']:'';
                            $users['mobile_number'] =  $userdataArr['mobile_number'];
                            $users['email'] =  $userdataArr['email'];
                            $users['latitude'] =  ($userdataArr['latitude'])?$userdataArr['latitude']:'';
                            $users['longitude'] =  ($userdataArr['longitude'])?$userdataArr['longitude']:'';
                            $users['address'] =  ($userdataArr['address'])?$userdataArr['address']:'';
                            $users['address_label'] =  ($userdataArr['address_label'])?$userdataArr['address_label']:'';
                            $users['landmark'] =  ($userdataArr['landmark'])?$userdataArr['landmark']:'';
                            $users['zipcode'] =  ($userdataArr['zipcode'])?$userdataArr['zipcode']:'';
                            $users['city'] =  ($userdataArr['city'])?$userdataArr['city']:'';
                            $users['image'] = ($userdataArr['image'])?image_url.$userdataArr['image']:'';
                            $users['user_id'] = ($value->user_id)?$value->user_id:'';
                        }
                    }
                }
                $items[$value->order_id]['users'] = $users;
                //code add to User detail :: End :: 13-10-2020
                $items[$value->order_id]['user_latitude'] = (isset($user_detail['latitude']))?$user_detail['latitude']:'';
                $items[$value->order_id]['user_longitude'] = (isset($user_detail['longitude']))?$user_detail['longitude']:'';
                $items[$value->order_id]['resLat'] = $value->resLat;
                $items[$value->order_id]['resLong'] = $value->resLong;
                $items[$value->order_id]['items']  = $value1;
                $items[$value->order_id]['transaction_id']  = $value->transaction_id;
                $items[$value->order_id]['order_type'] = ($value->transaction_id)?'paid':'cod';
                $items[$value->order_id]['available'] = ($count == 0)?'true':'false';
                if($value->first_name && $value->order_delivery == 'Delivery'){
                    $driver['first_name'] =  $value->first_name;
                    $driver['last_name'] =  $value->last_name;
                    $driver_mobile_number = $value->mobile_number;
                    if(!empty($value->phone_code) && !empty($value->mobile_number)){
                        $driver_mobile_number = "+".$value->phone_code.$value->mobile_number;
                    }
                    $driver['mobile_number'] =  $driver_mobile_number;
                    $driver['latitude'] =  $value->latitude;
                    $driver['longitude'] =  $value->longitude;
                    $driver['image'] = ($value->image)?image_url.$value->image:'';
                    $driver['driver_id'] = ($value->driver_id)?$value->driver_id:'';
                    $items[$value->order_id]['driver'] = $driver;
                }
                //$items[$value->order_id]['delivery_flag'] = ($value->order_delivery == 'Delivery')?'delivery':'pickup';
                $items[$value->order_id]['delivery_flag'] = ($value->order_delivery)?strtolower($value->order_delivery):'pickup';
                //thirdparty delivery flag :: start
                if(strtolower($value->order_delivery) == 'delivery'){
                    $chk_res_delivery_method = $this->check_delivery_method_map($value->res_content_id);
                    $items[$value->order_id]['is_thirdparty_delivery_available'] = (!empty($chk_res_delivery_method))?'yes':'no';
                    $items[$value->order_id]['delivery_method_display'] = ($value->delivery_method == "internal_drivers" || ($value->delivery_method == "" && $value->status == 1)) ? $this->lang->line('internal_drivers') : (($value->delivery_method == "doordash" || $value->delivery_method == "relay") ? $this->lang->line('thirdparty_delivery') : '');
                    $items[$value->order_id]['delivery_method'] = ($value->delivery_method == "internal_drivers" || ($value->delivery_method == "" && $value->status == 1)) ? 'internal_drivers' : (($value->delivery_method == "doordash" || $value->delivery_method == "relay") ? 'thirdparty_delivery' : '');
                    if($value->delivery_method == 'doordash'){
                        $doordash_driver_details = $this->common_model->getDoordashDriver($value->order_id);
                        if($doordash_driver_details) {
                            $items[$value->order_id]['thirdparty_driver_details'] = $doordash_driver_details;
                        }
                    }
                }
                //thirdparty delivery flag :: end
                /*$items[$value->order_id]['table_number'] = '';*/
                if(strtolower($value->order_delivery)=='dinein')
                {
                    $table_number = str_replace('Table', '', $value->table_number);
                    $table_number = str_replace('table', "", $table_number);
                    $items[$value->order_id]['table_number'] = trim($table_number);
                }

                //Code for paied status :: Start
                $paidstatus = $value->paid_status;
                if($value->order_delivery =='DineIn')
                {
                    $paidstatus_order = ($value->ostatus == 'delivered' || $value->ostatus == 'cancel' || $value->ostatus == 'rejected' || ($value->ostatus == 'complete' && $value->pstatus=='paid'))?'disabled':'';
                    $paidstatus = (in_array($value->payment_option,$paymentpaidarr) || (!is_null($value->admin_payment_option) && !empty($value->admin_payment_option))) ? 'paid' : 'unpaid';
                    $paidstatus = ($paidstatus_order == 'disabled' || $paidstatus == 'paid')?'paid':'unpaid';
                }
                //Code for paied status :: End

                $payment_option_val = '';
                if($value->payment_option){
                    if(strtolower($value->order_delivery) == 'dinein' && $value->payment_option == 'cod'){
                        if($language_slug == 'en'){
                            $payment_option_val = $this->lang->line('pay_at_counter_en');
                        }
                        if($language_slug == 'fr'){
                            $payment_option_val = $this->lang->line('pay_at_counter_fr');
                        }
                        if($language_slug == 'ar'){
                            $payment_option_val = $this->lang->line('pay_at_counter_ar');
                        }
                    }else{
                        $payment_option_detail = $this->common_model->get_payment_method_detail(strtolower($value->payment_option));
                        if($language_slug == 'en'){
                            $payment_option_val = $payment_option_detail->display_name_en;
                        }
                        if($language_slug == 'fr'){
                            $payment_option_val = $payment_option_detail->display_name_fr;
                        }
                        if($language_slug == 'ar'){
                            $payment_option_val = $payment_option_detail->display_name_ar;
                        }
                    }
                }
                $items[$value->order_id]['payment_option']  = $payment_option_val;

                $items[$value->order_id]['paid_status'] = $paidstatus; //$value->paid_status;
                $items[$value->order_id]['currency_symbol'] = $currency_symbol->currency_symbol;
                $items[$value->order_id]['currency_code'] = $currency_symbol->currency_code;
                $items[$value->order_id]['refund_status'] = $value->refund_status;
                $items[$value->order_id]['refunded_amount'] = ($value->refunded_amount)?$value->refunded_amount:'0';
                $items[$value->order_id]['refund_reason'] = str_replace("<br>", ", ", str_replace("<br/>", ", ", $value->refund_reason));
                if($value->refund_status=='refunded'){
                    $items[$value->order_id]['refund_flag'] = true;
                }else{
                    $items[$value->order_id]['refund_flag'] = false;
                }
                if($value->tips_transaction_id!=''){
                    $items[$value->order_id]['tips_refund_status'] = $value->tips_refund_status;
                    $items[$value->order_id]['tips_refund_reason'] = $value->tips_refund_reason;
                    if($value->tips_refund_status=='refunded'){
                        $items[$value->order_id]['tips_refund_flag'] = true;
                    }
                    else{
                        $items[$value->order_id]['tips_refund_flag'] = false;
                    }
                }
            }
        }
        $finalArray = array();
        foreach ($items as $nm => $va) {
            $finalArray[] = $va;
        }

        $finalArrayrrt['orders'] =  $finalArray;
        //Code for count the order total :: Start
        $this->db->select('order_master.entity_id');
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        $this->db->join('order_status as status','order_master.entity_id = status.order_id','left');
        $this->db->join('order_status as delayed_status_check','order_master.entity_id = delayed_status_check.order_id AND (delayed_status_check.order_status = "delivered" OR delayed_status_check.order_status = "complete" OR delayed_status_check.order_status = "rejected" OR delayed_status_check.order_status = "cancel")','left');
        $this->db->join('order_driver_map','order_master.entity_id = order_driver_map.order_id AND order_driver_map.is_accept = 1','left');
        $this->db->join('tips','tips.order_id = order_master.entity_id AND tips.amount > 0','left');
        $this->db->join('users','order_driver_map.driver_id = users.entity_id','left');
        //$this->db->join('driver_traking_map','order_driver_map.driver_id = driver_traking_map.driver_id','left');
        //driver tracking join last entry changes :: start
        $this->db->join('(select max(traking_id) as max_id, driver_id 
        from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = order_driver_map.driver_id', 'left');
        $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
        //driver tracking join last entry changes :: end
        $this->db->join('restaurant_address','order_master.restaurant_id = restaurant_address.resto_entity_id','left');
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        if(strtolower($user_type)!='restaurant admin')
        {
            $this->db->join('restaurant_branch_map','restaurant_branch_map.restaurant_content_id = restaurant.content_id','left');    
        }
        $this->db->join('currencies','restaurant.currency_id = currencies.currency_id','left');
        $this->db->join('table_master as table','table.entity_id = order_master.table_id','left');
        if(!empty($tabType)) {
            if($tabType == "new") {
                $status_arr = array('placed');
                $this->db->where_in('order_master.order_status',$status_arr);
                $this->db->where('order_master.status','0');
                $this->db->order_by('order_master.entity_id','asc');
            } else if($tabType == "inProgress") {
                // $where_string="(order_master.order_status IN('onGoing','preparing','pending','ready') OR (order_master.order_status='accepted' AND order_master.status='1'))";
                $where_string="(order_master.order_status IN('onGoing','pending','ready') OR (order_master.order_status='accepted' AND order_master.status='1'))";
                $this->db->where($where_string);
                $this->db->order_by('order_master.entity_id','asc');
            } else if($tabType == "past") {
                $status_arr = array('delivered','cancel','complete','rejected');
                $this->db->where_in('order_master.order_status',$status_arr);
                $this->db->order_by('order_master.entity_id','desc');
            } else if($tabType == "Delayed") {
                $this->db->where('order_master.is_delayed',1);
            }
            else if($tabType == "Refunded") {
                $this->db->like('order_master.refund_status','refunded');
            }
        }
        if(strtolower($user_type)!='restaurant admin')
        {
            $this->db->where('restaurant_branch_map.branch_admin_id',$branch_admin_id);
        }
        else
        {
            $this->db->where('restaurant.restaurant_owner_id',$branch_admin_id);
        }
        if($start_date!='' && $end_date !='') {
            /*$explode_date = explode('-',$date_filter);
            $from_date = $explode_date[0];
            $to_date = $explode_date[1];*/
            $this->db->where('Date(order_master.order_date) >=', date('Y-m-d',strtotime($start_date)));
            $this->db->where('Date(order_master.order_date) <=', date('Y-m-d',strtotime($end_date)));
        }
        if($search_data){
            $where = "(SUBSTRING(order_detail.user_detail, 1) LIKE '%".$this->common_model->escapeString($search_data)."%' OR SUBSTRING(order_detail.item_detail, 1) LIKE '%".$this->common_model->escapeString($search_data)."%')";
            $this->db->where($where);
        }
        /*$this->db->group_by(array("order_master.entity_id", "status.order_status"));*/
        $this->db->group_by('order_master.entity_id');
        $finalArrayrrt['count'] =  $this->db->get('order_master')->num_rows();
        
        //Code for count the order total :: End
        return $finalArrayrrt;
    }
    /*
        Author: Chirag Thoriya
        Update: $this->db->where('status','1');
        Description: updated query for fetching driver which has status 1
        Updated on: 21/12/2020
    */
    public function getDrivers($restaurant_id,$user_id,$user_type='')
    {
        //get res content id
        $this->db->select('restaurant.entity_id,restaurant.content_id');
        $this->db->where('entity_id',$restaurant_id);
        $restaurant_content_id = $this->db->get('restaurant')->first_row();

        $this->db->select('users.entity_id,users.first_name,users.last_name');
        $this->db->where('user_type','Driver');
        $this->db->where('users.status',1);  
        $this->db->where('users.availability_status',1);
        if($restaurant_content_id && !empty($restaurant_content_id))
        {
             $this->db->where('restaurant.content_id', $restaurant_content_id->content_id);
        }

        $this->db->join('restaurant_driver_map as restaurant_map','users.entity_id = restaurant_map.driver_id','left');
        $this->db->join('restaurant','restaurant_map.restaurant_content_id = restaurant.content_id','left');
        if($user_type == 'Restaurant Admin')
        {
            $this->db->where('restaurant.restaurant_owner_id', $user_id);
        }
        else if($user_typ == 'Branch Admin')
        {
            $this->db->where('restaurant.branch_admin_id',$user_id);
        }

        $this->db->group_by('restaurant_map.driver_id');        
        $result = $this->db->get('users')->result();
        return $result;
    }
    //New function to fetch user detail for order detail :: Start :: 13-10-2020
    public function getUsers($entity_id){
        $this->db->select('image, mobile_number, email,phone_code');
        $this->db->where('entity_id',$entity_id);  
        return $this->db->get('users')->first_row();
    }
    public function getOrderUsers($user_id,$address_id)
    {
        $this->db->select('users.first_name, users.last_name, users_add.address, users_add.landmark, users_add.zipcode,
            users_add.city, users_add.address_label, users.image, users.mobile_number,users.email, users_add.latitude,users_add.longitude');
        $this->db->join('user_address as users_add','users_add.user_entity_id = users.entity_id','left');
        $this->db->where('users.entity_id',$user_id);
        $this->db->where('users_add.entity_id',$address_id);
        return $this->db->get('users')->row_array(); 
    }
    public function getOrdercurrent_status($oder_id){
        $this->db->select('order_status,order_delivery');
        $this->db->where('entity_id',$oder_id);  
        return $this->db->get('order_master')->first_row();
    }
    //New function to fetch user detail for order detail :: End :: 13-10-2020
    public function getOrderDetails($order_id){ 
        $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians(user_address.latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians(user_address.longitude) ) + sin ( radians(user_address.latitude) ) * sin( radians( address.latitude )))) as distance,order_master.delivery_charge");
        $this->db->join('restaurant','order_master.restaurant_id = restaurant.entity_id','left');
        $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
        $this->db->join('user_address','order_master.address_id = user_address.entity_id','left');
        $this->db->where('order_master.entity_id',$order_id);
        return $distance = $this->db->get('order_master')->result();
    }
     // updating status and send request to driver
    public function UpdatedStatus($tblname,$restaurant_id,$order_id){
        $resp_arr = array('is_available' => 'yes');
        $this->db->set('order_status', 'accepted')->where('entity_id', $order_id)->update('order_master');
        $this->db->set('status',1)->where('entity_id',$order_id)->update('order_master');
        $this->db->set('accept_order_time',date("Y-m-d H:i:s"))->where('entity_id',$order_id)->update('order_master');

        $order_details_chk = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
        if($order_details_chk->user_id>0){
            //send notification to user
            $this->db->select('users.entity_id,users.device_id,order_delivery,users.language_slug,users.notification');
            $this->db->join('users','order_master.user_id = users.entity_id','left');
            $this->db->where('order_master.entity_id',$order_id);
            $this->db->where('users.status',1);
            $device = $this->db->get('order_master')->first_row();
        
            if($device->device_id && $device->notification == 1){  
                //get langauge
                $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                $this->lang->load('messages_lang', $languages->language_directory);
                #prep the bundle
                $fields = array();            
                $message = sprintf($this->lang->line('push_order_accept'),$order_id);
                $fields['to'] = $device->device_id; // only one user to send push notification
                $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                $fields['notification']['title'] = $this->lang->line('customer_app_name');
                $fields['data'] = array ('screenType'=>'order');
               
                $headers = array (
                    'Authorization: key=' . FCM_KEY,
                    'Content-Type: application/json'
                );
                #Send Reponse To FireBase Server    
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                curl_setopt( $ch,CURLOPT_POST, true );
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                $result = curl_exec($ch);
                curl_close($ch);  
            }
        } else {
            $this->db->select('order_delivery,order_master.paid_status');
            $this->db->where('order_master.entity_id',$order_id);
            $device = $this->db->get('order_master')->first_row();
        }
        //send notification to driver
        $this->db->select('restaurant.entity_id,restaurant.content_id');
        $this->db->where('entity_id',$restaurant_id);
        $restaurant_content_id = $this->db->get('restaurant')->first_row();
        if($device->order_delivery == 'Delivery')
        {
            /* drivers assigned to multiple restaurant - start */
            $this->db->select('driver_id');
            $this->db->where('restaurant_content_id',$restaurant_content_id->content_id);
            $driver = $this->db->get('restaurant_driver_map')->result_array();
            /* drivers assigned to multiple restaurant - end */
                
            $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
            //$this->db->join('users','driver_traking_map.driver_id = users.entity_id','left');
            //driver tracking join last entry changes :: start
            $this->db->join('(select max(traking_id) as max_id, driver_id 
            from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
            $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
            //driver tracking join last entry changes :: end
            $this->db->where('users.user_type','Driver');
            $this->db->where('driver_traking_map.latitude!=','');
            $this->db->where('driver_traking_map.longitude!=','');
            $this->db->where('users.status',1);
            $this->db->where('users.availability_status',1);
            $this->db->where('users.active',1);
            if(!empty($driver)){
                $this->db->where_in('driver_traking_map.driver_id',array_column($driver, 'driver_id'));
            }
            $this->db->group_by('driver_traking_map.driver_id');
            $this->db->order_by('driver_traking_map.created_date','desc');
            $detail = $this->db->get('users')->result();
            
            $flag = false;
            if(!empty($detail)){
                if($order_details_chk->user_id>0){
                    $this->db->select('u_address.latitude, u_address.longitude');
                    $this->db->join('user_address as u_address','order_master.address_id = u_address.entity_id','left');
                    $this->db->where('order_master.entity_id',$order_id);
                    $user_details = $this->db->get('order_master')->first_row();
                    $user_lat = $user_details->latitude;
                    $user_long = $user_details->longitude;
                } else {
                    $this->db->select('user_detail');
                    $this->db->where('order_id',$order_id);
                    $user_details = $this->db->get('order_detail')->first_row();
                    $user_lat_long = unserialize($user_details->user_detail);
                    $user_lat = $user_lat_long['latitude'];
                    $user_long = $user_lat_long['longitude'];
                }
                /*Begin::Finding Distance between restaurant to user*/
                $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($user_lat) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($user_long) ) + sin ( radians($user_lat) ) * sin( radians( address.latitude )))) as distance");
                $this->db->join('restaurant_address as address','order_master.restaurant_id = address.resto_entity_id','left');
                $this->db->where('order_master.entity_id',$order_id);
                $user_to_restaurant_distance = $this->db->get('order_master')->first_row();
                /*End::Finding Distance between restaurant to user*/
                foreach ($detail as $key => $value) {
                    $longitude = $value->longitude;
                    $latitude = $value->latitude;
                    $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
                    $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
                    $this->db->where('restaurant.entity_id',$restaurant_id);
                    $this->db->having('distance <',NEAR_KM);
                    $result = $this->db->get('restaurant')->result();
                    if(!empty($result)){
                        if($value->device_id){
                            //get langauge
                            $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
                            $this->lang->load('messages_lang', $languages->language_directory); 
                            $flag = true;   
                            $array = array(
                                'order_id'=>$order_id,
                                'driver_id'=>$value->driver_id,
                                'date'=>date('Y-m-d H:i:s'),
                                'distance'=>$user_to_restaurant_distance->distance
                            );
                            $id = $this->common_model->addData('order_driver_map',$array);
                            #prep the bundle
                            $fields = array();            
                            $message = sprintf($this->lang->line('push_new_order'),$order_id);
                            $fields['to'] = $value->device_id; // only one user to send push notification
                            $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                            $fields['notification']['title'] = $this->lang->line('driver_app_name');
                            $fields['data'] = array ('screenType'=>'order');
                           
                            $headers = array (
                                'Authorization: key=' . FCM_KEY,
                                'Content-Type: application/json'
                            );
                            #Send Reponse To FireBase Server    
                            $ch = curl_init();
                            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                            curl_setopt( $ch,CURLOPT_POST, true );
                            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                            $result = curl_exec($ch);
                            curl_close($ch);            
                        } 
                    }
                }
            }
        }
        return $resp_arr;
    }
    // Login
    public function getLogin($password, $email = NULL, $phone = NULL, $phone_code = NULL)
    {        
        $enc_pass  = md5(SALT.$password);
        $this->db->select('users.entity_id,users.first_name,users.last_name,users.status,users.active,users.mobile_number,users.phone_code,users.image,users.notification,users.email');
        if($phone){
            $this->db->where('mobile_number',$phone);
        }
        if($email){
            $this->db->where('email',$email);
        }
        if($phone_code)
        {
            if(strstr($phone_code,"+"))
            {
                $phone_codeval=str_replace("+", "", $phone_code);
                $this->db->where_in('phone_code',array($phone_code,$phone_codeval));
            }
            else
            {
                $phone_codeval="+".$phone_code;
                //$this->db->where('phone_code',$phone_code);
                $this->db->where_in('phone_code',array($phone_code,$phone_codeval));
            }            
        }
        $this->db->where('password',$enc_pass);
        //$this->db->where('user_type','Branch Admin');
        $this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin')");
        return $this->db->get('users')->first_row();
    }
    // check token for every API Call
    /*
        Author: Chirag Thoriya
        Update: 'status' => 1
        Description: added parameter in query to validate user must be active/status 1
        Updated on: 21/12/2020
    */
    public function checkToken($userid)
    {
        $this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin')");
        $this->db->where("entity_id", $userid);
        $this->db->where("status",1);
        return $this->db->get('users')->first_row();
    }
    //get order details
    public function orderDetails($entity_id){
        $this->db->where('order_master.entity_id',$entity_id);
        $this->db->join('order_detail','order_master.entity_id = order_detail.order_id','left');
        return $this->db->get('order_master')->result();
    }
    // get config
    public function getSystemOptoin($OptionSlug)
    {        
        $this->db->select('OptionValue');                
        $this->db->where('OptionSlug',$OptionSlug);        
        return $this->db->get('system_option')->first_row();
    }
    //check email for user edit
    public function checkEmailExists($table,$fieldName,$where,$UserID)
    {
        $this->db->where($fieldName,$where);
        $this->db->where('entity_id !=',$UserID);
        $roles = array('User','Driver','Agent');
        $this->db->where_not_in('user_type',$roles);
        return $this->db->get($table)->num_rows();
    }
    //get record with multiple where
    public function getRecordMultipleWhere($table,$whereArray)
    {
        $this->db->where($whereArray);
        return $this->db->get($table)->first_row();
    }
    // function to get users total wallet money
    public function getUsersWalletMoney($user_id){
        $this->db->select('users.wallet');
        $this->db->where('users.entity_id',$user_id);
        return $this->db->get('users')->first_row();  
    }
    // delete wallet history by order_id
    public function deletewallethistory($order_id){
        $this->db->where('order_id',$order_id);
        $this->db->update('wallet_history',array('is_deleted'=> 1));
        return $this->db->affected_rows();
    }
    //update record with multiple where
    public function updateMultipleWhere($table,$whereArray,$data)
    {
        $this->db->where($whereArray);
        $this->db->update($table,$data);
    }
    public function getRecord($table,$fieldName,$where)
    {
        $this->db->where($fieldName,$where);
        return $this->db->get($table)->first_row();
    }
    // get systemOption
    public function getSystemOption($OptionSlug){
        $this->db->select('OptionValue');
        $this->db->where('system_option.OptionSlug',$OptionSlug);
        return $this->db->get('system_option')->first_row();
    }
    public function checkOrderAssigned($order_id){
        $this->db->select("CONCAT(COALESCE(users.first_name,''),' ',COALESCE(users.last_name,'')) AS 'driver_name'");
        $this->db->join('users','order_driver_map.driver_id = users.entity_id','left');
        $this->db->where('order_driver_map.is_accept',1);
        $this->db->where('order_driver_map.order_id',$order_id);
        return $this->db->get('order_driver_map')->first_row();
    }
    public function checkAdminRecord($email)
    {
        $this->db->where('email',$email);
        $this->db->where('status',1);
        $this->db->where("(user_type='Restaurant Admin' OR user_type='Branch Admin')");
        return $this->db->get('users')->first_row();
    }
    // method to get details by id
    public function getorderPrintDetail($entity_id)
    {
        $this->db->select('order.*,res.name,res.phone_code as r_phone_code,res.phone_number as r_phone_number,res.is_printer_available,res.printer_paper_width,res.printer_paper_height,address.address,address.landmark,address.city,address.zipcode,u.first_name,u.last_name,uaddress.address as uaddress,uaddress.landmark as ulandmark,uaddress.city as ucity,uaddress.zipcode as uzipcode,tb.table_number,tips.amount as tip_amount');
        $this->db->join('restaurant as res','order.restaurant_id = res.entity_id','left');
        $this->db->join('restaurant_address as address','res.entity_id = address.resto_entity_id','left');
        $this->db->join('users as u','order.user_id = u.entity_id','left');
        $this->db->join('user_address as uaddress','u.entity_id = uaddress.user_entity_id','left');
        $this->db->join('table_master as tb','order.table_id = tb.entity_id','left');
        $this->db->join('tips','order.entity_id = tips.order_id AND tips.amount > 0','left');
        return  $this->db->get_where('order_master as order',array('order.entity_id'=>$entity_id))->first_row();
    }
    //get invoice data
    public function getInvoiceMenuItem($entity_id){
        $this->db->where('order_id',$entity_id);
        return $this->db->get('order_detail')->first_row();
    }
    //Delete order dirver relation before assign
    public function DelOrderbeforAssign($order_id,$driver_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->where('is_accept !=',1);
        $this->db->where('is_accept !=',2);
        //$this->db->where('driver_id !=',$driver_id);
        $this->db->delete('order_driver_map'); 
    }

    public function getAssignDrvier($order_id)
    {
        $this->db->select('driver_id');        
        $this->db->where('order_id',$order_id);
        $this->db->where('is_accept','1');        
        $result = $this->db->get('order_driver_map')->first_row();
        return $result;
    }
    public function check_delivery_method_map($restaurant_content_id){
        return $this->db->get_where('restaurant_delivery_method_map',array('restaurant_content_id'=>$restaurant_content_id))->result();
    }
    public function getResContentId($restaurant_id){
        $this->db->select('content_id');
        $this->db->where('entity_id',$restaurant_id);
        $result = $this->db->get('restaurant')->first_row();
        return $result->content_id;
    }
    public function UpdatedStatusForDeliveryOrders($tblname,$restaurant_id,$order_id,$choose_delivery_method,$user_timezone){
        $resp_arr = array('is_available' => 'no','error'=>'', 'delivery_method'=>'');
        $delivery_method_flag = 'no';

        $order_details_chk = $this->common_model->getSingleRow('order_master','entity_id',$order_id);
        if($order_details_chk->user_id>0){
            //send notification to user
            $this->db->select('users.entity_id,users.device_id,order_delivery,users.language_slug,order_master.payment_option,order_master.scheduled_date,order_master.slot_open_time');
            $this->db->join('users','order_master.user_id = users.entity_id','left');
            $this->db->where('order_master.entity_id',$order_id);
            $this->db->where('users.status',1);
            $device = $this->db->get('order_master')->first_row();
        } else {
            $this->db->select('order_delivery,order_master.paid_status,order_master.payment_option,order_master.scheduled_date,order_master.slot_open_time');
            $this->db->where('order_master.entity_id',$order_id);
            $device = $this->db->get('order_master')->first_row();
        }
        if($device->order_delivery == 'Delivery' && $choose_delivery_method == 'internal_drivers'){
            $resp_arr = array('is_available' => 'yes', 'delivery_method'=>'internal_drivers');
            $delivery_method_flag = 'yes';

            $this->db->set('delivery_method','internal_drivers')->where('entity_id',$order_id)->update('order_master');

            $scheduledorderopentime = date('Y-m-d H:i:s', strtotime("$device->scheduled_date $device->slot_open_time"));
            $order_scheduled_date = ($device->scheduled_date) ? date('Y-m-d', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime,$user_timezone))) : NULL;
            $order_slot_open_time = ($device->slot_open_time) ? date('H:i:s', strtotime($this->common_model->getZonebaseDate($scheduledorderopentime,$user_timezone))) : NULL;
            $current_time_chk = date('Y-m-d H:i:s');
            $current_time_chk = date('Y-m-d H:i:s', strtotime($this->common_model->getZonebaseDate($current_time_chk,$user_timezone)));
            
            if((empty($order_scheduled_date) && empty($order_slot_open_time)) || ($order_scheduled_date && $order_slot_open_time && date('Y-m-d',strtotime($order_scheduled_date)) == date('Y-m-d',strtotime($current_time_chk)))) {
                $combined_scheduled_date = date('Y-m-d H:i:s', strtotime("$order_scheduled_date $order_slot_open_time"));

                $scheduleddatetime = new DateTime($combined_scheduled_date);
                $currentdatetime = new DateTime($current_time_chk);

                if((empty($device->scheduled_date) && empty($device->slot_open_time)) || $scheduleddatetime <= $currentdatetime) {
                    //send notification to driver
                    $this->db->select('restaurant.entity_id,restaurant.content_id');
                    $this->db->where('entity_id',$restaurant_id);
                    $restaurant_content_id = $this->db->get('restaurant')->first_row();
                
                    /* drivers assigned to multiple restaurant - start */
                    $this->db->select('driver_id');
                    $this->db->where('restaurant_content_id',$restaurant_content_id->content_id);
                    $driver = $this->db->get('restaurant_driver_map')->result_array();
                    /* drivers assigned to multiple restaurant - end */
                        
                    $this->db->select('driver_traking_map.latitude,driver_traking_map.longitude,driver_traking_map.driver_id,users.device_id,users.language_slug');
                    //driver tracking join last entry changes :: start
                    $this->db->join('(select max(traking_id) as max_id, driver_id 
                    from driver_traking_map group by driver_id) as tracking1', 'tracking1.driver_id = users.entity_id', 'left');
                    $this->db->join('driver_traking_map', 'driver_traking_map.traking_id = tracking1.max_id', 'left');
                    //driver tracking join last entry changes :: end
                    $this->db->where('users.user_type','Driver');
                    $this->db->where('users.status',1);
                    $this->db->where('users.availability_status',1);
                    $this->db->where('users.active',1);
                    if(!empty($driver)){
                        $this->db->where_in('driver_traking_map.driver_id',array_column($driver, 'driver_id'));
                    }
                    $this->db->group_by('driver_traking_map.driver_id');
                    $this->db->order_by('driver_traking_map.created_date','desc');
                    $detail = $this->db->get('users')->result();
                    
                    $flag = false;
                    if(!empty($detail)){
                        if($order_details_chk->user_id>0){
                            $this->db->select('u_address.latitude, u_address.longitude');
                            $this->db->join('user_address as u_address','order_master.address_id = u_address.entity_id','left');
                            $this->db->where('order_master.entity_id',$order_id);
                            $user_details = $this->db->get('order_master')->first_row();
                            $user_lat = $user_details->latitude;
                            $user_long = $user_details->longitude;
                        } else {
                            $this->db->select('user_detail');
                            $this->db->where('order_id',$order_id);
                            $user_details = $this->db->get('order_detail')->first_row();
                            $user_lat_long = unserialize($user_details->user_detail);
                            $user_lat = $user_lat_long['latitude'];
                            $user_long = $user_lat_long['longitude'];
                        }
                        /*Begin::Finding Distance between restaurant to user*/
                        $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($user_lat) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($user_long) ) + sin ( radians($user_lat) ) * sin( radians( address.latitude )))) as distance");
                        $this->db->join('restaurant_address as address','order_master.restaurant_id = address.resto_entity_id','left');
                        $this->db->where('order_master.entity_id',$order_id);
                        $user_to_restaurant_distance = $this->db->get('order_master')->first_row();
                        /*End::Finding Distance between restaurant to user*/
                        foreach ($detail as $key => $value) {
                            $longitude = $value->longitude;
                            $latitude = $value->latitude;
                            $this->db->select("(".DISTANCE_CALCVAL." * acos ( cos ( radians($latitude) ) * cos( radians(address.latitude ) ) * cos( radians( address.longitude ) - radians($longitude) ) + sin ( radians($latitude) ) * sin( radians( address.latitude )))) as distance");
                            $this->db->join('restaurant_address as address','restaurant.entity_id = address.resto_entity_id','left');
                            $this->db->where('restaurant.entity_id',$restaurant_id);
                            $this->db->having('distance <',NEAR_KM);
                            $result = $this->db->get('restaurant')->result();
                            if(!empty($result)){
                                if($value->device_id){
                                    //get langauge
                                    $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$value->language_slug))->first_row();
                                    $this->lang->load('messages_lang', $languages->language_directory); 
                                    $flag = true;   
                                    $array = array(
                                        'order_id'=>$order_id,
                                        'driver_id'=>$value->driver_id,
                                        'date'=>date('Y-m-d H:i:s'),
                                        'distance'=>$user_to_restaurant_distance->distance
                                    );
                                    $id = $this->common_model->addData('order_driver_map',$array);
                                    #prep the bundle
                                    $fields = array();            
                                    $message = sprintf($this->lang->line('push_new_order'),$order_id);
                                    $fields['to'] = $value->device_id; // only one user to send push notification
                                    $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                                    $fields['notification']['title'] = $this->lang->line('driver_app_name');
                                    $fields['data'] = array ('screenType'=>'order');
                                   
                                    $headers = array (
                                        'Authorization: key=' . FCM_KEY,
                                        'Content-Type: application/json'
                                    );
                                    #Send Reponse To FireBase Server    
                                    $ch = curl_init();
                                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                                    curl_setopt( $ch,CURLOPT_POST, true );
                                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                                    $result = curl_exec($ch);
                                    curl_close($ch);            
                                } 
                            }
                        }
                    }
                }
            }
        } else if($device->order_delivery == 'Delivery' && $choose_delivery_method == 'thirdparty_delivery'){
            $res_content_id = $this->getResContentId($restaurant_id);
            $delivery_method_arr = $this->check_delivery_method_map($res_content_id);
            $delivery_method_ids = array_column($delivery_method_arr, 'delivery_method_id');
            $delivery_method_slug = $this->common_model->getDeliveryMethodName($delivery_method_ids);

            $is_scheduled = 0;
            if($device->scheduled_date && $device->slot_open_time) {
                $is_scheduled = 1;
            }

            $relay_check = (in_array('relay', $delivery_method_slug)) ? $this->common_model->checkDeliveryAvailableInRelay($order_id, $is_scheduled) : $resp_arr;
            if($relay_check['is_available'] == 'no' && $device->payment_option != 'cod'){
                $door_dash_check = (in_array('doordash', $delivery_method_slug)) ? $this->common_model->checkDeliveryAvailableInDoorDash($order_id, $is_scheduled) : $resp_arr;
                if($door_dash_check['is_available'] == 'yes'){
                    $resp_arr = array('is_available' => 'yes', 'delivery_method'=>'doordash');
                    $delivery_method_flag = 'yes';
                } else {
                    $resp_arr = array('is_available' => 'no', 'delivery_method'=>'doordash', 'error'=>$door_dash_check['error']);
                }
            } else if($relay_check['is_available'] == 'no'){
                $resp_arr = array('is_available' => 'no', 'delivery_method'=>'relay', 'error'=>'');
            } else {
                $resp_arr = array('is_available' => 'yes', 'delivery_method'=>'relay');
                $delivery_method_flag = 'yes';
            }
        }
        if($delivery_method_flag == 'yes'){
            $this->db->set('order_status', 'accepted')->where('entity_id', $order_id)->update('order_master');
            $this->db->set('status',1)->where('entity_id',$order_id)->update('order_master');
            $this->db->set('accept_order_time',date("Y-m-d H:i:s"))->where('entity_id',$order_id)->update('order_master');
            if($order_details_chk->user_id>0){
                //send notification to user
                if($device->device_id){  
                    //get langauge
                    $languages = $this->db->select('*')->get_where('languages',array('language_slug'=>$device->language_slug))->first_row();
                    $this->lang->load('messages_lang', $languages->language_directory);
                    #prep the bundle
                    $fields = array();            
                    $message = sprintf($this->lang->line('push_order_accept'),$order_id);
                    $fields['to'] = $device->device_id; // only one user to send push notification
                    $fields['notification'] = array ('body'  => $message,'sound'=>'default');
                    $fields['notification']['title'] = $this->lang->line('customer_app_name');
                    $fields['data'] = array ('screenType'=>'order');
                   
                    $headers = array (
                        'Authorization: key=' . FCM_KEY,
                        'Content-Type: application/json'
                    );
                    #Send Reponse To FireBase Server    
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
                    $result = curl_exec($ch);
                    curl_close($ch);  
                }
            }
        }
        return $resp_arr;
    }
    //for edit order
    public function get_delivery_pickup_order($order_id) {
        $this->db->select('om.entity_id as order_id, om.user_id, om.agent_id, om.restaurant_id, om.coupon_id, om.table_id, om.total_rate, om.subtotal, om.tax_rate, om.tax_type, om.coupon_discount, om.coupon_name, om.coupon_amount, om.coupon_type, om.extra_comment, od.item_detail, od.user_detail, res.name as restaurant_name, CONCAT(us.first_name, " ",us.last_name) as user_name, us.device_id, us.notification, us.language_slug, om.service_fee_type, om.service_fee, om.order_status, om.paid_status, tips.amount as tip_amount, om.used_earning, om.delivery_charge, om.creditcard_fee_type, om.creditcard_fee, om.refunded_amount, om.refund_reason, om.payment_option, om.transaction_id, od.user_mobile_number, od.user_name as order_user_name, us.phone_code, us.mobile_number, us.email');        
        $this->db->join('order_detail as od','od.order_id = om.entity_id');
        $this->db->join('restaurant as res','om.restaurant_id = res.entity_id');
        $this->db->join('tips as tips','tips.order_id = om.entity_id AND tips.amount > 0','left');
        $this->db->join('users as us','om.user_id = us.entity_id','left');
        $this->db->where('om.entity_id',$order_id);
        $detail = $this->db->get('order_master as om')->first_row(); 
        $order_detailarr = array();
        if($detail && !empty($detail)) {
            $order_detailarr =  array(
                'entity_id' => $detail->order_id,
                'order_id' => $detail->order_id,
                'user_id' => $detail->user_id,
                'agent_id' => ($detail->agent_id)?$detail->agent_id:NULL,
                'device_id' => $detail->device_id,
                'notification' => $detail->notification,
                'language_slug' => $detail->language_slug,
                'restaurant_id' => $detail->restaurant_id,
                'coupon_id' => $detail->coupon_id,
                'total_rate' => $detail->total_rate,
                'subtotal' => $detail->subtotal,
                'tax_rate' => $detail->tax_rate,
                'tax_type' => $detail->tax_type,
                'service_fee_type' => $detail->service_fee_type,
                'service_fee' => $detail->service_fee,
                'creditcard_fee_type' => $detail->creditcard_fee_type,
                'creditcard_fee' => $detail->creditcard_fee,
                'coupon_discount' => $detail->coupon_discount,
                'coupon_name' => $detail->coupon_name,
                'coupon_amount' => $detail->coupon_amount,
                'coupon_type' => $detail->coupon_type,
                'extra_comment'=> $detail->extra_comment,
                'restaurant_name' => $detail->restaurant_name,
                'user_name' => $detail->user_name,
                'order_status' => $detail->order_status,
                'paid_status' => $detail->paid_status,
                'tip_amount' => $detail->tip_amount,
                'delivery_charge' => $detail->delivery_charge,
                'refunded_amount' => $detail->refunded_amount,
                'refund_reason' => $detail->refund_reason,
                'payment_option' => $detail->payment_option,
                'transaction_id' => $detail->transaction_id,
                'item_detail' => unserialize($detail->item_detail),
                'user_detail' => unserialize($detail->user_detail),
                'user_mobile_number' => $detail->user_mobile_number,
                'order_user_name' => $detail->order_user_name,
                'phone_code' => $detail->phone_code,
                'mobile_number' => $detail->mobile_number,
                'email' => $detail->email
            );
        }
        return $order_detailarr;
    }
    public function getMenuDetail($entity_id,$language_slug='',$restaurant_id='') {
        $this->db->select('menu.entity_id, menu.name, menu.price, menu.check_add_ons, menu.content_id, menu.category_id,cat.name as cat_name,cat.content_id as cat_content_id,menu.restaurant_id,menu.item_slug,menu.sku,menu.menu_detail,menu.image,menu.recipe_time,menu.availability,menu.is_veg,menu.is_combo_item,menu.food_type,menu.status,menu.popular_item,menu.language_slug,menu.is_deal,menu.is_masterdata');
        $this->db->join('category as cat','cat.entity_id = menu.category_id','left');
        $this->db->where('menu.entity_id',$entity_id);
        $this->db->where('menu.status',1);
        $this->db->where('menu.stock',1);
        $this->db->order_by('cat.name', 'ASC');
        $this->db->order_by('menu.name', 'ASC');
        if($language_slug!='')
        {
            $this->db->where('menu.language_slug',$language_slug);
        }       
        $result = $this->db->get('restaurant_menu_item as menu')->first_row();
        if(!empty($result)){
            $item_not_appicable_for_item_discount = array();
            $restaurant_data = $this->common_model->getSingleRow('restaurant','entity_id',$restaurant_id);
            $category_discount = '';
            if(!empty($restaurant_data) && $restaurant_data->content_id){
                $category_discount = $this->common_model->getCategoryDiscount($restaurant_data->content_id);
            }
            $ItemDiscount = $this->common_model->getItemDiscount(array('status'=>1,'coupon_type'=>'discount_on_items'));
            if(!empty($category_discount)){
                foreach ($category_discount as $key => $cat_value) {
                    if(!empty($cat_value['combined'])){
                        if(isset($cat_value['combined'][$result->cat_content_id])){
                            if($result->price >= $cat_value['combined'][$result->cat_content_id]['minimum_amount']){
                                array_push($item_not_appicable_for_item_discount, $result->content_id);
                                if($cat_value['combined'][$result->cat_content_id]['discount_type'] == 'Percentage'){
                                    $result->price = $result->price - (($result->price * $cat_value['combined'][$result->cat_content_id]['discount_value'])/100);
                                }
                                if($cat_value['combined'][$result->cat_content_id]['discount_type'] == 'Amount'){
                                    $result->price = $result->price - $cat_value['combined'][$result->cat_content_id]['discount_value'];
                                }
                            }
                        }
                    }
                }
            }
            if(!empty($ItemDiscount)) {
                foreach ($ItemDiscount as $cpnkey => $cpnvalue) {
                    if(!empty($cpnvalue['itemDetail'])) {
                        if(in_array($result->content_id,$cpnvalue['itemDetail']) && !in_array($result->content_id, $item_not_appicable_for_item_discount)){
                            if($cpnvalue['max_amount'] <= $result->price){ 
                                if($cpnvalue['amount_type'] == 'Percentage'){
                                    $result->price = $result->price - (($result->price * $cpnvalue['amount'])/100);
                                }else if($cpnvalue['amount_type'] == 'Amount'){
                                    $result->price = $result->price - $cpnvalue['amount'];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
    public function updateData($Data,$tblName,$fieldName,$ID) {
        $this->db->where($fieldName,$ID);
        $this->db->update($tblName,$Data);
        return $this->db->affected_rows();
    }
    public function getDevice($user_id) {
        $this->db->select('users.entity_id, users.device_id, users.language_slug, users.notification');
        $this->db->where('users.entity_id',$user_id);
        $this->db->where('status',1);
        return $this->db->get('users')->first_row();
    }
    public function getRestaurantmode_detail($user_id,$user_type='MasterAdmin')
    {
        $this->db->select('entity_id, schedule_mode, name,content_id');
        if(strtolower($user_type)=='restaurant admin')
        {
            $this->db->where('restaurant_owner_id',$user_id);
        }
        else if(strtolower($user_type)=='branch admin')
        {
            $this->db->where('branch_admin_id',$user_id);
        }
        
        $this->db->order_by('name','asc');
        return $this->db->get('restaurant')->result();
    }
    public function getCouponData($coupon_id) { 
        $this->db->select('entity_id, name, amount_type, amount, coupon_type'); 
        $this->db->where('entity_id',$coupon_id);   
        return $this->db->get('coupon')->first_row();   
    }
}
?>