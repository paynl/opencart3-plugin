jQuery(document).ready(function () {
    jQuery("#suggestions_form_submit").click(function () {
        $('#email_error').hide();
        $('#message_error').hide();
        var email = $('#suggestions_form_email').val();
        var message = $('#suggestions_form_message').val();

        var regex = /^[\w-\.]+@([\w-]+\.)+[\w-]/i;
        if($.trim(message) == '' || ($.trim(email) != '' && !regex.test($('#suggestions_form_email').val()))){
            if($.trim(email) != '' && !regex.test($('#suggestions_form_email').val())){
                $('#email_error').css('display', 'inline');
            }
            if($.trim(message) == ''){
                $('#message_error').css('display', 'inline');
            }
            return false;
        }

        var ajaxurl = $('#suggestions_form_url').val();
        var pluginversion = $('#suggestions_form_plugin_version').val();
        var data = {
            'email' : email,
            'message' : message,
            'pluginverison' : pluginversion
        };
        setTimeout(function () {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        $('#suggestions_form_email').val("");
                        $('#suggestions_form_message').val("");
                        $('#suggestions_form_success').modal('show');
                    } else {
                        $('#suggestions_form_fail').modal('show');
                    }
                },
                error: function () {
                    $('#suggestions_form_fail').modal('show');
                }
            });
        }, 750);

    });

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

    jQuery(".advanced_settings").hide();
    jQuery("#show_advanced_settings").click(function () {
        jQuery(".advanced_settings").toggle();

        if ($(".advanced_settings").is(":visible")) {
            $("#advanced_settings_icon").removeClass("fa-chevron-down").addClass("fa-chevron-up");
        } else {
            $("#advanced_settings_icon").removeClass("fa-chevron-up").addClass("fa-chevron-down");
        }
    });

    var fastCheckoutSelect = $('.fast-checkout-select-block select');
    var dependentFields = $('#dependent-fields');

    toggleDependentFields(fastCheckoutSelect.val());

    fastCheckoutSelect.on('change', function () {
        toggleDependentFields($(this).val());
    });

    function toggleDependentFields(value) {
        if (value === '1') {
            dependentFields.show();
        } else {
            dependentFields.hide();
        }
    }
    jQuery('.obscuredInput').each(function () {
        var input = this;
        jQuery('<a class="obscuredDisplayShow"></a>').click(function () {
            toggleObscured(input)
        }).insertAfter(input);
    })
});

function toggleObscured(element) {
    jQuery(element).parent().find('.obscuredInput').toggleClass('display');
}