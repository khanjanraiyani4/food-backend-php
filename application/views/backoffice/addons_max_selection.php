<script>
/*flow - on check get max selection value get it's total check box checked compare if checked greater display error*/
var SELECTED_LANG = '<?php echo $this->session->userdata('language_slug') ?>';
$(document).on('click', '.row .check_addons',function() {
    if(this.checked) {
        var selection_length = $(this).closest('.max-selection').attr('data-max-selection');
        if(selection_length != '' && $.isNumeric(selection_length) && selection_length > 0){
            var checkbox_count = $(this).closest('.max-selection').find('.check_addons').filter(':checked').length;
            if(checkbox_count > selection_length){
                $(this).prop("checked", false);
                var category_name = $(this).attr('addons_category');
                if(SELECTED_LANG == 'en') {
                    bootbox.alert({
                        message: "Please select any "+ selection_length + " from " + category_name + ".",
                        buttons: {
                            ok: {
                                label: "Ok",
                            }
                        }
                    });
                } else if(SELECTED_LANG == 'fr') {
                    bootbox.alert({
                    message: "Veuillez sélectionner n'importe quel "+selection_length+" from " +category_name +".",
                    buttons: {
                        ok: {
                            label: "D'accord",
                        }
                    }
                });
                } else {
                    bootbox.alert({
                        message: "الرجاء تحديد أي"+selection_length+" from "+category_name+".",
                        buttons: {
                            ok: {
                                label: "نعم",
                            }
                        }
                    });
                }
            }
        }
    }
});
</script>