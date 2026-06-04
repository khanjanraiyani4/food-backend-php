<?php
// create slug based on title
function slugify($text,$tablename,$fieldname,$primaryField=NULL,$primaryValue=NULL)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, '-');
    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // lowercase
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    $i = 1; 
    $baseSlug = $text;
    while(slug_exist($text,$tablename,$fieldname,$primaryField,$primaryValue))
    {
        $text = $baseSlug . "-" . $i++;        
    }
    return $text;
}
function slug_exist($text,$tablename,$fieldname,$primaryField,$primaryValue)
{ 
    //check slug is uniquee or not.
    $CI =& get_instance();
    $where = array(
        $fieldname=>$text
    );
    $array = $where;
    if (!empty($primaryField) && !empty($primaryValue)) {
        $whereA = array(
            $primaryField.'!='=>$primaryValue
        );
        $array = array_merge($where,$whereA);
    }
    $checkSlug = $CI->db->get_where($tablename,$array)->num_rows(); 
    if($checkSlug > 0)
    {
        return true;
    }
}
function paypal_details(){
    $CI =& get_instance();
    return $CI->common_model->get_payment_method_detail('paypal');
}
function stripe_details(){
    $CI =& get_instance();
    return $CI->common_model->get_payment_method_detail('stripe');
}
function getUserTypeList($lang_slug, $usertype = 'MasterAdmin'){
    if($usertype == 'Admin'){
        if($lang_slug == "fr"){
            $usertype = array(
                'BranchAdmin' =>'Administrateur de succursale',
            );
        }
        else if($lang_slug == "ar"){
            $usertype = array(
                'BranchAdmin' =>'مدير الفرع',
            );
        }
        else
        {
            $usertype = array(
                'BranchAdmin' =>'Branch Admin',
            );
        }
    }
    if($usertype == 'MasterAdmin'){
        if($lang_slug == "fr"){
            $usertype = array(
                'Admin' =>'Administrateur du restaurant',
                'BranchAdmin' =>'Administrateur de succursale', 
                // 'User' =>'Cliente',
            );
        }
        else if($lang_slug == "ar"){
            $usertype = array(
                'Admin' =>'إدارة المطعم',
                'BranchAdmin' =>'مدير الفرع',
                // 'User' =>'الزبون',
            );
        }
        else
        {
            $usertype = array(
                'Admin' =>'Restaurant Admin',
                'BranchAdmin' =>'Branch Admin',
                // 'User' =>'Customer',
            );
        }
    }
    return $usertype;
}
function generateEmailBody($body,$arrayVal)
{
    // replace # email body variable's
    $arrayVal['img_url']=base_url().'assets/front/images/logo.png';
    if($arrayVal['FirstName']==""){
        $arrayVal['FirstName'] = 'Unknown';
    }  
    $CI =& get_instance();
    if($CI->session->userdata('CompanyName'))
    {
        $body = str_replace("#Company_Name#",$CI->session->userdata('CompanyName'),$body);  
    }
    else
    {
        $body = str_replace("#Company_Name#",$CI->session->userdata('site_title'),$body);  
    }
    $body = str_replace("#img_url#",$arrayVal['img_url'],$body);
    $body = str_replace("#firstname#",$arrayVal['FirstName'],$body); 
    if(isset($arrayVal['LastName']))  
    $body = str_replace("#lastname#",$arrayVal['LastName'],$body);
    if(isset($arrayVal['SFirstName']))
    $body = str_replace("#s_firstname#",$arrayVal['SFirstName'],$body); 
    if(isset($arrayVal['SLastName'])) 
    $body = str_replace("#s_lastname#",$arrayVal['SLastName'],$body);
    if(isset($arrayVal['ForgotPasswordLink']))
    $body = str_replace("#forgotlink#",$arrayVal['ForgotPasswordLink'],$body);
    if(isset($arrayVal['Email']))  
    $body = str_replace("#email#",$arrayVal['Email'],$body);  
    if(isset($arrayVal['Password']))
    $body = str_replace("#password#",$arrayVal['Password'],$body); 
    if(isset($arrayVal['Sender_Email'])) 
    $body = str_replace("#s_email#",$arrayVal['Sender_Email'],$body);
    if(isset($arrayVal['Sender_Utype']))
    $body = str_replace("#s_utype#",$arrayVal['Sender_Utype'],$body);
    if(isset($arrayVal['Site_Name']))
    $body = str_replace("#Site_Name#",$arrayVal['Site_Name'],$body);
    if(isset($arrayVal['LoginLink']))
    $body = str_replace("#loginlink#",$arrayVal['LoginLink'],$body);
    if(isset($arrayVal['restaurant_name']))
    $body = str_replace("#restaurant#",$arrayVal['restaurant_name'],$body);
    if(isset($arrayVal['Status']))
    $body = str_replace("#status#",$arrayVal['Status'],$body);
    if(isset($arrayVal['Message']))
    $body = str_replace("#message#",$arrayVal['Message'],$body);
    $body = str_replace("#copy_years#",date('Y'),$body);
    if(isset($arrayVal['time']))
    $body = str_replace("#time#",$arrayVal['time'],$body);
    if(isset($arrayVal['Restaurant_name']))
    $body = str_replace("#Restaurant_name#",$arrayVal['Restaurant_name'],$body);
    if(isset($arrayVal['Address']))
    $body = str_replace("#Address#",$arrayVal['Address'],$body);
    if(isset($arrayVal['peoples']))
    $body = str_replace("#no_of_peoples#",$arrayVal['peoples'],$body);
    if(isset($arrayVal['order_id']))
    $body = str_replace("#order_id#",$arrayVal['order_id'],$body);
    if(isset($arrayVal['order_total']))
    $body = str_replace("#order_total#",$arrayVal['order_total'],$body);
    if(isset($arrayVal['track_order']))
    $body = str_replace("#track_order#",$arrayVal['track_order'],$body);
    if(isset($arrayVal['your_otp']))  
    $body = str_replace("#your_otp#",$arrayVal['your_otp'],$body);
    if(isset($arrayVal['res_phone_number']))  
    $body = str_replace("#res_phone_number#",$arrayVal['res_phone_number'],$body);
    if(isset($arrayVal['res_phone_number']))  
    $body = str_replace("#res_name#",$arrayVal['res_name'],$body);
    if(isset($arrayVal['res_name']))  
    $body = str_replace("#res_zip_code#",$arrayVal['res_zip_code'],$body);
    if(isset($arrayVal['res_zip_code']))  
    $body = str_replace("#res_phone_number#",$arrayVal['res_phone_number'],$body);
    if(isset($arrayVal['canceled_by']))  
    $body = str_replace("#canceled_by#",$arrayVal['canceled_by'],$body);
    if(isset($arrayVal['cancelled_order_text']))  
    $body = str_replace("#cancelled_order_text#",$arrayVal['cancelled_order_text'],$body);
    if(isset($arrayVal['updated_by']))
        $body = str_replace("#updated_by#",$arrayVal['updated_by'],$body);
    if(isset($arrayVal['order_refund_text']))
        $body = str_replace("#order_refund_text#",$arrayVal['order_refund_text'],$body);
    if(isset($arrayVal['user_ipaddress']))
        $body = str_replace("#user_ipaddress#",$arrayVal['user_ipaddress'],$body);
    if(isset($arrayVal['owners_phone_number']))
        $body = str_replace("#owners_phone_number#",$arrayVal['owners_phone_number'],$body);
    $body = str_replace("#base_url_email_temp#",base_url_email_temp,$body);
    $body = str_replace("#facebook_link#",facebook,$body);
    $body = str_replace("#googleplus_link#","#",$body);
    $body = str_replace("#instagram_link#",instagram,$body);
    $body = str_replace("#twitter_link#",twitter,$body);
    return $body;
}
function event_status($lang_slug)
{
    if($lang_slug == "fr"){
        $event_status = array(
            'pending'=>'En attente',
            /*'onGoing'=>'En cours',
            'completed'=>'Livré',*/
            'cancel'=>'Annuler',
            'paid'=>'Payé'
        );
    }
    else if($lang_slug == "ar"){
        $event_status = array(
            'pending'=>'قيد الانتظار',
            /*'onGoing'=>'جاري التنفيذ',
            'completed'=>'تم التوصيل',*/
            'cancel'=>'إلغاء',
            'paid'=>'دفع'
        );
    }
    else
    {
        $event_status = array(
            'pending'=>'Pending',
            /*'onGoing'=>'On Going',
            'completed'=>'Delivered',*/
            'cancel'=>'Cancel',
            'paid'=>'Paid'
        );
    }
    return $event_status;
}
function booking_status($lang_slug)
{
    if($lang_slug == "fr"){
        $event_status = array(
            'awaiting'=>'En attente',
            'confirmed'=>'Confirmé',
            'cancelled'=>'Annulé'
        );
    }
    else if($lang_slug == "ar"){
        $event_status = array(
            'awaiting'=>'في انتظار',
            'confirmed'=>'مؤكد',
            'cancelled'=>'ألغيت'
        );
    }
    else
    {
        $event_status = array(
            'awaiting'=>'Awaiting',
            'confirmed'=>'Confirmed',
            'cancelled'=>'Cancelled'
        );
    }
    return $event_status;
}
function order_status($lang_slug)
{
    if($lang_slug == "fr"){
        $order_status = array(
            'placed'=>'Mis',
            'accepted'=>'Accepté',
            //'preparing'=>'En train de préparer',
            'orderready'=>'Commande prête',
            'onGoing'=>'En cours de livraison',
            'delivered'=>'Livré',
            'cancel'=>'Annuler',
            'complete'=>'Achevée',
            'rejected'=>'Rejetée'
        );
    }
    else if($lang_slug == "ar"){
        $order_status = array(
            'placed'=>'وضعت',
            'accepted'=>'قبلت ',
            //'preparing'=>'خطة',
            'orderready'=>'اطلب جاهزًا ',
            'onGoing'=>'خارج للتوصيل',
            'delivered'=>'تم التوصيل',
            'cancel'=>'إلغاء',
            'complete'=>'اكتمال',
            'rejected'=>'مرفوض'
        );
    } else {
        $order_status = array(
            'placed'=>'Placed',
            'accepted'=>'Accepted',
            //'preparing'=>'Preparing',
            'orderready'=>'Ready For Pick Up', //'Order ready',
            'onGoing'=>'Out for delivery',
            'delivered'=>'Delivered',
            'cancel'=>'Cancel',
            'complete'=>'Complete',
            'rejected'=>'Rejected'
        );
    }
    return $order_status;
}
function order_status_forlogs($lang_slug) {
    if($lang_slug == "fr") {
        $order_status = array(
            'accepted_by_restaurant' => 'Accepté',
            'cancel'=>'Annuler',
            'complete'=>'Achevée',
            'delivered'=>'Livré',
            'ready'=>'Commande prête',
            'onGoing'=>'En cours de livraison',
            //'placed'=>'Mis',
            'rejected'=>'Rejetée'
        );
    } else if($lang_slug == "ar") {
        $order_status = array(
            'accepted_by_restaurant' =>'قبلت ',
            'cancel'=>'إلغاء',
            'complete'=>'اكتمال',
            'delivered'=>'تم التوصيل',
            'ready'=>'اطلب جاهزًا ',
            'onGoing'=>'خارج للتوصيل',
            //'placed'=>'وضعت',
            'rejected'=>'مرفوض'
        );
    } else {
        $order_status = array(
            'accepted_by_restaurant' => 'Accepted',
            'cancel'=>'Cancel',
            'complete'=>'Complete',
            'delivered'=>'Delivered',
            'ready'=>'Ready For Pick Up', //'Order ready',
            'onGoing'=>'Out for delivery',
            //'placed'=>'Placed',
            'rejected'=>'Rejected'
        );
    }
    return $order_status;
}
function dinein_order_status($lang_slug){
    if($lang_slug == "fr"){
      $order_status = array(
        'placed'=>'Mis',
        'accepted'=>'Accepté',
        //'preparing'=>'En train de préparer',
        //'orderready'=>'Prêt',
        'ready'=>'Servi',
        'cancel'=>'Annuler',
        'rejected'=>'Rejetée',
        'complete'=>'Achevée'
      );
    }
    else if($lang_slug == "ar"){
      $order_status = array(
        'placed'=>'وضعت',
        'accepted'=>'قبلت ',
        //'preparing'=>'خطة',
        //'orderready'=>'مستعد',
        'ready'=>'خدم',
        'cancel'=>'إلغاء',
        'rejected'=>'مرفوض',
        'complete'=>'اكتمال'
      );
    }
    else
    {
      $order_status = array(
        'placed'=>'Placed',
        'accepted'=>'Accepted',
        //'preparing'=>'Preparing',
        //'orderready'=>'Ready',
        'ready'=>'Served',
        'cancel'=>'Cancel',
        'rejected'=>'Rejected',
        'complete'=>'Complete'
      );
    }
  return $order_status;
}
function reason_types($lang_slug){
    if($lang_slug == "fr"){
        $reason_type = array(
            'cancel' =>'Cancel',
            'reject' =>'Reject',
        );
    }else if($lang_slug == "ar"){
        $reason_type = array(
            'cancel' =>'Cancel',
            'reject' =>'Reject',
        );
    }else{
        $reason_type = array(
            'cancel' =>'Cancel',
            'reject' =>'Reject',
        );
    }
    return $reason_type;
}
function number_format_unchanged_precision($number, $currency_code=NULL, $dec_point=',', $thousands_sep='.')
{
    if (!empty($currency_code) && $currency_code == "EUR") {
        if($dec_point==$thousands_sep){
            trigger_error('2 parameters for ' . METHOD . '() have the same value, that is "' . $dec_point . '" for $dec_point and $thousands_sep', E_USER_WARNING);
        }
        if(preg_match('{\.\d+}', $number, $matches)===1){
            $decimals = strlen($matches[0]) - 1;
        }else{
            $decimals = 0;
        }
        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }
    else
    {
        return number_format($number, 2);
    }
}
function currency_symboldisplay($number, $currency_code=NULL)
{
    $number = ($number)?$number:0;
    return $currency_code.$number;
}
//coupon type
function coupon_type(){
    return array(
        'discount_on_cart'=>'Discount on Cart Items',
        'discount_on_items'=>'Discount on Items',        
        'free_delivery'=>'Free Delivery',
        //'discount_on_combo'=>'Discount on combo deal',
        'user_registration'=>'User Registration',
        'discount_on_categories'=>'Discount on Categories',
        
    );
}
function getCountry()
{
    return $countryArray = array(
        'AD'=>array('name'=>'ANDORRA','code'=>'+376'),
        'AE'=>array('name'=>'UNITED ARAB EMIRATES','code'=>'+971'),
        'AF'=>array('name'=>'AFGHANISTAN','code'=>'+93'),
        'AG'=>array('name'=>'ANTIGUA AND BARBUDA','code'=>'+1268'),
        'AI'=>array('name'=>'ANGUILLA','code'=>'+1264'),
        'AL'=>array('name'=>'ALBANIA','code'=>'+355'),
        'AM'=>array('name'=>'ARMENIA','code'=>'+374'),
        'AN'=>array('name'=>'NETHERLANDS ANTILLES','code'=>'+599'),
        'AO'=>array('name'=>'ANGOLA','code'=>'+244'),
        'AQ'=>array('name'=>'ANTARCTICA','code'=>'+672'),
        'AR'=>array('name'=>'ARGENTINA','code'=>'+54'),
        'AS'=>array('name'=>'AMERICAN SAMOA','code'=>'+1684'),
        'AT'=>array('name'=>'AUSTRIA','code'=>'+43'),
        'AU'=>array('name'=>'AUSTRALIA','code'=>'+61'),
        'AW'=>array('name'=>'ARUBA','code'=>'+297'),
        'AZ'=>array('name'=>'AZERBAIJAN','code'=>'+994'),
        'BA'=>array('name'=>'BOSNIA AND HERZEGOVINA','code'=>'+387'),
        'BB'=>array('name'=>'BARBADOS','code'=>'+1246'),
        'BD'=>array('name'=>'BANGLADESH','code'=>'+880'),
        'BE'=>array('name'=>'BELGIUM','code'=>'+32'),
        'BF'=>array('name'=>'BURKINA FASO','code'=>'+226'),
        'BG'=>array('name'=>'BULGARIA','code'=>'+359'),
        'BH'=>array('name'=>'BAHRAIN','code'=>'+973'),
        'BI'=>array('name'=>'BURUNDI','code'=>'+257'),
        'BJ'=>array('name'=>'BENIN','code'=>'+229'),
        'BL'=>array('name'=>'SAINT BARTHELEMY','code'=>'+590'),
        'BM'=>array('name'=>'BERMUDA','code'=>'+1441'),
        'BN'=>array('name'=>'BRUNEI DARUSSALAM','code'=>'+673'),
        'BO'=>array('name'=>'BOLIVIA','code'=>'+591'),
        'BR'=>array('name'=>'BRAZIL','code'=>'+55'),
        'BS'=>array('name'=>'BAHAMAS','code'=>'+1242'),
        'BT'=>array('name'=>'BHUTAN','code'=>'+975'),
        'BW'=>array('name'=>'BOTSWANA','code'=>'+267'),
        'BY'=>array('name'=>'BELARUS','code'=>'+375'),
        'BZ'=>array('name'=>'BELIZE','code'=>'+501'),
        'CA'=>array('name'=>'CANADA','code'=>'+1'),
        'CC'=>array('name'=>'COCOS (KEELING) ISLANDS','code'=>'+61'),
        'CD'=>array('name'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE','code'=>'+243'),
        'CF'=>array('name'=>'CENTRAL AFRICAN REPUBLIC','code'=>'+236'),
        'CG'=>array('name'=>'CONGO','code'=>'+242'),
        'CH'=>array('name'=>'SWITZERLAND','code'=>'+41'),
        'CI'=>array('name'=>'COTE D IVOIRE','code'=>'+225'),
        'CK'=>array('name'=>'COOK ISLANDS','code'=>'+682'),
        'CL'=>array('name'=>'CHILE','code'=>'+56'),
        'CM'=>array('name'=>'CAMEROON','code'=>'+237'),
        'CN'=>array('name'=>'CHINA','code'=>'+86'),
        'CO'=>array('name'=>'COLOMBIA','code'=>'+57'),
        'CR'=>array('name'=>'COSTA RICA','code'=>'+506'),
        'CU'=>array('name'=>'CUBA','code'=>'+53'),
        'CV'=>array('name'=>'CAPE VERDE','code'=>'+238'),
        'CX'=>array('name'=>'CHRISTMAS ISLAND','code'=>'+61'),
        'CY'=>array('name'=>'CYPRUS','code'=>'+357'),
        'CZ'=>array('name'=>'CZECH REPUBLIC','code'=>'+420'),
        'DE'=>array('name'=>'GERMANY','code'=>'+49'),
        'DJ'=>array('name'=>'DJIBOUTI','code'=>'+253'),
        'DK'=>array('name'=>'DENMARK','code'=>'+45'),
        'DM'=>array('name'=>'DOMINICA','code'=>'+1767'),
        'DO'=>array('name'=>'DOMINICAN REPUBLIC','code'=>'+1809'),
        'DZ'=>array('name'=>'ALGERIA','code'=>'+213'),
        'EC'=>array('name'=>'ECUADOR','code'=>'+593'),
        'EE'=>array('name'=>'ESTONIA','code'=>'+372'),
        'EG'=>array('name'=>'EGYPT','code'=>'+20'),
        'ER'=>array('name'=>'ERITREA','code'=>'+291'),
        'ES'=>array('name'=>'SPAIN','code'=>'+34'),
        'ET'=>array('name'=>'ETHIOPIA','code'=>'+251'),
        'FI'=>array('name'=>'FINLAND','code'=>'+358'),
        'FJ'=>array('name'=>'FIJI','code'=>'+679'),
        'FK'=>array('name'=>'FALKLAND ISLANDS (MALVINAS)','code'=>'+500'),
        'FM'=>array('name'=>'MICRONESIA, FEDERATED STATES OF','code'=>'+691'),
        'FO'=>array('name'=>'FAROE ISLANDS','code'=>'+298'),
        'FR'=>array('name'=>'FRANCE','code'=>'+33'),
        'GA'=>array('name'=>'GABON','code'=>'+241'),
        'GB'=>array('name'=>'UNITED KINGDOM','code'=>'+44'),
        'GD'=>array('name'=>'GRENADA','code'=>'+1473'),
        'GE'=>array('name'=>'GEORGIA','code'=>'+995'),
        'GH'=>array('name'=>'GHANA','code'=>'+233'),
        'GI'=>array('name'=>'GIBRALTAR','code'=>'+350'),
        'GL'=>array('name'=>'GREENLAND','code'=>'+299'),
        'GM'=>array('name'=>'GAMBIA','code'=>'+220'),
        'GN'=>array('name'=>'GUINEA','code'=>'+224'),
        'GQ'=>array('name'=>'EQUATORIAL GUINEA','code'=>'+240'),
        'GR'=>array('name'=>'GREECE','code'=>'+30'),
        'GT'=>array('name'=>'GUATEMALA','code'=>'+502'),
        'GU'=>array('name'=>'GUAM','code'=>'+1671'),
        'GW'=>array('name'=>'GUINEA-BISSAU','code'=>'+245'),
        'GY'=>array('name'=>'GUYANA','code'=>'+592'),
        'HK'=>array('name'=>'HONG KONG','code'=>'+852'),
        'HN'=>array('name'=>'HONDURAS','code'=>'+504'),
        'HR'=>array('name'=>'CROATIA','code'=>'+385'),
        'HT'=>array('name'=>'HAITI','code'=>'+509'),
        'HU'=>array('name'=>'HUNGARY','code'=>'+36'),
        'ID'=>array('name'=>'INDONESIA','code'=>'+62'),
        'IE'=>array('name'=>'IRELAND','code'=>'+353'),
        'IL'=>array('name'=>'ISRAEL','code'=>'+972'),
        'IM'=>array('name'=>'ISLE OF MAN','code'=>'+44'),
        'IN'=>array('name'=>'INDIA','code'=>'+91'),
        'IQ'=>array('name'=>'IRAQ','code'=>'+964'),
        'IR'=>array('name'=>'IRAN, ISLAMIC REPUBLIC OF','code'=>'+98'),
        'IS'=>array('name'=>'ICELAND','code'=>'+354'),
        'IT'=>array('name'=>'ITALY','code'=>'+39'),
        'JM'=>array('name'=>'JAMAICA','code'=>'+1876'),
        'JO'=>array('name'=>'JORDAN','code'=>'+962'),
        'JP'=>array('name'=>'JAPAN','code'=>'+81'),
        'KE'=>array('name'=>'KENYA','code'=>'+254'),
        'KG'=>array('name'=>'KYRGYZSTAN','code'=>'+996'),
        'KH'=>array('name'=>'CAMBODIA','code'=>'+855'),
        'KI'=>array('name'=>'KIRIBATI','code'=>'+686'),
        'KM'=>array('name'=>'COMOROS','code'=>'+269'),
        'KN'=>array('name'=>'SAINT KITTS AND NEVIS','code'=>'+1869'),
        'KP'=>array('name'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF','code'=>'+850'),
        'KR'=>array('name'=>'KOREA REPUBLIC OF','code'=>'+82'),
        'KW'=>array('name'=>'KUWAIT','code'=>'+965'),
        'KY'=>array('name'=>'CAYMAN ISLANDS','code'=>'+1345'),
        'KZ'=>array('name'=>'KAZAKSTAN','code'=>'+7'),
        'LA'=>array('name'=>'LAO PEOPLES DEMOCRATIC REPUBLIC','code'=>'+856'),
        'LB'=>array('name'=>'LEBANON','code'=>'+961'),
        'LC'=>array('name'=>'SAINT LUCIA','code'=>'+1758'),
        'LI'=>array('name'=>'LIECHTENSTEIN','code'=>'+423'),
        'LK'=>array('name'=>'SRI LANKA','code'=>'+94'),
        'LR'=>array('name'=>'LIBERIA','code'=>'+231'),
        'LS'=>array('name'=>'LESOTHO','code'=>'+266'),
        'LT'=>array('name'=>'LITHUANIA','code'=>'+370'),
        'LU'=>array('name'=>'LUXEMBOURG','code'=>'+352'),
        'LV'=>array('name'=>'LATVIA','code'=>'+371'),
        'LY'=>array('name'=>'LIBYAN ARAB JAMAHIRIYA','code'=>'+218'),
        'MA'=>array('name'=>'MOROCCO','code'=>'+212'),
        'MC'=>array('name'=>'MONACO','code'=>'+377'),
        'MD'=>array('name'=>'MOLDOVA, REPUBLIC OF','code'=>'+373'),
        'ME'=>array('name'=>'MONTENEGRO','code'=>'+382'),
        'MF'=>array('name'=>'SAINT MARTIN','code'=>'+1599'),
        'MG'=>array('name'=>'MADAGASCAR','code'=>'+261'),
        'MH'=>array('name'=>'MARSHALL ISLANDS','code'=>'+692'),
        'MK'=>array('name'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','code'=>'+389'),
        'ML'=>array('name'=>'MALI','code'=>'+223'),
        'MM'=>array('name'=>'MYANMAR','code'=>'+95'),
        'MN'=>array('name'=>'MONGOLIA','code'=>'+976'),
        'MO'=>array('name'=>'MACAU','code'=>'+853'),
        'MP'=>array('name'=>'NORTHERN MARIANA ISLANDS','code'=>'+1670'),
        'MR'=>array('name'=>'MAURITANIA','code'=>'+222'),
        'MS'=>array('name'=>'MONTSERRAT','code'=>'+1664'),
        'MT'=>array('name'=>'MALTA','code'=>'+356'),
        'MU'=>array('name'=>'MAURITIUS','code'=>'+230'),
        'MV'=>array('name'=>'MALDIVES','code'=>'+960'),
        'MW'=>array('name'=>'MALAWI','code'=>'+265'),
        'MX'=>array('name'=>'MEXICO','code'=>'+52'),
        'MY'=>array('name'=>'MALAYSIA','code'=>'+60'),
        'MZ'=>array('name'=>'MOZAMBIQUE','code'=>'+258'),
        'NA'=>array('name'=>'NAMIBIA','code'=>'+264'),
        'NC'=>array('name'=>'NEW CALEDONIA','code'=>'+687'),
        'NE'=>array('name'=>'NIGER','code'=>'+227'),
        'NG'=>array('name'=>'NIGERIA','code'=>'+234'),
        'NI'=>array('name'=>'NICARAGUA','code'=>'+505'),
        'NL'=>array('name'=>'NETHERLANDS','code'=>'+31'),
        'NO'=>array('name'=>'NORWAY','code'=>'+47'),
        'NP'=>array('name'=>'NEPAL','code'=>'+977'),
        'NR'=>array('name'=>'NAURU','code'=>'+674'),
        'NU'=>array('name'=>'NIUE','code'=>'+683'),
        'NZ'=>array('name'=>'NEW ZEALAND','code'=>'+64'),
        'OM'=>array('name'=>'OMAN','code'=>'+968'),
        'PA'=>array('name'=>'PANAMA','code'=>'+507'),
        'PE'=>array('name'=>'PERU','code'=>'+51'),
        'PF'=>array('name'=>'FRENCH POLYNESIA','code'=>'+689'),
        'PG'=>array('name'=>'PAPUA NEW GUINEA','code'=>'+675'),
        'PH'=>array('name'=>'PHILIPPINES','code'=>'+63'),
        'PK'=>array('name'=>'PAKISTAN','code'=>'+92'),
        'PL'=>array('name'=>'POLAND','code'=>'+48'),
        'PM'=>array('name'=>'SAINT PIERRE AND MIQUELON','code'=>'+508'),
        'PN'=>array('name'=>'PITCAIRN','code'=>'+870'),
        'PR'=>array('name'=>'PUERTO RICO','code'=>'+1'),
        'PT'=>array('name'=>'PORTUGAL','code'=>'+351'),
        'PW'=>array('name'=>'PALAU','code'=>'+680'),
        'PY'=>array('name'=>'PARAGUAY','code'=>'+595'),
        'QA'=>array('name'=>'QATAR','code'=>'+974'),
        'RO'=>array('name'=>'ROMANIA','code'=>'+40'),
        'RS'=>array('name'=>'SERBIA','code'=>'+381'),
        'RU'=>array('name'=>'RUSSIAN FEDERATION','code'=>'+7'),
        'RW'=>array('name'=>'RWANDA','code'=>'+250'),
        'SA'=>array('name'=>'SAUDI ARABIA','code'=>'+966'),
        'SB'=>array('name'=>'SOLOMON ISLANDS','code'=>'+677'),
        'SC'=>array('name'=>'SEYCHELLES','code'=>'+248'),
        'SD'=>array('name'=>'SUDAN','code'=>'+249'),
        'SE'=>array('name'=>'SWEDEN','code'=>'+46'),
        'SG'=>array('name'=>'SINGAPORE','code'=>'+65'),
        'SH'=>array('name'=>'SAINT HELENA','code'=>'+290'),
        'SI'=>array('name'=>'SLOVENIA','code'=>'+386'),
        'SK'=>array('name'=>'SLOVAKIA','code'=>'+421'),
        'SL'=>array('name'=>'SIERRA LEONE','code'=>'+232'),
        'SM'=>array('name'=>'SAN MARINO','code'=>'+378'),
        'SN'=>array('name'=>'SENEGAL','code'=>'+221'),
        'SO'=>array('name'=>'SOMALIA','code'=>'+252'),
        'SR'=>array('name'=>'SURINAME','code'=>'+597'),
        'ST'=>array('name'=>'SAO TOME AND PRINCIPE','code'=>'+239'),
        'SV'=>array('name'=>'EL SALVADOR','code'=>'+503'),
        'SY'=>array('name'=>'SYRIAN ARAB REPUBLIC','code'=>'+963'),
        'SZ'=>array('name'=>'SWAZILAND','code'=>'+268'),
        'TC'=>array('name'=>'TURKS AND CAICOS ISLANDS','code'=>'+1649'),
        'TD'=>array('name'=>'CHAD','code'=>'+235'),
        'TG'=>array('name'=>'TOGO','code'=>'+228'),
        'TH'=>array('name'=>'THAILAND','code'=>'+66'),
        'TJ'=>array('name'=>'TAJIKISTAN','code'=>'+992'),
        'TK'=>array('name'=>'TOKELAU','code'=>'+690'),
        'TL'=>array('name'=>'TIMOR-LESTE','code'=>'+670'),
        'TM'=>array('name'=>'TURKMENISTAN','code'=>'+993'),
        'TN'=>array('name'=>'TUNISIA','code'=>'+216'),
        'TO'=>array('name'=>'TONGA','code'=>'+676'),
        'TR'=>array('name'=>'TURKEY','code'=>'+90'),
        'TT'=>array('name'=>'TRINIDAD AND TOBAGO','code'=>'+1868'),
        'TV'=>array('name'=>'TUVALU','code'=>'+688'),
        'TW'=>array('name'=>'TAIWAN, PROVINCE OF CHINA','code'=>'+886'),
        'TZ'=>array('name'=>'TANZANIA, UNITED REPUBLIC OF','code'=>'+255'),
        'UA'=>array('name'=>'UKRAINE','code'=>'+380'),
        'UG'=>array('name'=>'UGANDA','code'=>'+256'),
        'US'=>array('name'=>'UNITED STATES','code'=>'+1'),
        'UY'=>array('name'=>'URUGUAY','code'=>'+598'),
        'UZ'=>array('name'=>'UZBEKISTAN','code'=>'+998'),
        'VA'=>array('name'=>'HOLY SEE (VATICAN CITY STATE)','code'=>'+39'),
        'VC'=>array('name'=>'SAINT VINCENT AND THE GRENADINES','code'=>'+1784'),
        'VE'=>array('name'=>'VENEZUELA','code'=>'+58'),
        'VG'=>array('name'=>'VIRGIN ISLANDS, BRITISH','code'=>'+1284'),
        'VI'=>array('name'=>'VIRGIN ISLANDS, U.S.','code'=>'+1340'),
        'VN'=>array('name'=>'VIET NAM','code'=>'+84'),
        'VU'=>array('name'=>'VANUATU','code'=>'+678'),
        'WF'=>array('name'=>'WALLIS AND FUTUNA','code'=>'+681'),
        'WS'=>array('name'=>'SAMOA','code'=>'+685'),
        'XK'=>array('name'=>'KOSOVO','code'=>'+381'),
        'YE'=>array('name'=>'YEMEN','code'=>'+967'),
        'YT'=>array('name'=>'MAYOTTE','code'=>'+262'),
        'ZA'=>array('name'=>'SOUTH AFRICA','code'=>'+27'),
        'ZM'=>array('name'=>'ZAMBIA','code'=>'+260'),
        'ZW'=>array('name'=>'ZIMBABWE','code'=>'+263')
    );
}
function getModuleTilte($arrLangaugeData, $key="name")
{
    $CI =& get_instance();
    $language_slug = $CI->session->userdata('language_slug');
    if(!empty($arrLangaugeData[$language_slug][$key])){
        $deleteName = addslashes($arrLangaugeData[$language_slug][$key]);
    }    
    else {
        $Languages = $CI->common_model->getLanguages();

        foreach ($Languages as $lang) { 
            if(array_key_exists($lang->language_slug,$arrLangaugeData)){
                $deleteName = $arrLangaugeData[$lang->language_slug][$key];
                break;
            }
        }
        /*if(empty($deleteName)){
            $arrNames = reset($arrLangaugeData);
            $deleteName = addslashes($arrNames[$key]);
        }*/
    }
    return $deleteName;
}
//New code for get the time zone wiht user browser[detect] :: Start
function user_timezone()
{
    ?>
    <script>
        var timezone_name = Intl.DateTimeFormat().resolvedOptions().timeZone;
        var timezone_name = timezone_name.replace("Calcutta", "Kolkata");
        document.cookie = 'timezone_name='+timezone_name;
        location.reload();
    </script>
    <?php    
}

if(strstr(strtolower($_SERVER['REQUEST_URI']),"/api/") || strstr(strtolower($_SERVER['REQUEST_URI']),"/driver_api/") || strstr(strtolower($_SERVER['REQUEST_URI']),"/general_api/") || strstr(strtolower($_SERVER['REQUEST_URI']),"/branch_admin_api/") || strstr($_SERVER['REQUEST_URI'],"/checkCartRestaurantDetails") || strstr($_SERVER['REQUEST_URI'],"/getCustomAddOnsDetails") || strstr($_SERVER['REQUEST_URI'],"/customItemCount") || strstr($_SERVER['REQUEST_URI'],"/ajax_restaurant_details") || strstr($_SERVER['REQUEST_URI'],"/getResturantsDish") || strstr($_SERVER['REQUEST_URI'],"/getReviewsPagination") || strstr($_SERVER['REQUEST_URI'],"/checkResStat") || strstr($_SERVER['REQUEST_URI'],"/checkTableAvailability") || strstr($_SERVER['REQUEST_URI'],"/checkEventAvailability") || strstr($_SERVER['REQUEST_URI'],"/add_package") || strstr($_SERVER['REQUEST_URI'],"/checkEventAvailability") || strstr($_SERVER['REQUEST_URI'],"/checkCartRestaurant") || strstr($_SERVER['REQUEST_URI'],"/emptyCart") || strstr($_SERVER['REQUEST_URI'],"/addToCart") || strstr($_SERVER['REQUEST_URI'],"/getCustomAddOns") || strstr($_SERVER['REQUEST_URI'],"/checkMenuItem") || strstr($_SERVER['REQUEST_URI'],"/getTimeSlot"))
{}
else
{   
    $CI =& get_instance();
    $CI->load->library('session');
    if($CI->session->userdata('timezone_name') || isset($_COOKIE['timezone_name']))
    {
        if(!$CI->session->userdata('timezone_name'))
        {
            //$_SESSION['timezone_name'] = $_COOKIE['timezone_name'];
            $CI->session->set_userdata('timezone_name',$_COOKIE['timezone_name']);
        }
    }
    else
    {
        user_timezone();
        setcookie('timezone_name', $_COOKIE['timezone_name'], time() + (86400 * 30), "/"); //1 day
        $CI->session->set_userdata('timezone_name',$_COOKIE['timezone_name']); 
           
    }    
}
//New code for get the time zone wiht user browser[detect] :: End

function generate_user_password($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars),0,$length);
}
//Code for allow add/edit/delete permission :: Start
function Disabled_HideButton($is_masterdata,$is_disable='yes')
{
    //Note :: returnval='1' mean button disable,
    //returnval='2' mean button hide
    $returnval='';
    if($is_masterdata=='1')
    {
        $returnval='1';
        if($is_disable=='no')
        {
            $returnval='2';
        }
    }
    return $returnval;
}
//Code for allow add/edit/delete permission :: End

function get_driver_tip_amount()
{
    $CI =& get_instance();
    $result = $CI->db->select('OptionValue')->where('OptionSlug','driver_tip_amount')->get('system_option')->first_row();
    return ($result->OptionValue) ? array_filter(explode("\r\n", $result->OptionValue)) : driver_tiparr;
}

function get_default_driver_tip_amount()
{
    $CI =& get_instance();
    $result = $CI->db->select('OptionValue')->where('OptionSlug','default_driver_tip')->get('system_option')->first_row();
    return ($result->OptionValue) ? $result->OptionValue : '';
}

function get_default_system_currency(){
    $CI =& get_instance();
    return (ACTIVATE_SYSTEM_DEFAULT_CURRENCY) ? $CI->db->get_where('currencies',array('currency_id' => DEFAULT_CURRENCY_ID))->first_row() : NULL;
}

function validate_captcha_common($g_recaptcha_response) {
  $CI =& get_instance();
  $recaptcha = trim($g_recaptcha_response);
  $userIp= $CI->input->ip_address();
  $secret= config_item('GOOGLE_CAPTCHA_SECRET_KEY');
  $data = array(
      'secret' => "$secret",
      'response' => "$recaptcha",
      'remoteip' =>"$userIp"
  );

  $verify = curl_init();
  curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
  curl_setopt($verify, CURLOPT_POST, true);
  curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($verify);
  $status= json_decode($response, true);
  if(empty($status['success'])){
      return false;
  }else{           
      return true;
  }
}
?>