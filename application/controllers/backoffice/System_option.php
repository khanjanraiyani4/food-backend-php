<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class System_option extends CI_Controller {	 
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(ADMIN_URL.'/home');
        }
        $this->load->model(ADMIN_URL.'/systemoption_model');
    }
    public function view() {
        if(in_array('system_option~view',$this->session->userdata("UserAccessArray"))) {
            $data['meta_title'] = $this->lang->line('titleadmin_systemoptions').' | '.$this->lang->line('site_title');
            if($this->input->post('SubmitSystemSetting') == "Submit" && $this->session->userdata('AdminUserType') == 'MasterAdmin')
            {
                if(!empty($_POST)){
                    $default_drivertip_val = NULL;
                    $systemOptionDefaultTip = array();
                    foreach ($_POST as $OptionSlug => $OptionValue) {
                        if($OptionSlug != "SubmitSystemSetting"){
                            if($OptionSlug == 'driver_tip_amount'){
                                $OptionValue = '';
                                foreach ($this->input->post('driver_tip_amount') as $key => $value) {
                                    foreach ($value as $k => $val) {
                                        if($val['tip_amount'] != ''){
                                            $OptionValue .= $val['tip_amount']."\r\n";
                                            $default_drivertip_val = $val['tip_amount'];
                                        }
                                        if(isset($val['default_driver_tip']) && $val['default_driver_tip'] != '') {
                                            $systemOptionDefaultTip = array(
                                                'OptionSlug'  => 'default_driver_tip',
                                                'OptionValue'  => $default_drivertip_val,
                                                'UpdatedBy'    => $this->session->userdata("adminID"),
                                                'UpdatedDate'  => date('Y-m-d h:i:s')
                                            );
                                        }
                                    }
                                }
                                if(empty($systemOptionDefaultTip)) {
                                    $systemOptionDefaultTip = array(
                                        'OptionSlug'  => 'default_driver_tip',
                                        'OptionValue'  => $default_drivertip_val,
                                        'UpdatedBy'    => $this->session->userdata("adminID"),
                                        'UpdatedDate'  => date('Y-m-d h:i:s')
                                    );
                                }
                            }
                            //Code for update the language table to set default language :: Start
                            if($OptionSlug == 'default_language') {
                                $this->db->update('languages',array('language_default'=>'0'));
                                $this->db->affected_rows();

                                $this->db->where('language_slug',$this->input->post('default_language'));
                                $this->db->update('languages',array('language_default'=>'1'));
                                $this->db->affected_rows();
                            }
                            //End

                            $systemOptionData[] = array(
                              'OptionSlug'  => $OptionSlug,
                              'OptionValue'  => $OptionValue,
                              'UpdatedBy'    => $this->session->userdata("adminID"),
                              'UpdatedDate'  => date('Y-m-d h:i:s')
                            );
                        }
                    } 
                    $systemOptionData[] = $systemOptionDefaultTip;
                }
                if(!empty($_FILES)){
                    foreach ($_FILES as $OptionSlug => $OptionValue) {
                        if($OptionSlug == 'language_file_mobile_app' && !empty($_FILES['language_file_mobile_app']['name'])){
                            /*Begin::Excel Upload*/
                            if (!empty($_FILES['language_file_mobile_app']['name'])){
                                $previous_file = $this->common_model->getSingleRow('system_option','OptionSlug',$OptionSlug);
                                $this->load->library('upload');
                                $config['upload_path'] = './uploads/language_import';
                                $config['allowed_types'] = 'xlsx';  
                                $config['max_size'] = '5120';
                                $config['encrypt_name'] = TRUE;
                                // create directory if not exists
                                if (!@is_dir('uploads/language_import')) {
                                  @mkdir('./uploads/language_import', 0777, TRUE);
                                }
                                $this->upload->initialize($config);
                                if ($this->upload->do_upload('language_file_mobile_app'))
                                {
                                    $file_data = $this->upload->data();
                                    $fileName = basename($file_data['file_name']);
                                    $OptionValue = "uploads/language_import/".$fileName;

                                    //removing old file
                                    if(!empty($previous_file)){
                                        @unlink(FCPATH.'/'.$previous_file->OptionValue);
                                    } 
                                }else{
                                    $error = array('error' => $this->upload->display_errors());
                                }
                            }
                            /*End::Excel Upload*/
                            if(empty($error)){
                                $systemOptionData[] = array(
                                    'OptionSlug'  => $OptionSlug,
                                    'OptionValue'  => $OptionValue,
                                    'UpdatedBy'    => $this->session->userdata("adminID"),
                                    'UpdatedDate'  => date('Y-m-d h:i:s')
                                );
                            }
                        }
                    }
                }
                if(!empty($_FILES)){
                    foreach ($_FILES as $OptionSlug => $OptionValue) {
                        if($OptionSlug == 'language_file_website' && !empty($_FILES['language_file_website']['name'])){
                            //Begin::Excel Upload
                            if (!empty($_FILES['language_file_website']['name'])){
                                $previous_file = $this->common_model->getSingleRow('system_option','OptionSlug',$OptionSlug);
                                $this->load->library('upload');
                                $config['upload_path'] = './uploads/language_import';
                                $config['allowed_types'] = 'xlsx';  
                                $config['max_size'] = '5120';
                                $config['encrypt_name'] = TRUE;
                                // create directory if not exists
                                if (!@is_dir('uploads/language_import')) {
                                  @mkdir('./uploads/language_import', 0777, TRUE);
                                }
                                $this->upload->initialize($config);
                                if ($this->upload->do_upload('language_file_website'))
                                {
                                    $file_data = $this->upload->data();
                                    $fileName = basename($file_data['file_name']);
                                    $OptionValue = "uploads/language_import/".$fileName;
                                    $this->Web_Translations("./uploads/language_import/".$fileName); //TEMP HIDE
                                    //removing old file
                                    if(!empty($previous_file)){
                                        @unlink(FCPATH.'/'.$previous_file->OptionValue);
                                    } 
                                }else{
                                    $error = array('error' => $this->upload->display_errors());
                                }
                            }
                            //End::Excel Upload
                            if(empty($error)){
                                $systemOptionData[] = array(
                                    'OptionSlug'  => $OptionSlug,
                                    'OptionValue'  => $OptionValue,
                                    'UpdatedBy'    => $this->session->userdata("adminID"),
                                    'UpdatedDate'  => date('Y-m-d h:i:s')
                                );
                            }
                        }
                    }
                }
                if(!empty($systemOptionData)){
                    $this->systemoption_model->upateSystemOption($systemOptionData);
                    $this->common_model->save_user_log($this->session->userdata("adminFirstname").' '.$this->session->userdata("adminLastname").' updated system options');
                }
                if(!empty($error)){
                    $_SESSION['file_error'] = $error['error'];
                }else{
                    // $this->session->set_flashdata('SystemOptionMSG', $this->lang->line('success_update'));
                    $_SESSION['SystemOptionMSG'] = $this->lang->line('success_update');
                }
                redirect(base_url().ADMIN_URL.'/system_option/view');
            }
            $data['currencies'] = $this->common_model->getCountriesCurrency();
            $data['countryArray'] = $this->common_model->list_country_codes();
            $data['languageArray'] = $this->common_model->getLanguages();
            $SystemOptionGroupList = $this->systemoption_model->getSystemOptionGroupList();
            $arrSystemOptions = array();
            if(!empty($SystemOptionGroupList)){
                foreach ($SystemOptionGroupList as $key => $groupData) {
                    // Get System options of Group
                    $systemOptions = $this->systemoption_model->getSystemOptionList($groupData->GroupID);
                    if(!empty($systemOptions)){
                        $arrSystemOptions[$groupData->GroupName] = $systemOptions;
                    }
                }
            }
            $data['default_drivertip_opt'] = $this->systemoption_model->getdefaultdrivertip();
            $data['arrSystemOptions'] = $arrSystemOptions;
            $this->db->select('OptionValue');
            $distance_inarr = $this->db->get_where('system_option',array('OptionSlug'=>'distance_in'))->first_row();
            //echo "<pre>"; print_r($distance_inarr); exit;
            $data['arrSystemOptions'] = $arrSystemOptions;
            $data['distance_inarr'] = $distance_inarr;
            $this->load->view(ADMIN_URL.'/system_option',$data);
        } else {
            redirect(base_url().ADMIN_URL);
        }
    }
    public function Web_Translations($path)
    {
        //$this->load->library('Excel');
        $this->load->library('excel');
        //read file from path
        //$objPHPExcel = PHPExcel_IOFactory::load($path);
        $reader = $this->excel->load("Xlsx");
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);
        $objPHPExcel = $reader->load($path);
        foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            foreach ($cellIterator as $cell) {
                $column = $cell->getColumn();
                $row = $cell->getRow();
                $data_value = (string) $cell->getValue();
                $data_value = str_replace('"', "'", $data_value);
                if ($row == 1)
                {
                    $header[$row][$column] = $data_value;
                } 
                else if ($row > 1)
                {
                    $arr_data[$row][$column] = $data_value;
                }
            }
        }
        //get only the Cell Collection
        /*$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
        foreach ($cell_collection as $cell)
        {
            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
            $data_value = (string)$objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
            //header will/should be in row 1 only. of course this can be modified to suit your need.
            if ($row == 1) 
            {
                $header[$row][$column] = $data_value;
            } 
            else if ($row > 1)
            {
                $arr_data[$row][$column] = $data_value;
            }
            
        }*/
        $d=1;
        $add_data = array();
        rename('./application/language/english/messages_lang.php', './application/language/english/messages_lang_'.strtotime("now").'.php');
        rename('./application/language/french/messages_lang.php', './application/language/french/messages_lang_'.strtotime("now").'.php');
        rename('./application/language/arabic/messages_lang.php', './application/language/arabic/messages_lang_'.strtotime("now").'.php');
        $myfile = fopen("./application/language/english/messages_lang.php", "w");
        $myfile2 = fopen("./application/language/french/messages_lang.php", "w");
        $myfile3 = fopen("./application/language/arabic/messages_lang.php", "w");
        fwrite($myfile, "<?php\ndefined('BASEPATH') OR exit('No direct script access allowed');\n\n" );
        fwrite($myfile2, "<?php\ndefined('BASEPATH') OR exit('No direct script access allowed');\n\n" );
        fwrite($myfile3, "<?php\ndefined('BASEPATH') OR exit('No direct script access allowed');\n\n" );
        for($rowcount=1; $rowcount<=count($arr_data); $rowcount++)
        {
            $d++;
            if (trim($arr_data[$d]['B']) != '') {
                $add_data['en'] = trim($arr_data[$d]['B']);
                fwrite($myfile,"\$lang['".$arr_data[$d]['A']."'] =\"".$add_data['en']."\";".PHP_EOL);
            }
            if (trim($arr_data[$d]['C']) != '') {
                $add_data['fr'] = trim($arr_data[$d]['C']);
                fwrite($myfile2,"\$lang['".$arr_data[$d]['A']."'] =\"".$add_data['fr']."\";".PHP_EOL);
            }
            if (trim($arr_data[$d]['D']) != '') {
                $add_data['ar'] = trim($arr_data[$d]['D']);
                fwrite($myfile3,"\$lang['".$arr_data[$d]['A']."'] =\"".$add_data['ar']."\";".PHP_EOL);
            }
        }
        fclose($myfile);
        fclose($myfile2);
        fclose($myfile3);
    }
}