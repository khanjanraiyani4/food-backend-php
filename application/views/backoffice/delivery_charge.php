<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL.'/sidebar');?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                       <?php echo $this->lang->line('title_delivery_charges'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                                <i class="fa fa-angle-right"></i>
                            </li>
                            <li>
                                <?php echo $this->lang->line('title_delivery_charges') ?>
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
                                <div class="caption"><?php echo $this->lang->line('title_delivery_charges') ?></div>
                                <div class="actions c-dropdown">
                                    <?php if(in_array('delivery_charge~add',$this->session->userdata("UserAccessArray"))) { ?>
                                        <button type="button" class="btn btn-sm default-btn danger-btn theme-btn dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="false"><?php echo $this->lang->line('add') ?><span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach ($Languages as $lang) {
                                                $langname = ($lang->language_name == 'English')?$this->lang->line('english'):(($lang->language_slug == 'ar')?$this->lang->line('arabic'):$this->lang->line('french'));
                                            ?>
                                            <li><a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name.'/add/'.$lang->language_slug?>"><?php echo $langname; ?></a></li>
                                            <?php } ?>
                                        </ul>
                                    <?php } ?>                                    
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <?php                                         
                                    if(isset($_SESSION['page_MSG']))
                                    { ?>
                                        <div class="alert alert-success">
                                             <?php echo $_SESSION['page_MSG'];
                                             unset($_SESSION['page_MSG']);
                                             ?>
                                        </div>
                                    <?php } ?>
                                    <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                        <thead>
                                            <tr role="row" class="heading">
                                                <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                                <th><?php echo $this->lang->line('res_name') ?></th>
                                                <th><?php echo $this->lang->line('area_name') ?></th>
                                                <th><?php echo $this->lang->line('price') ?></th>
                                                <th><?php echo $this->lang->line('action') ?></th>
                                            </tr>
                                            <tr role="row" class="filter">
                                                <td></td>  
                                                <td><input type="text" class="form-control form-filter input-sm" name="res_name"></td>                                     
                                                <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                                <td><input type="text" class="form-control form-filter input-sm" name="price"></td>
                                                
                                                <td style="white-space: nowrap;">     
                                                    <button class="btn btn-sm red filter-submit" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                    <button class="btn btn-sm red filter-cancel" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
                                                </td>
                                            </tr>
                                        </thead>                                        
                                    <tbody>
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
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script>
<script>
    var grid;
    var delivery_charge_count = <?php echo ($delivery_charge_count)?$delivery_charge_count:0; ?>;
    jQuery(document).ready(function() {           
    Layout.init(); // init current layout    
    grid = new Datatable();
    grid.init({
        src: $("#datatable_ajax"),
        onSuccess: function(grid) {
            // execute some code after table records loaded
        },
        onError: function(grid) {
            // execute some code on network or other general error  
        },
        dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
            
                "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
                "aoColumns": [
                    { "bSortable": false },
                    null,
                    null,
                    null,
                    { "bSortable": false }
                ],
                "sPaginationType": "bootstrap_full_number",
                "oLanguage":{
                    "sProcessing": sProcessing,
                    "sLengthMenu": sLengthMenu,
                    "sInfo": sInfo,
                    "sInfoEmpty":'', //sInfoEmpty,
                    "sGroupActions":sGroupActions,
                    "sAjaxRequestGeneralError": sAjaxRequestGeneralError,
                    "sEmptyTable": sEmptyTable,
                    "sZeroRecords":sZeroRecords,
                    "oPaginate": {
                        "sPrevious": sPrevious,
                        "sNext": sNext,
                        "sPage": sPage,
                        "sPageOf":sPageOf,
                        "sFirst": sFirst,
                        "sLast": sLast
                    }
                },
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                if(oSettings.aoData.length == 0 && delivery_charge_count != 0 && oData.iStart >= delivery_charge_count){
                    oData.iStart = 0;
                    localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
                    location.reload();
                    //grid.getDataTable().fnDraw();
                } else {
                    localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
                }
            },
            "fnStateLoad": function (oSettings) {
                var data = localStorage.getItem('DataTables_' + window.location.pathname);
                return JSON.parse(data);
            },
            "fnStateLoadParams": function (oSettings, oData) {
                oData.aaSorting = [[ 3, "desc" ]];
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": "ajaxview", // ajax source
            "aaSorting": [[ 3, "desc" ]] // set first column as a default sort by asc
        }
    });            
    $('#datatable_ajax_filter').addClass('hide');
    $('input.form-filter, select.form-filter').keydown(function(e) 
    {
        if (e.keyCode == 13) 
        {
            grid.addAjaxParam($(this).attr("name"), $(this).val());
            grid.getDataTable().fnDraw(); 
        }
    });
});
// method for deleting
function deleteDetail(entity_id, message,is_masterdata)
{
    if(is_masterdata=='1')
    {
        return false;
    }       
    bootbox.confirm({
        message: message,
        buttons: {
            confirm: {
                label: "<?php echo $this->lang->line('ok'); ?>",
            },
            cancel: {
                label: "<?php echo $this->lang->line('cancel'); ?>",
            }
        },
        callback: function (deleteConfirm) { 
            if (deleteConfirm) {
                jQuery.ajax({
                  type : "POST",
                  dataType : "html",
                  url : 'ajaxDeleteAll',
                  data : {'entity_id':entity_id},
                  success: function(response) {
                    //grid.getDataTable().fnDraw(); 
                    location.reload(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>