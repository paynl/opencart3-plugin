$(document).ready(function () {

    $isCapture = true;
    $isVoid = false;

    $('#payOrderAmount').change(function () {
        $(this).val(parseFloat($.trim($(this).val().replace(/[^0-9\.]/g, ''))).toFixed(2))
    })

    $('#payOrderAmount').on('keypress', function (e) {
        var charCode = (typeof e.which == 'undefined') ? e.keyCode : e.which
        var charStr = String.fromCharCode(charCode)
        if (!charStr.match(/^[0-9]+$/) && charStr !== '.') {
            e.preventDefault()
        }
    })

    $('#paymodalcancel, #paymodalclose, .paymodel .btn-close').click(function () {
        payModelClose()
    })

    $('#payOrderButton').click(function () {
        $isCapture = true;
        $isVoid = false;
        var amount = parseFloat($.trim($('#payOrderAmount').val().replace(/[^0-9\.]/g, '')));
        if ((isFloat(amount) || isInteger(amount)) && !(amount <= 0)) {
            var message = $('#confirmMessage').val().replace('%amount%', $('#payOrderCurrency').val() + ' ' + $('#payOrderAmount').val());
            $('#payMessage').text(message);
            $('#modal-pay').show();
            $('body').append('<div class="modal-backdrop show"></div>');
        } else {
            showMessage($('#nanErrorMessage').val())
        }
    })

    $('#payOrderButtonVoid').click(function () {
        $isCapture = false;
        $isVoid = true;
        var amount = parseFloat($.trim($('#payOrderAmount').val().replace(/[^0-9\.]/g, '')));
        if((isFloat(amount) || isInteger(amount)) && !(amount <= 0)){
            var message = $('#confirmMessageVoid').val().replace('%amount%', $('#payOrderCurrency').val() + ' ' + $('#orderVoidAmount').val());
            $('#payMessage').text(message);
            $('#modal-pay').show();
            $('body').append('<div class="modal-backdrop show"></div>');   
        } else {
            showMessage($('#nanErrorMessage').val())
        }            
    })

    $('#paymodalconfirm').click(function () {
        if ($isVoid) {
            ajax($('#ajaxURLVoid').val() + '&amount=' + $.trim($('#payOrderAmount').val()) + '&currency=' + $('#payOrderCurrency').val());
        } else {
            ajax($('#ajaxURL').val() + '&amount=' + $.trim($('#payOrderAmount').val()) + '&currency=' + $('#payOrderCurrency').val());
        }        
    })

    function showMessage(message) {
        $('#paySuccessMessage').text(message);
        $('#modal-pay-success').show();
        $('body').append('<div class="modal-backdrop show"></div>');
    }

    function payModelClose() {
        $('#payMessage').text('');
        $('#modal-pay').hide();
        $('#modal-pay-success').hide();
        $('.modal-backdrop').remove();
    }

    function ajax(url) {
        payModelClose()
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            asynchronous: true,
            success: function (data) {
                if (data['error']) {
                    var message = data['error']
                } else if (data['success']) {
                    var message = data['success']
                    $('#paymodalclose, #modal-pay-success .btn-close').click(function () {
                        window.location.reload(true)
                    })
                }
                showMessage(message)
            }
        })
    }

    function isFloat(n) {
        return n === +n && n !== (n | 0);
    }

    function isInteger(n) {
        return n === +n && n === (n | 0);
    }
})
