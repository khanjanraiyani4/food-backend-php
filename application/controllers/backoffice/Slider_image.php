<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Slider_image extends CI_Controller { 
    public $controller_name = 'slider-image';
    public $prefix = '_slider';
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect('home');
        }
        $this->load->library('form_validation');
        $this->load->model(ADMIN_URL.'/slider_image_model');
    }
    //view
    public function view() {
      if(in_array('slider-image~view',$this->session->userdata("UserAccessArray"))) {
          $data['meta_title'] = $this->lang->line('title_slider_image')." ".$this->lang->line('management').' | '.$this->lang->line('site_title');
          $data['slider_count'] = $this->slider_image_model->getsliderCount();        
          $this->load->view(ADMIN_URL.'/slider_images',$data);
      } else {
        redirect(base_url().ADMIN_URL);
      }
    }
    // add slider images
    public function add(){
      if(in_array('slider-image~add',$this->session->userdata("UserAccessArray"))) {
        $data['meta_title'] = $this->lang->line('title_slider_image_add').' | '.$this->lang->line('site_title');
        if($this->input->post('submit_page') == "Submit")
          { 
              if (!empty($_FILES['Slider_image']['name']))
              {
                  $this->load->library('upload');
                  $config['upload_path'] = './uploads/slider-images';
                  $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                  $config['max_size'] = '500'; //in KB    
                  $config['encrypt_name'] = TRUE;               
                  // create directory if not exists
                  if (!@is_dir('uploads/slider-images')) {
                    @mkdir('./uploads/slider-images', 0777, TRUE);
                  }
                  $this->upload->initialize($config);                  
                  if ($this->upload->do_upload('Slider_image'))
                  {
                    $img = $this->upload->data();

                    //Code for compress image :: Start
                    $fileName = basename($img['file_name']);                   
                    $imageUploadPath = './uploads/slider-images/'. $fileName; 
                    $imageTemp = $_FILES["Slider_image"]["tmp_name"];
                    $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                    //Code for compress image :: End

                    $add_data['image'] = "slider-images/".$img['file_name'];    
                    $this->slider_image_model->addData('slider_image',$add_data);   
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' added a slider image');
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_add'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_add');
                  }
                  else
                  {
                    $data['Error'] = $this->upload->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                  }
              }
              redirect(base_url().ADMIN_URL.'/slider-image/view');                 
        }
        $this->load->view(ADMIN_URL.'/slider_image_add',$data);
      } else {
        redirect(base_url().ADMIN_URL);
      }
    }
    // edit user insurance
    public function edit(){
      if(in_array('slider-image~edit',$this->session->userdata("UserAccessArray"))) {
        $data['meta_title'] = $this->lang->line('title_slider_image_edit').' | '.$this->lang->line('site_title');
        // check if form is submitted 
        if($this->input->post('submit_page') == "Submit")
        { 
            if (!empty($_FILES['Slider_image']['name']))
              {
                  $this->load->library('upload');
                  $config['upload_path'] = './uploads/slider-images';
                  $config['allowed_types'] = 'gif|jpg|png|jpeg';  
                  $config['max_size'] = '500'; //in KB    
                  $config['encrypt_name'] = TRUE;               
                  // create directory if not exists
                  if (!@is_dir('uploads/slider-images')) {
                    @mkdir('./uploads/slider-images', 0777, TRUE);
                  }
                  $this->upload->initialize($config);                  
                  if ($this->upload->do_upload('Slider_image'))
                  {
                    $img = $this->upload->data();

                    //Code for compress image :: Start
                    $fileName = basename($img['file_name']);                   
                    $imageUploadPath = './uploads/slider-images/'. $fileName; 
                    $imageTemp = $_FILES["Slider_image"]["tmp_name"];
                    $compressedImage = $this->common_model->compressImage($imageTemp, $imageUploadPath); 
                    //Code for compress image :: End
                    
                    $add_data['image'] = "slider-images/".$img['file_name'];   
                    // code for delete existing image
                    if($this->input->post('uploadedSliderImage')){
                      @unlink(FCPATH.'uploads/'.$this->input->post('uploadedSliderImage'));
                    } 
                    $this->slider_image_model->updateData($add_data,'slider_image','entity_id',$this->input->post('entity_id'));
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' edited a slider image');
                    // $this->session->set_flashdata('page_MSG', $this->lang->line('success_update'));
                    $_SESSION['page_MSG'] = $this->lang->line('success_update');  
                  }
                  else
                  {
                    $data['Error'] = $this->upload->display_errors();
                    $this->form_validation->set_message('upload_invalid_filetype', $this->lang->line('upload_invalid_file_type'));
                  }   
              }   
              redirect(base_url().ADMIN_URL.'/slider-image/view');     
        }        
        $entity_id = ($this->uri->segment('4'))?$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='), $this->uri->segment(4))):$this->input->post('entity_id');
        $data['edit_records'] = $this->slider_image_model->getEditDetail($entity_id);
        $this->load->view(ADMIN_URL.'/slider_image_add',$data);
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
      
      $sortfields = array(2=>'status',3=>'entity_id');
      $sortFieldName = '';
      if(array_key_exists($sortCol, $sortfields))
      {
          $sortFieldName = $sortfields[$sortCol];
      }
      //Get Recored from model
      $grid_data = $this->slider_image_model->getGridList($sortFieldName,$sortOrder,$displayStart,$displayLength);
      $totalRecords = $grid_data['total'];        
      $records = array();
      $records["aaData"] = array(); 
      $nCount = ($displayStart != '')?$displayStart+1:1;
      foreach ($grid_data['data'] as $key => $val) {
          $doc = "'".$val->image."'";
          $sliderimg_editbtn = (in_array('slider-image~edit',$this->session->userdata("UserAccessArray"))) ? '<a class="btn btn-sm default-btn margin-bottom red" href="'.base_url().ADMIN_URL.'/'.$this->controller_name.'/edit/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($val->entity_id)).'" title="'.$this->lang->line('edit').'"><i class="fa fa-edit"></i></a>' : '';
          $sliderimg_disablebtn = (in_array('slider-image~ajaxdisable',$this->session->userdata("UserAccessArray"))) ? '<button onclick="disableDetail('.$val->entity_id.','.$val->status.')"  title="'.($val->status?''.$this->lang->line('inactive').'':''.$this->lang->line('active').'').' " class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-'.($val->status?'ban':'check').'"></i></button>' : '';
          $sliderimg_deletebtn = (in_array('slider-image~ajaxDelete',$this->session->userdata("UserAccessArray"))) ? '<button onclick="deleteDetail('.$val->entity_id.','.$doc.')"  title="'.$this->lang->line('delete').'" class="delete btn btn-sm default-btn margin-bottom red"><i class="fa fa-trash"></i></button>' : '';
          $records["aaData"][] = array(
              $nCount,
               '<img id="oldpic" class="sliderimg" width="70" height="50" src="'.base_url().'uploads/'.$val->image.'">',
              ($val->status)?$this->lang->line('active'):$this->lang->line('inactive'),
              $sliderimg_editbtn.$sliderimg_disablebtn.$sliderimg_deletebtn
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
            $this->slider_image_model->UpdatedStatus('slider_image',$entity_id,$this->input->post('status'));
            $status_txt = '';
            if($this->input->post('status') == 0) {
              $status_txt = 'activated';
            } else {
              $status_txt = 'deactivated';
            }
            $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' '.$status_txt.' a slider image');
        }
    }
    // method for deleting
    public function ajaxDelete(){
        $entity_id = ($this->input->post('entity_id') != '')?$this->input->post('entity_id'):'';
        $Image = ($this->input->post('image') != '')?$this->input->post('image'):'';
        $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' deleted a slider image');
        $this->slider_image_model->ajaxDelete('slider_image',$entity_id);
        @unlink(FCPATH.'uploads/'.$Image);
        // $this->session->set_flashdata('page_MSG', $this->lang->line('success_delete'));
        $_SESSION['page_MSG'] = $this->lang->line('success_delete');
    }
}
?>