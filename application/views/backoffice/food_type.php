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
                       <?php echo $this->lang->line('food_type'); ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url().ADMIN_URL;?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                                <i class="fa fa-angle-right"></i>
                            </li>
                            <li>
                                <?php echo $this->lang->line('food_type') ?>
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
                                <div class="caption"><?php echo $this->lang->line('food_type') ?></div>
                                <div class="actions c-dropdown">
                                    <?php if(in_array('food_type~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) { ?>
                                        <button class="btn default-btn btn-sm danger-btn theme-btn" id="active"><i class="fa fa-check"></i> <?php echo $this->lang->line('active') ?></button>
                                        <button class="btn default-btn btn-sm danger-btn theme-btn" id="deactive"><i class="fa fa-ban"></i> <?php echo $this->lang->line('inactive') ?></button>
                                    <?php } ?>
                                    <?php if(in_array('food_type~add',$this->session->userdata("UserAccessArray"))) { ?>
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
                                    <?php if(isset($_SESSION['page_MSG']))
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

                                                <?php if(in_array('food_type~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) {  ?>
                                                <th class="table-checkbox"><input type="checkbox" class="group-checkable"></th>
                                                <?php  } ?>
                                                <th class="table-checkbox"><?php echo $this->lang->line('s_no') ?></th>
                                                <?php foreach ($Languages as $lang) {?>
                                                    <th><?php echo $this->lang->line('title') ?>&nbsp;(<?php echo $lang->language_slug;?>)</th>
                                                <?php } ?>
                                                <th><?php echo $this->lang->line('status') ?></th>
                                                <th><?php echo $this->lang->line('action') ?></th>
                                            </tr>
                                            <tr role="row" class="filter">
                                                <?php if(in_array('food_type~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) {  ?>
                                                    <td></td>
                                                <?php } ?>
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
    var food_type_count = <?php echo ($food_type_count)?$food_type_count:0; ?>;
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
                    <?php if(in_array('food_type~ajaxDisableAll',$this->session->userdata("UserAccessArray"))) { ?>
                        { "bSortable": false },
                    <?php } ?>
                    { "bSortable": false },
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
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                if(oSettings.aoData.length == 0 && food_type_count != 0 && oData.iStart >= food_type_count){
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
                    grid.getDataTable().fnDraw(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
function deleteAll(content_id,image, message)
{    
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
                  data : {'entity_id':ID,'status':Status,'tblname':'food_type'},
                  success: function(response) {
                       grid.getDataTable().fnDraw(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }          
        }
    });
}
function disableAll(ContentID,Status)
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
                  url : 'ajaxDisableAll',
                  data : {'content_id':ContentID,'status':Status,'tblname':'food_type'},
                  success: function(response) {
                    grid.getDataTable().fnDraw(); 
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) {           
                    alert(errorThrown);
                  }
               });
            }
        }
    });
}
//activate multiple food types
$('#active').click(function(e){
    e.preventDefault();
    var records = grid.getSelectedRows();  
    if(!jQuery.isEmptyObject(records)){            
        var CommissionIds = Array();
        var amount = '0.00';
        for (var i in records) {  
            var val = records[i]["value"];            
            CommissionIds.push(val);                        
        }
        var CommissionIdComma = CommissionIds.join(",");
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
                      url : 'activeDeactiveMultiFoodType',
                      data : {'arrayData':CommissionIdComma, 'flag':'active'},
                      success: function(response) {
                        grid.getDataTable().fnDraw(); 
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
//deactivate multiple food types
$('#deactive').click(function(e){
    e.preventDefault();
    var records = grid.getSelectedRows();  
    if(!jQuery.isEmptyObject(records)){            
        var CommissionIds = Array();
        var amount = '0.00';
        for (var i in records) {  
            var val = records[i]["value"];            
            CommissionIds.push(val);                        
        }
        var CommissionIdComma = CommissionIds.join(",");
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
                      url : 'activeDeactiveMultiFoodType',
                      data : {'arrayData':CommissionIdComma, 'flag':'deactive'},
                      success: function(response) {
                        grid.getDataTable().fnDraw(); 
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
</script>
<?php $this->load->view(ADMIN_URL.'/footer');?>