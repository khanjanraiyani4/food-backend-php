<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Review extends CI_Controller { 
    public $module_name = 'Review';
    public $controller_name = 'review';
    public $prefix = '_rw'; 
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/review_model');
    }
    // view review
    public function view(){
        if(in_array('review~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('rating_review').' | '.$this->lang->line('site_title');
            //review count
            $this->db->select('review.entity_id');
            $this->db->join('restaurant as res','review.restaurant_content_id = res.content_id','left');
            $this->db->join('users as u','review.user_id = u.entity_id','left');
            $this->db->where('review.restaurant_id !=', '');
            $this->db->where('(review.order_user_id = 0 OR review.order_user_id is NULL)');
            $this->db->where('res.language_slug', $this->session->userdata('language_slug'));  
            if($this->session->userdata('AdminUserType') == 'Restaurant Admin'){
                $this->db->where('res.restaurant_owner_id',$this->session->userdata('AdminUserID'));
            }
            if($this->session->userdata('AdminUserType') == 'Branch Admin'){
                $this->db->where('res.branch_admin_id',$this->session->userdata('AdminUserID'));
            }
            $data['review_count'] = $this->db->get('review')->num_rows();
            $this->load->view(ADMIN_URL.'/review',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    //ajax view
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        $sortfields = array(
            1=>'res.name',
            2=>'u.first_name',
            3=>'review',
            4=>'rating',
            5=>'review.created_date'
        );
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        //Get Recored from model
        $grid_data = $this->review_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $grid_data['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        foreach ($grid_data['data'] as $key => $val) {
            $deleteName = addslashes($val->first_name);
            $msgDelete = "'".sprintf(addslashes($this->lang->line('delete_module_review')),$deleteName)."'";
            $records["aaData"][] = array(
                $nCount,
                $val->rname,
                $val->first_name.' '.$val->last_name,
                utf8_decode($val->review),
                $val->rating,
                // ($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
                (in_array('review~ajaxDelete',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteDetail('.$val->entity_id.','.$msgDelete.')" class="delete btn btn-sm default-btn margin-bottom" title="'.$this->lang->line('delete').'"><i class="fa fa-trash"></i></button>' : '',
                //<button onclick="disableDetail('.$val->entity_id.','.$val->status.')"  title="Click here for '.($val->status?'Deactivate':'Activate').' " class="delete btn btn-sm default-btn margin-bottom"><i class="fa fa-'.($val->status?'times':'check').'"></i> '.($val->status?'Deactivate':'Activate').'</button>
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }
    // method to change status
    public function ajaxdisable() {
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        if($entity_id != ''){
            $this->review_model->UpdatedStatus('review',$entity_id,$this->input->post('status'));
        }
    }
    // method for deleting
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $resname = $this->review_model->getRestaurantByReviewId($entity_id);
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted a review from restaurant - '.$resname);
        $this->review_model->ajaxDelete('review',$entity_id);
        // $this->session->set_flashdata('userPageMSG', $this->lang->line('success_delete'));
        $_SESSION['userPageMSG'] = $this->lang->line('success_delete');
    }
}
?>