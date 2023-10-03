jQuery(document).ready(function () {       
    jQuery("#check_version_submit").click(function (e) {
        e.preventDefault();
        var ajaxurl = jQuery('#ajax_url').val();
        var data = {
            'versionCheck' : jQuery('#pay_current_version').val()
        };
        jQuery('#paynl_version_check_result').hide();
        jQuery('#paynl_version_check_loading').css('display', 'block');
        setTimeout(function () {    
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {                   
                    let result = '';                        
                    if (!data) {
                        result = 'Something went wrong, please try again later'
                    } else {
                        var newest_version = data.version.substring(1);
                        var current_version = jQuery('#pay_current_version').val();
                        if (newest_version > current_version) {
                            result = 'There is a new version available (' + data.version + ')'
                        } else {
                            jQuery('#check_version_submit').hide();
                            result = 'You are up to date with the latest version'
                            jQuery('#paynl_version_check_current_version').addClass('versionUpToDate');
                        }
                    }
                    jQuery('#paynl_version_check_result').html(result);
                    jQuery('#paynl_version_check_result').css('display', 'block');
                    jQuery('#paynl_version_check_loading').hide();
                },
                error: function () {
                    result = 'Something went wrong, please try again later'
                    jQuery('#paynl_version_check_result').html(result);
                    jQuery('#paynl_version_check_result').css('display', 'block');
                    jQuery('#paynl_version_check_loading').hide();
                }
            });
        }, 750);       
    });        
});
