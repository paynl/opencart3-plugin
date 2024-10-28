jQuery(document).ready(function () {
    $('.fast-checkout-block a').on('click', function(event) {
        event.preventDefault();
        $(this).disabled = true
        $('#button-cart').click();

        var method = $(this).data('method');
        $.ajax({
            url: 'index.php?route=extension/payment/' + method + '/initFastCheckout',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ method: method }),
            success: function(response) {
                if (typeof response === "string") {
                    response = JSON.parse(response);
                }

                window.location.href = response.data.links.redirect
            },
            error: function(xhr, status, error) {
                console.error('Error:', error.data);
            }
        });
    });
});
