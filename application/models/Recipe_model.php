<?php
class Recipe_model extends CI_Model {
    function __construct()
    {
        parent::__construct();        
    }
    // get restaurant reviews
    public function getRestaurantReview($restaurant_content_id){
        $this->db->select("review.restaurant_id,review.rating,review.review,users.first_name,users.last_name,users.image");
        $this->db->join('users','review.user_id = users.entity_id','left');
        $this->db->where('review.status',1);
        $this->db->where('review.restaurant_content_id',$restaurant_content_id);
        $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
        $result =  $this->db->get('review')->result();
        $avg_rating = 0;
        if (!empty($result)) {
            $rating = array_column($result, 'rating');
            $a = array_filter($rating);
            if(count($a)) {
                $average = array_sum($a)/count($a);
            }
            $avg_rating = number_format($average,1);
        }
        return $avg_rating;
    }
    // get restaurant menu details
    public function getMenuItemDetail($content_id){
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select("restaurant.image as restaurant_image,restaurant_menu_item.*");
        $this->db->join('restaurant','restaurant_menu_item.restaurant_id = restaurant.entity_id','left');
        $this->db->where('restaurant_menu_item.content_id',$content_id);
        $this->db->where('restaurant_menu_item.language_slug',$language_slug);
        $result = $this->db->get('restaurant_menu_item')->result_array();
        if (!empty($result)) {
            $result[0]['image'] = ($result[0]['image'])?$result[0]['image']:'';
            $result[0]['restaurant_image'] = ($result[0]['restaurant_image'])?$result[0]['restaurant_image']:'';
        } 
        return $result;
    }
    // get menu id
    public function getMenuItemID($item_slug){
        $this->db->select('entity_id');
        return $this->db->get_where('restaurant_menu_item',array('item_slug'=>$item_slug))->first_row();
    }
    // get content id
    public function getContentID($item_slug){
        $this->db->select('content_id');
        return $this->db->get_where('restaurant_menu_item',array('item_slug'=>$item_slug))->first_row();
    }
    // get all recipies
    public function get_all_recipies($limit,$offset,$recipe=NULL){
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('recipe.slug,recipe.image,recipe.name,food_type.entity_id,is_veg');
        if (!empty($recipe)) {
            $where = "recipe.name LIKE '%".$this->common_model->escapeString($recipe)."%'";
            $this->db->where($where);
        }
        $this->db->where('recipe.language_slug',$language_slug);
        $this->db->join('food_type','recipe.food_type = food_type.entity_id','left');
        $this->db->group_by('recipe.content_id');
        $this->db->order_by('recipe.entity_id', 'DESC');
        $this->db->limit($limit,$offset);
        $result['data'] = $this->db->get_where('recipe',array('recipe.status'=>1))->result_array();
        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $value) {
                $result['data'][$key]['image'] = ($value['image']) ?$value['image'] : '';
            }
        } 
        // total count
        $this->db->select('recipe.slug,recipe.image,recipe.name,food_type.entity_id,is_veg');
        if (!empty($recipe)) {
            $where = "recipe.name LIKE '%".$this->common_model->escapeString($recipe)."%'";
            $this->db->where($where);
        }
        $this->db->where('recipe.language_slug',$language_slug);
        $this->db->join('food_type','recipe.food_type = food_type.entity_id','left');
        $this->db->group_by('recipe.content_id');
        $result['count'] =  $this->db->get_where('recipe',array('recipe.status'=>1))->num_rows();
        return $result;
    }
    // get recipe details
    public function get_recipe_detail($content_id){
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('entity_id, name,image,content_id,detail,recipe_time,youtube_video,ingredients,recipe_detail');
        $this->db->where('content_id',$content_id);
        $this->db->where('language_slug',$language_slug);
        $result = $this->db->get('recipe')->result_array();
        if (!empty($result)) {
            $result[0]['image'] = ($result[0]['image']) ? $result[0]['image'] : '';
            // $result[0]['restaurant_image'] = ($result[0]['restaurant_image'])?image_url.$result[0]['restaurant_image']:'';
        } 
        return $result;
    }
    public function get_content_id($item_slug){
        $this->db->select('content_id');
        return $this->db->get_where('recipe',array('slug'=>$item_slug))->first_row();
    }
    public function get_menu_detail($recipe_content_id){
        $language_slug = $this->session->userdata('language_slug');
        $this->db->select('menu_content_id');
        $this->db->where('recipe_content_id',$recipe_content_id);
        $menu_content_id = $this->db->get('restaurant_menu_recipe_map')->result();
        if(!empty($menu_content_id)){
            
           $this->db->select('menu.check_add_ons,menu.entity_id,menu.restaurant_id,restaurant.currency_id,currencies.currency_symbol,restaurant.timings,restaurant.enable_hours');
            $this->db->join('restaurant','restaurant.entity_id= menu.restaurant_id','left');
            $this->db->join('category','category.entity_id= menu.category_id','left');
            $this->db->join('currencies','restaurant.currency_id= currencies.currency_id','left');
            $this->db->where('category.status',1);
            $this->db->where('menu.status',1);
            $this->db->where('restaurant.status',1);
            $this->db->where('menu.language_slug',$language_slug);
            $this->db->where_in('menu.content_id',array_column($menu_content_id,'menu_content_id'));
            $menu_list = $this->db->get('restaurant_menu_item as menu')->result();

            if (!empty($menu_list)) {
                $default_currency = get_default_system_currency();
                foreach ($menu_list as $key => $value) {
                    $timing = $value->timings;
                    if($timing){
                       $timing =  unserialize(html_entity_decode($timing));
                       $newTimingArr = array();
                        $day = date("l");
                        //print_r($timing);exit;
                        foreach($timing as $keys=>$values) {
                            $day = date("l");
                            if($keys == strtolower($day))
                            {
                                $close = 'Closed';
                                if($value->enable_hours=='1')
                                {
                                    $newTimingArr[strtolower($day)]['open'] = (!empty($values['open']))?date('g:i A',strtotime($values['open'])):'';
                                    $newTimingArr[strtolower($day)]['close'] = (!empty($values['close']))?date('g:i A',strtotime($values['close'])):'';
                                    $newTimingArr[strtolower($day)]['off'] = (!empty($values['open']) && !empty($values['close']))?'open':'close';
                                    $close = 'Closed';
                                    if (!empty($values['open']) && !empty($values['close'])) {
                                        $close = $this->common_model->openclose($values['open'],$values['close']);
                                    }
                                    $newTimingArr[strtolower($day)]['closing'] = $close;
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
                    }
                    else
                    {
                        $newTimingArr[strtolower($day)]['closing'] = 'close';
                        $newTimingArr[strtolower($day)]['open'] = '';
                        $newTimingArr[strtolower($day)]['close'] ='';
                        $newTimingArr[strtolower($day)]['off'] = 'close';
                    }
                    $menu_list[$key]->timings = $newTimingArr[strtolower($day)];
                    if(!empty($default_currency)){
                        $menu_list[$key]->currency_symbol = $default_currency->currency_symbol;
                    }
                }
            }
            return $menu_list;
        }
    }
    public function viewRestaurant($restaurant_id){
        $this->db->select('restaurant_slug');
        $this->db->where('entity_id',$restaurant_id);
        $this->db->where('status',1);
        return $res =  $this->db->get('restaurant')->result();
    }
    
}