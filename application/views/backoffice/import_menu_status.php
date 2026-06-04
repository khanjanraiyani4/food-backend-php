<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
<?php $this->load->view(ADMIN_URL.'/sidebar'); ?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                    <?php echo $this->lang->line('imported_menu') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name.'/view_menu';?>">
                            <?php echo $this->lang->line('menu') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('imported_menu') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('imported_menu_list'); ?> <?php echo $this->session->userdata('import_data')['restaurant']->name; ?> </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container import-users" style="overflow-x:auto;">
                            <?php                                         
                            if($_SESSION['PageMSG'])
                            { ?>
                                <div class="alert alert-success">
                                     <?php echo $_SESSION['PageMSG'];
                                     unset($_SESSION['PageMSG']);
                                     ?>
                                </div>
                            <?php } if($_SESSION['Import_users_limit'])
                            { ?>
                                <div class="alert alert-danger">
                                     <?php echo $_SESSION['Import_users_limit'];
                                     unset($_SESSION['Import_users_limit']);
                                     ?>
                                </div>
                            <?php } ?>
                                <table class="table">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th class="table-checkbox">#</th>                                        
                                        <th>Status</th>
                                        <th>Reason</th>
                                        <?php $keys = array(); 
                                        if (!empty($this->session->userdata('import_data')['header'][2])) {
                                            foreach ($this->session->userdata('import_data')['header'][2] as $key => $value) { 
                                                $keys[] = $key;?>
                                                <th><?php echo $value; ?></th>
                                            <?php }
                                        } ?>
                                    </tr>
                                    </thead>                                        
                                    <tbody>
                                        <?php $i = 0;
                                        foreach ($this->session->userdata('import_data')['arr_data'] as $akey => $avalue) {  ++$i; ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <?php $status = "Failed";
                                                $Errors = '';
                                                if (!empty($this->session->userdata('import_data')['Import'])) {
                                                    foreach ($this->session->userdata('import_data')['Import'] as $key => $value) {
                                                        if ($akey == $key+2) {
                                                            if ($value[0] == "New Added") {
                                                                $status = "New Added";
                                                            }
                                                            else if ($value[0] == "Updated") {
                                                                $status = "Updated";
                                                            }
                                                            else
                                                            {
                                                                $status = "Failed";
                                                            }
                                                        }
                                                    } 
                                                } ?>
                                                <td><?php echo $status; ?></td>
                                                <?php if (!empty($this->session->userdata('import_data')['Import'])) {
                                                    foreach ($this->session->userdata('import_data')['Import'] as $key => $value) {
                                                        if ($akey == $key+2) {
                                                            $Errors = implode(" ", $value);
                                                        }
                                                    }  
                                                } ?>
                                                <td><?php echo $Errors; ?></td>
                                                <?php if (!empty($keys)) {
                                                    foreach ($keys as $kkey => $kval) { ?>
                                                        <td><?php echo ($avalue[$kval])?$avalue[$kval]:''; ?></td>
                                                    <?php }
                                                } ?>                                                
                                            <tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script>
jQuery(document).ready(function() {           
    Layout.init(); // init current layout    
});
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>