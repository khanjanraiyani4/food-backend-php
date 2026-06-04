<?php
class systemoption_model extends CI_Model {
    function __construct()
    {
        parent::__construct();		
    }
    function getSystemOptionGroupList()
    {
        return $this->db->select('GroupID,GroupName')->where('status','1')->order_by('sort_order','asc')->get('system_option_group')->result();
    }
    function getSystemOptionList($group_id)
    {
        return $this->db->select('SystemOptionID,OptionName,OptionSlug,OptionValue,FieldType,Description,IsHidden')->where('GroupID',$group_id)->where('IsHidden','0')->order_by('sort_order','asc')->get('system_option')->result();
    }
    function upateSystemOption($systemOptionData)
    {
        $this->db->update_batch('system_option', $systemOptionData, 'OptionSlug');
    }
    function getdefaultdrivertip()
    {
        return $this->db->get_where('system_option',array('OptionSlug'=>'default_driver_tip'))->first_row();
    }
}
?>