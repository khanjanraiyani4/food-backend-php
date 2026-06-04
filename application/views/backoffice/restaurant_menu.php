<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<style>
    #dropdown2.dropdown-menu{
        left: 30%;
    }
</style>
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
                        <?php echo $this->lang->line('manage_res_menu'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                            <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('menus') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <?php if(in_array('restaurant_menu~import_menu',$this->session->userdata("UserAccessArray"))) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box red">
                            <div class="portlet-title portlet-titlecenter">
                                <div class="caption"><?php echo $this->lang->line('menus') ?>&nbsp;(<?php echo $this->lang->line('title_multiple_items') ?>)</div>
                                <div class="actions c-dropdown">
                                    <a href="<?php  echo base_url().ADMIN_URL.'/restaurant/download_sample'; ?>" name="download_sample" id="download_sample" value="Download Sample File" class="btn btn-sm default-btn danger-btn theme-btn" style="text-decoration: underline; font-weight: bold; font-size: 14px;" ><?php echo $this->lang->line('sample_file_download'); ?></a>
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <div class="form-body">                                    
                                    <?php if(isset($_SESSION['Import_Error']))
                                    { ?>
                                        <div class="alert alert-danger">
                                             <?php echo $_SESSION['Import_Error'];
                                             unset($_SESSION['Import_Error']);
                                             ?>
                                        </div>
                                    <?php } ?>
                                    <?php if(validation_errors()){?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors();?>
                                        </div>
                                    <?php } ?>
                                    <form action="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name ?>/import_menu" id="form_add_import" name="form_add_import" method="post" class="horizontal-form" enctype="multipart/form-data" >
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label"><?php echo $this->lang->line('menu_file') ?><span class="required">*</span></label>
                                                    <input type="file" name="import_tax" id="import_tax" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"> 
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success btn-sm default-btn danger-btn theme-btn" style="    margin: 20px 0px;"><?php echo $this->lang->line('import_menu'); ?></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
            <?php } ?>
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title portlet-titlecenter">
                            <div class="caption"><?php echo $this->lang->line('menus') ?></div>
                            <?php if(!empty($restaurant_adminarr)){ ?>
                             
                            <?php if(count($restaurant_adminarr)>0){ ?>
                            <div class="col-md-3">                           
                            <select class="form-control sumo" name="restaurant_owner_id" id="restaurant_owner_id">
                                <option value=""><?php echo $this->lang->line('select_restaurant'); ?></option>
                                <?php
                                    foreach ($restaurant_adminarr as $key => $value) { ?>
                                       <option value="<?php echo $value->restaurant_owner_id ?>" resdata="<?php echo $value->entity_id ?>"><?php echo $value->name; ?></option>
                                <?php } ?>
                            </select>
                            </div>
                            <?php } else { ?>
                            <input type="hidden" name="restaurant_owner_id" id="restaurant_owner_id" value="<?php echo $restaurant_adminarr[0]->restaurant_owner_id;?>" resdata="<?php echo $restaurant_adminarr[0]->entity_id ?>" >
                            
                            <?php }} ?>
                            <div class="actions c-dropdown">
                                <?php if(in_array('restaurant_menu~ajaxDeleteAll',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="delete_menu"><i class="fa fa-trash"></i> <?php echo $this->lang->line('delete') ?></button>
                                <?php } ?>
                                <?php if(in_array('restaurant_menu~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="active_menu"><i class="fa fa-check"></i> <?php echo $this->lang->line('active') ?></button>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="deactive_menu"><i class="fa fa-ban"></i> <?php echo $this->lang->line('inactive') ?></button>
                                <?php } ?>
                                <?php if(in_array('restaurant_menu~add_menu',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button type="button" class="btn btn-sm danger-btn theme-btn default-btn dropbtn" onclick="myFunction('dropdown2')"><?php echo $this->lang->line('add_single_item') ?> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown2-content" id="dropdown2">
                                        <?php foreach ($Languages as $lang) {
                                            $langname = ($lang->language_name == 'English')?$this->lang->line('english'):(($lang->language_slug == 'ar')?$this->lang->line('arabic'):$this->lang->line('french'));
                                        ?>
                                        <li><a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name.'/add_menu/'.$lang->language_slug?>"><?php echo $langname; ?></a></li>
                                        <?php } ?>
                                    </ul>

                                    <button type="button" class="btn btn-sm danger-btn theme-btn default-btn dropbtn" onclick="myFunction('dropdown1')"><?php echo $this->lang->line('add_combo_item') ?> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown2-content pull-right" id="dropdown1">
                                        <?php foreach ($Languages as $lang) {
                                            $langname = ($lang->language_name == 'English')?$this->lang->line('english'):(($lang->language_slug == 'ar')?$this->lang->line('arabic'):$this->lang->line('french'));
                                        ?>
                                        <li><a href="<?php echo base_url().ADMIN_URL.'/'.$this->controller_name.'/add_combo_menu_item/'.$lang->language_slug?>"><?php echo $langname; ?></a></li>
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
                            <div id="delete-msg" class="alert alert-success hidden">
                                 <?php echo $this->lang->line('success_delete');?>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                            <th class="table-checkbox"><input type="checkbox" class="group-checkable"></th>
                                            <?php foreach ($Languages as $lang) {?>
                                                <th><?php echo $this->lang->line('title') ?>&nbsp;(<?php echo $lang->language_slug;?>)</th>
                                            <?php } ?>
                                            <th><?php echo $this->lang->line('price') ?></th>
                                            <th><?php echo $this->lang->line('restaurant') ?>/<?php echo $this->lang->line('branch') ?></th>
                                            <th><?php echo $this->lang->line('combo_item') ?></th>
                                            <th><?php echo $this->lang->line('status') ?></th>
                                            <th><?php echo $this->lang->line('stock') ?></th>
                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td></td>
                                            <?php foreach ($Languages as $lang) {?>
                                                <td><input type="text" class="form-control form-filter input-sm" name="title_<?php echo $lang->language_slug;?>"></td>
                                            <?php } ?> 
                                            <td><input type="text" class="form-control form-filter input-sm" name="price"></td>   
                                            <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>
                                            <td>
                                                <select name="combo_item" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('all')?></option>
                                                    <option value="1"><?php echo $this->lang->line('yes')?></option>
                                                    <option value="0"><?php echo $this->lang->line('no')?></option>                                                
                                                </select>
                                            </td> 
                                            <td>
                                                <select name="status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('all')?></option>
                                                    <option value="1"><?php echo $this->lang->line('active')?></option>
                                                    <option value="0"><?php echo $this->lang->line('inactive')?></option>                                                
                                                </select>
                                            </td>
                                            <td>
                                             <select name="stock" class="form-control form-filter input-sm">
                                                <option value=""><?php echo $this->lang->line('all')?></option>
                                                <option value="1"><?php echo $this->lang->line('in_stock')?></option>
                                                <option value="0"><?php echo $this->lang->line('out_stock')?></option>                                                                    
                                            </select>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <button class="btn btn-sm red filter-submit" title="<?php echo $this->lang->line('search') ?>" id="search"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm red filter-cancel" id="reset" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>
                                            </td>
                                        </tr>
                                      
                                        </thead>                                        
                                        <tbody class="order-tbl-action">
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
<!-- <script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/reorder/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<!-- <script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/reorder/dataTables.rowReorder.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
var current_user_role = '<?php echo $this->session->userdata('AdminUserType'); ?>';
var menu_count = <?php echo ($menu_count)?$menu_count:0; ?>;
jQuery(document).ready(function() {
    
    $(document).on('click',function(e){
        if(!$(e.target).closest('.dropbtn').length)
            $('.dropbtn').next().removeClass('show');
    });
    Layout.init(); // init current layout
    var sProcessing = "<img src='<?php echo base_url();?>assets/admin/img/loading-spinner-grey.gif'/><span>&nbsp;&nbsp;Loading...</span>";
    var Datatable = $('#datatable_ajax').DataTable({
        "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
        "aoColumns": [
            { "bSortable": false },
            { "bSortable": false },
            <?php foreach ($Languages as $lang) {?>
            { "bSortable": false },
            <?php } ?>
            null,
            null,
            null,
            { "bSortable": false },
            null,
            { "bSortable": false }
        ],
        "sPaginationType": "bootstrap_full_number",
        "oLanguage":{
            "sProcessing": sProcessing,
            "sLengthMenu": sLengthMenu,
            "sInfo": sInfo,
            "sInfoEmpty": '', //sInfoEmpty,
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
        "processing": true,        
        "bStateSave": false,
        "stateSaveCallback": function (oSettings, oData) {
            if(oSettings.aoData.length == 0 && menu_count != 0 && oData.start >= menu_count){
                oData.start = 0;
                localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
                location.reload();
                //grid.getDataTable().fnDraw();
            } else {
                localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
            }
        },
        "stateLoadCallback": function (oSettings, callback) {
            var data = localStorage.getItem('DataTables_' + window.location.pathname);
            return JSON.parse(data);
        },
        "fnStateLoadParams": function (oSettings, oData) {
            oData.aaSorting = [[ 7, "desc" ]];
        },
        "fnDrawCallback": function(oSettings) {
            if(($('#restaurant_owner_id').val() != undefined && $('#restaurant_owner_id').val() != '') || current_user_role == 'Restaurant Admin' || current_user_role == 'Branch Admin') {
                $('#datatable_ajax_info').css("top", "0");
                $('#datatable_ajax_paginate').hide();
                $('#datatable_ajax_length').hide();
            }else{
                $('#datatable_ajax_info').css("top", "15px");
                $('#datatable_ajax_paginate').show();  
                $('#datatable_ajax_length').show();
            }
        },
        "bSortCellsTop": true,
        "bServerSide": true, // server side processing
        //"bPaginate" : false,
        "sAjaxSource": "ajaxviewMenu", // ajax source
        "sServerMethod": "POST",
        "fnServerParams": function ( aoData )
        {
            var element = $('#restaurant_owner_id').find('option:selected'); 
            var rest_id = element.attr("resdata");
            aoData.push( { "name": "restaurant_owner_id", "value": $('#restaurant_owner_id').val()} );
            aoData.push( { "name": "rest_id", "value": rest_id} );
        },
        "rowReorder": true,
        "aaSorting": [[ 7, "desc" ]], // set first column as a default sort by asc
        "columnDefs": [
            {className:"drag_move",targets:0}
        ],
    });
    
    //***************Handle click on "Select all" control***************
    $('.group-checkable').on('click', function(){
      // Get all rows with search applied
      var rows = Datatable.rows({ 'search': 'applied' }).nodes();
      // Check/uncheck checkboxes for all rows in the table
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    // Handle click on checkbox to set state of "Select all" control
    $('#datatable_ajax tbody').on('change', 'input[type="checkbox"]', function(){
      // If checkbox is not checked
      if(!this.checked){
         var el = $('.group-checkable').get(0);
         // If "Select all" control is checked and has 'indeterminate' property
         if(el && el.checked && ('indeterminate' in el)){
            // Set visual state of "Select all" control
            // as 'indeterminate'
            el.indeterminate = true;
          }
       }
    });

    //***************Handle click on "Select all" control***************
    //reset datatable
    $('#reset').on('click', function(e) {
        e.preventDefault();
        $('.input-sm').val('');
        $('input:checkbox').removeAttr('checked');
        $('#datatable_ajax').DataTable().columns().search( '' ).draw();
    });
    //search in datatable
    $('input.form-filter, select.form-filter').keydown(function(e) 
    {
        if (e.keyCode == 13) 
        {
           e.preventDefault();
           search_result();
        }
    });
    $('#search').on('click', function(e) {
        e.preventDefault();
        search_result();
    });
    $('#restaurant_owner_id').on('change', function(e) {
        e.preventDefault();
        $('input:checkbox').removeAttr('checked'); 
        $('#datatable_ajax').DataTable().table().draw();
    });
    function search_result()
    {
        var params = {};
        $('.filter').find('.input-sm').each(function(i) {       
            //var name = $(this).attr("name");            
            params[i] = $(this).val();   
        });   
        $.each(params, function(i, val) {
            $('#datatable_ajax').DataTable().column(i).search(val ? val : '', false, false);            
        });
        $('input:checkbox').removeAttr('checked');     
        $('#datatable_ajax').DataTable().table().draw();
    }
    //re order 
    $('#datatable_ajax').on('row-reorder.dt', function (e, diff, edit) 
    {
        var restaurant_owner_id = $('#restaurant_owner_id').val();
        if(!restaurant_owner_id){
            bootbox.alert({
                message: "<?php echo $this->lang->line('select_res_admin'); ?>",
                buttons: {
                    ok: {
                        label: "<?php echo $this->lang->line('ok'); ?>",
                    }
                }
            });
            return false;
        }
        var dataid = [];
        $('.hidden-id').each(function(){
            dataid.push($(this).val());
        });

        jQuery.ajax({
          type : "POST",
          dataType : "json",
          url : 'ajaxReorder',
          data : {'dataid':dataid,'restaurant_owner_id':restaurant_owner_id},
          success: function(response) {
            $('input:checkbox').removeAttr('checked');
            $('#datatable_ajax').DataTable().table().draw();
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {           
            alert(errorThrown);
          }
        });
    });

    //activate multiple restaurants
    $('#active_menu').click(function(e)
    {
        e.preventDefault();
        var records = Array();
        Datatable.$('input[type="checkbox"]').each(function()
        {
            if(this.checked){           
                records.push(this.value)
            } 
        }); 
        if(!jQuery.isEmptyObject(records))
        {   
            var CommissionIdComma = records.join(",");
            bootbox.confirm({
                message: "<?php echo $this->lang->line('active_module'); ?>",
                buttons: {
                    confirm: {
                        label: "<?php echo $this->lang->line('ok'); ?>",
                    },
                    cancel: {
                        label: "<?php echo $this->lang->line('cancel'); ?>",
                    }
                },   
                callback: function (activeConfirm) {  
                    if (activeConfirm) {
                        jQuery.ajax({
                          type : "POST",
                          url : 'activeDeactiveMultiRes',
                          data : {'arrayData':CommissionIdComma, 'flag':'active','tab':'menu'},
                          success: function(response) {
                            $('input:checkbox').removeAttr('checked');
                            $('#datatable_ajax').DataTable().table().draw();                        
                          },
                          error: function(XMLHttpRequest, textStatus, errorThrown) {           
                            alert(errorThrown);
                          }
                       });
                    }
                }
            });
        }else{
            bootbox.alert({
                message: "<?php echo $this->lang->line('checkbox'); ?>",
                buttons: {
                    ok: {
                        label: "<?php echo $this->lang->line('ok'); ?>",
                    }
                }
            });
        }        
    });
    //deactivate multiple restaurants
    $('#deactive_menu').click(function(e){
        e.preventDefault();
        var records = Array();
        Datatable.$('input[type="checkbox"]').each(function()
        {
            if(this.checked){           
                records.push(this.value)
            } 
        }); 
        if(!jQuery.isEmptyObject(records))
        {            
            var CommissionIdComma = records.join(",");
            bootbox.confirm({
                message: "<?php echo $this->lang->line('deactive_module'); ?>",
                buttons: {
                    confirm: {
                        label: "<?php echo $this->lang->line('ok'); ?>",
                    },
                    cancel: {
                        label: "<?php echo $this->lang->line('cancel'); ?>",
                    }
                },   
                callback: function (deactiveConfirm) {  
                    if (deactiveConfirm) {
                        jQuery.ajax({
                          type : "POST",
                          url : 'activeDeactiveMultiRes',
                          data : {'arrayData':CommissionIdComma, 'flag':'deactive','tab':'menu'},
                          success: function(response) {
                            $('input:checkbox').removeAttr('checked');
                            $('#datatable_ajax').DataTable().table().draw();
                          },
                          error: function(XMLHttpRequest, textStatus, errorThrown) {           
                            alert(errorThrown);
                          }
                       });
                    }
                }
            });
        }else{
            bootbox.alert({
                message: "<?php echo $this->lang->line('checkbox'); ?>",
                buttons: {
                    ok: {
                        label: "<?php echo $this->lang->line('ok'); ?>",
                    }
                }
            });
        }        
    });
    //delete multiple
    $('#delete_menu').click(function(e){
        e.preventDefault();
        var records = Array();
        Datatable.$('input[type="checkbox"]').each(function()
        {
            if(this.checked){           
                records.push(this.value)
            } 
        });  
        if(!jQuery.isEmptyObject(records))
        {            
            var CommissionIdComma = records.join(",");
            bootbox.confirm({
                message: "<?php echo $this->lang->line('delete_module'); ?>",
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
                          url : 'DeleteMultiRes',
                          data : {'arrayData':CommissionIdComma},
                          success: function(response) {                        
                            $('input:checkbox').removeAttr('checked');
                            $('#datatable_ajax').DataTable().table().draw();
                          },
                          error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                          }
                        });
                    }
                }
            });
        }else{
            bootbox.alert({
                message: "<?php echo $this->lang->line('checkbox'); ?>",
                buttons: {
                    ok: {
                        label: "<?php echo $this->lang->line('ok'); ?>",
                    }
                }
            });
        }        
    });
   
   $('#datatable_ajax_filter').addClass('hide'); 
});
// method for deleting
function deleteDetail(entity_id,content_id)
{   
     bootbox.confirm({
        message: "<?php echo $this->lang->line('delete_module'); ?>",
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
                  url : 'ajaxDelete',
                  data : {'tblname':'restaurant_menu_item','entity_id':entity_id,'content_id':content_id},
                  success: function(response) {
                    $('input:checkbox').removeAttr('checked');
                    $('#datatable_ajax').DataTable().table().draw(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
function deleteAll(content_id, message,is_masterdata)
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
                dataType : "json",
                url : 'ajaxDeleteAll',
                data : {'tblname':'restaurant_menu_item','content_id':content_id},
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
function disable_record(entity_id,Status)
{
    var StatusVar = (Status==0)?"<?php echo $this->lang->line('active'); ?>":"<?php echo $this->lang->line('inactive'); ?>";
    bootbox.confirm({
        message: "<?php echo $this->lang->line('alert_msg'); ?> "+StatusVar+"?",
        buttons: {
            confirm: {
                label: "<?php echo $this->lang->line('ok'); ?>",
            },
            cancel: {
                label: "<?php echo $this->lang->line('cancel'); ?>",
            }
        }, 
        callback: function (disableConfirm) {  
          if (disableConfirm) {
              jQuery.ajax({
                type : "POST",
                dataType : "json",
                url : 'ajaxDisable',
                data : {'entity_id':entity_id,'status':Status,'tblname':'restaurant_menu_item'},
                success: function(response) {
                    $('input:checkbox').removeAttr('checked');
                    $('#datatable_ajax').DataTable().table().draw();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {           
                  alert(errorThrown);
                }
             });
          }
        }
    });
}
function disableAll(ContentID,Status,is_masterdata)
{
    if(is_masterdata=='1')    
    {
        return false;
    }
    var StatusVar = (Status==0)?"<?php echo $this->lang->line('active'); ?>":"<?php echo $this->lang->line('inactive'); ?>";
    bootbox.confirm({
        message: "<?php echo $this->lang->line('alert_msg'); ?> "+StatusVar+"?",
        buttons: {
            confirm: {
                label: "<?php echo $this->lang->line('ok'); ?>",
            },
            cancel: {
                label: "<?php echo $this->lang->line('cancel'); ?>",
            }
        }, 
        callback: function (disableConfirm) {  
          if (disableConfirm) {
              jQuery.ajax({
                type : "POST",
                dataType : "json",
                url : 'ajaxDisableAll',
                data : {'content_id':ContentID,'status':Status,'tblname':'restaurant_menu_item'},
                success: function(response) {
                    $('input:checkbox').removeAttr('checked');
                    $('#datatable_ajax').DataTable().table().draw();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {           
                  alert(errorThrown);
                }
             });
          }
        }
    });
}
function myFunction(id) {
    var dropdowns = document.getElementsByClassName("dropdown2-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
        var openDropdown = dropdowns[i];
        openDropdown.classList.remove('show');
    }
    document.getElementById(id).classList.toggle("show");
}
function stockAll(ContentID,stock)
{
    //if (stock != 0){
        var StatusVar = (stock==0)?"<?php echo $this->lang->line('in_stock'); ?>":"<?php echo $this->lang->line('out_stock'); ?>";
        bootbox.confirm({
            message: "<?php echo $this->lang->line('stock_alert_msg'); ?> "+StatusVar+"?",
            buttons: {
                confirm: {
                    label: "<?php echo $this->lang->line('yes'); ?>",
                },
                cancel: {
                    label: "<?php echo $this->lang->line('no'); ?>",
                }
            }, 
            callback: function (disableConfirm) {  
              if (disableConfirm) {
                  jQuery.ajax({
                    type : "POST",
                    dataType : "json",
                    url : 'ajaxStockUpdate',
                    data : {'content_id':ContentID,'stock':stock,'tblname':'restaurant_menu_item'},
                    success: function(response) {
                        $('input:checkbox').removeAttr('checked');
                        $('#datatable_ajax').DataTable().table().draw();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {           
                      alert(errorThrown);
                    }
                 });
              }
            }
        });
    //}
    /*else
    {
        bootbox.alert("<?php echo $this->lang->line('in_stock_alert'); ?>");
    }*/
}
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>