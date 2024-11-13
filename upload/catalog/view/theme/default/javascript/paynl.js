jQuery(document).ready(function () {
    $('.fast-checkout-block a').off('click').on('click', function (event) {
        event.preventDefault();

        $(this).disabled = true;
        $('#button-cart').click();

        var method = $(this).data('method');
        $.ajax({
            url: 'index.php?route=extension/payment/' + method + '/initFastCheckout',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({method: method}),
            success: function (response) {
                if (typeof response === "string") {
                    response = JSON.parse(response);
                }

                window.location.href = response.data.links.redirect
            },
            error: function (xhr, status, error) {
                console.error('Error:', error.data);
            }
        });
    });

    waitForElement('#paypal-button-container', function () {
        renderPayPalButton('#paypal-button-container');
    });

    waitForElement('#paypal-button-container-2', function () {
        renderPayPalButton('#paypal-button-container-2');
    });
});

function renderPayPalButton(containerSelector) {
    if (!jQuery(containerSelector).is(':empty')) return; // Avoid re-rendering

    const buttonConfig = {
        style: {
            layout: 'horizontal',
            color: 'blue',
            shape: 'rect',
            label: 'paypal',
            height: 34
        },
        createOrder: function (data, actions) {
            $('#button-cart').click();

            return fetch('index.php?route=extension/payment/paynl_paypal/initFastCheckout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(orderData => {
                    return actions.order.create({
                        purchase_units: [{
                            reference_id: orderData.order_id,
                            amount: {
                                value: orderData.total_amount
                            }
                        }]
                    });
                });
        },
        onApprove: function (data, actions) {
            return fetch('index.php?route=extension/payment/paynl_paypal/handleResult', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(result => {
                    console.log(result);
                    // window.location.href = 'index.php?route=checkout/success';
                })
                .catch(error => console.error('Payment failed:', error));
        }
    };

    paypal.Buttons(buttonConfig).render(containerSelector);
}

function waitForElement(selector, callback, interval = 200, maxAttempts = 50) {
    let attempts = 0;

    function checkElement() {
        if (jQuery(selector).length) {
            callback();
        } else {
            attempts++;
            if (attempts < maxAttempts) {
                setTimeout(checkElement, interval);
            }
        }
    }

    checkElement();
}
