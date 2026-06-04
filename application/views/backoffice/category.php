<?php $this->load->view(ADMIN_URL.'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/admin/plugins/multiselect/sumoselect.min.css"/>
<!-- <link rel="stylesheet" href="<?php echo base_url();?>assets/admin/reorder/rowReorder.dataTables.scss" /> -->
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
                       <?php echo $this->lang->line('menu_category'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                                <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('menu_category') ?>
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
                        <div class="portlet-title portlet-titlecenter">
                            <div class="caption"><?php echo $this->lang->line('menu_category') ?>
                            </div>
                            <?php if($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin'){ ?>
                                <div class="col-md-3">
                                    <select class="form-control sumo" name="restaurant_owner_id" id="restaurant_owner_id">
                                        <option value=""><?php echo $this->lang->line('select_restaurant'); ?></option>
                                        <?php if(!empty($restaurant_admins)){
                                            foreach ($restaurant_admins as $key => $value) { ?>
                                               <option value="<?php echo $value->restaurant_owner_id ?>" data-restaurant-id="<?php echo $value->entity_id; ?>"><?php echo $value->name ?></option>
                                            <?php } 
                                        } ?>
                                    </select>
                                </div>
                            <?php }else{ ?>
                                    <input type="hidden" name="restaurant_owner_id" id="restaurant_owner_id" value="">
                            <?php } ?>
                            <div class="actions c-dropdown">
                                <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="active"><i class="fa fa-check"></i> <?php echo $this->lang->line('active') ?></button>
                                    <button class="btn default-btn btn-sm danger-btn theme-btn" id="deactive"><i class="fa fa-ban"></i> <?php echo $this->lang->line('inactive') ?></button>
                                <?php } ?>
                                <?php if(in_array('category~add',$this->session->userdata("UserAccessArray"))) { ?>
                                    <button type="button" class="btn btn-sm default-btn danger-btn theme-btn dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><?php echo $this->lang->line('add') ?><span class="caret"></span>
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
                                    <table class="table table-striped table-bordered table-hover table-data" id="datatable_ajax">
                                        <thead>
                                            <tr role="row" class="heading">
                                                <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                                <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || ($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin')){ ?>
                                                    <th class="table-checkbox"><input type="checkbox" class="group-checkable"></th>
                                                <?php }?>
                                                <?php foreach ($Languages as $lang) {?>
                                                    <th><?php echo $this->lang->line('title') ?>&nbsp;(<?php echo $lang->language_slug;?>)</th>
                                                <?php } ?>
                                                <th><?php echo $this->lang->line('status') ?></th>
                                                <th><?php echo $this->lang->line('action') ?></th>
                                            </tr>
                                            <tr role="row" class="filter">
                                                <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || ($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin')){ ?>
                                                    <td></td>
                                                <?php }?> 
                                                <td></td>
                                                <?php foreach ($Languages as $lang) {?>
                                                    <td><input type="text" class="form-control form-filter input-sm" name="title_<?php echo $lang->language_slug;?>"></td>
                                                <?php } ?> 
                                                <td>
                                                    <select name="status" class="form-control form-filter input-sm">
                                                        <option value=""><?php echo $this->lang->line('all')?></option>
                                                        <option value="1"><?php echo $this->lang->line('active')?></option>
                                                        <option value="0"><?php echo $this->lang->line('inactive')?></option>                                                
                                                    </select>
                                                </td>
                                                <td>                                                
                                                    <button class="btn btn-sm red filter-submit" id="search" title="<?php echo $this->lang->line('search') ?>"><i class="fa fa-search"></i></button>
                                                    <button class="btn btn-sm red filter-cancel" id="reset" title="<?php echo $this->lang->line('reset') ?>"><i class="fa fa-refresh"></i></button>                      
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
<!-- <script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/reorder/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<script src="<?php echo base_url();?>assets/admin/plugins/multiselect/dashboard/jquery.sumoselect.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url();?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<!-- <script src="<?php echo base_url();?>assets/admin/scripts/datatable.js"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>assets/admin/reorder/dataTables.rowReorder.js"></script>
<?php if($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin') {
    $allowasmin = 'yes'; ?>
<script> var allowasmin = 'yes';</script>
<?php } else{
    $allowasmin = 'no'; ?>
<script> var allowasmin = 'no'; </script>    
<?php } ?>
<script>
    var current_user_role = '<?php echo $this->session->userdata('AdminUserType'); ?>';
    var category_count = <?php echo ($category_count)?$category_count:0; ?>;
    jQuery(document).ready(function() {           
    Layout.init(); // init current layout    
    var Datatable = $('#datatable_ajax').DataTable({
        "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
        "aoColumns": [
            { "bSortable": false },
            <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || ($this->session->userdata('AdminUserType') == 'MasterAdmin' || $this->session->userdata('AdminUserType') == 'Restaurant Admin' || $this->session->userdata('AdminUserType') == 'Branch Admin')){ ?>
            { "bSortable": false }, 
            <?php } ?>
            <?php foreach ($Languages as $lang) {?>
            { "bSortable": false },
            <?php } ?>
            { "bSortable": false },
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
        "bStateSave": false,
        "stateSaveCallback": function (oSettings, oData) {
            console.log(category_count);
            if(oSettings.aoData.length == 0 && category_count != 0 && oData.start >= category_count){
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
            oData.aaSorting = [[ 3, "desc" ]];
        },
        "fnDrawCallback": function(oSettings) {
            if($('#restaurant_owner_id').val() != '' || current_user_role == 'Restaurant Admin' || current_user_role == 'Branch Admin'){
                $('#datatable_ajax_info').css("top", "0");
                $('#datatable_ajax_paginate').hide();
                $('#datatable_ajax_length').hide();
            }else{
                $('#datatable_ajax_info').css("top", "15px");
                $('#datatable_ajax_paginate').show();  
                $('#datatable_ajax_length').show();  
            }
        },
        "bSort":false,          
        "processing": true,
        "bServerSide": true, // server side processing
        "bSortCellsTop": true,
        "sAjaxSource": "ajaxview", // ajax source             
        "sServerMethod": "POST",
        "rowReorder": true,
        "aaSorting": [[ 3, "desc" ]], // set first column as a default sort by asc 
        "columnDefs": [
            {className:"drag_move",targets:0}
        ],
        "fnServerParams": function ( aoData ) {
            aoData.push( 
                { "name": "restaurant_owner_id", "value": $('#restaurant_owner_id').val() },
                { "name": "restaurant_id_for_category", "value": $('#restaurant_owner_id').find(':selected').attr('data-restaurant-id') }
            );
        }        
    });    
    
    //***************Handle click on "Select all" control***************
    <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
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
    <?php } ?> 

   //***************Handle click on "Select all" control***************
    //reset datatable
    $('#reset').on('click', function(e) {
        e.preventDefault();
        $('.input-sm').val('');
        <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
            $('input:checkbox').removeAttr('checked');
        <?php } ?>
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
        <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
            $('input:checkbox').removeAttr('checked');
        <?php } ?>
        $('#datatable_ajax').DataTable().table().draw();
    }
    //re order 
    $('#datatable_ajax').on('row-reorder.dt', function (e, diff, edit) 
    {
        if(allowasmin=='yes')
        {
            var restaurant_owner_id = $('#restaurant_owner_id').val();
            var restaurant_entity_id = $('#restaurant_owner_id').find(':selected').attr('data-restaurant-id');
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
        }
        var dataid = [];
        $('.hidden-id').each(function(){
            dataid.push($(this).val());
        });
        jQuery.ajax({
          type : "POST",
          dataType : "json",
          url : 'ajaxReorder',
          data : {'dataid':dataid,'restaurant_owner_id':restaurant_owner_id,'restaurant_entity_id':restaurant_entity_id},
          success: function(response) {
            <?php if($allowasmin == 'yes') { ?>
                $('input:checkbox').removeAttr('checked');
            <?php } ?>
            $('#datatable_ajax').DataTable().table().draw();
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {           
            alert(errorThrown);
          }
        });
    });

    //activate multiple categories
    $('#active').click(function(e){
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
                          url : 'activeDeactiveMultiCat',
                          data : {'arrayData':CommissionIdComma, 'flag':'active'},
                          success: function(response) {
                            <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
                                $('input:checkbox').removeAttr('checked');
                            <?php } ?>
                            $('#datatable_ajax').DataTable().table().draw() 
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

    //deactivate multiple categories
    $('#deactive').click(function(e)
    {
        e.preventDefault();
        var records = Array();
        Datatable.$('input[type="checkbox"]').each(function()
        {
            if(this.checked){           
                records.push(this.value)
            } 
        });

        if(!jQuery.isEmptyObject(records)){            
            
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
                          url : 'activeDeactiveMultiCat',
                          data : {'arrayData':CommissionIdComma, 'flag':'deactive'},
                          success: function(response) {
                            <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
                                $('input:checkbox').removeAttr('checked');
                            <?php } ?>
                            $('#datatable_ajax').DataTable().table().draw() 
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
function deleteDetail(entity_id,content_id,image)
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
                  data : {'content_id':content_id,'entity_id':entity_id,'image':image},
                  success: function(response) {
                    <?php if(in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
                        $('input:checkbox').removeAttr('checked');
                    <?php } ?>
                    $('#datatable_ajax').DataTable().table().draw()  
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
function deleteAll(content_id,image, message,is_masterdata)
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
                data : {'content_id':content_id,'image':image},
                success: function(response) {
                  <?php if(in_array('category~ajaxDeleteAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
                    $('input:checkbox').removeAttr('checked');
                  <?php } ?>
                  // $('#datatable_ajax').DataTable().table().draw() 
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
function disable_record(ID,Status)
{
    var StatusVar = (Status==0)?"<?php echo $this->lang->line('active_module'); ?>":"<?php echo $this->lang->line('deactive_module'); ?>";
    bootbox.confirm({
        message: StatusVar,
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
                  data : {'entity_id':ID,'status':Status,'tblname':'category'},
                  success: function(response) {
                    <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
                        $('input:checkbox').removeAttr('checked');
                    <?php } ?>
                    $('#datatable_ajax').DataTable().table().draw() 
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
    var StatusVar = (Status==0)?"<?php echo $this->lang->line('active_module'); ?>":"<?php echo $this->lang->line('deactive_module'); ?>";
    bootbox.confirm({
        message: StatusVar,
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
                  data : {'content_id':ContentID,'status':Status,'tblname':'category'},
                  success: function(response) {
                    <?php if(in_array('category~ajaxDisableAll',$this->session->userdata("UserAccessArray")) || $allowasmin == 'yes') { ?>
                        $('input:checkbox').removeAttr('checked');
                    <?php } ?>
                    $('#datatable_ajax').DataTable().table().draw() 
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