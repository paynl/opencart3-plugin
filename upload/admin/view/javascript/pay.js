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
});