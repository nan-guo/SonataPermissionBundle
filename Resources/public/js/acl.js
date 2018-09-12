jQuery(document).ready(function ($) {

    jQuery(".assign_permissions_for_a_module").on('ifChecked', function(){
        var childClass = jQuery(this).attr('id');
        jQuery('.' + childClass).iCheck("check");
        var counter = jQuery('.' + childClass + '.module-permission').attr("data-counter");
        jQuery('.module-permission-' + counter).iCheck("check");
    });

    jQuery(".assign_permissions_for_a_module").on('ifUnchecked', function(){
        var childClass = jQuery(this).attr('id');
        jQuery('.' + childClass).iCheck("uncheck");
    });

});