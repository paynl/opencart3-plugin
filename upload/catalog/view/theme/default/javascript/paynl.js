jQuery(document).ready(function () {
    fetch('index.php?route=extension/payment/paynl_paypal/getButtonConfig')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(config => {
            if (config && config.client_id) {
                return loadPayPalScript(config).then(() => config);
            } else {
                console.error('Client ID is missing in the configuration');
                return null;
            }
        })
        .then((config) => {
            if (!config) {
                console.error('Config is undefined or null');
                return;
            }

            const currentRoute = getCurrentRoute();

            config.appearsIn.forEach(function (location) {
                if (location === 'mini_cart') {
                    waitForElement('#paypal-button-container', function () {
                        renderPayPalButton('#paypal-button-container');
                    });
                }

                if (location === 'product' && currentRoute === 'product/product') {
                    waitForElement('#paypal-button-container-2', function () {
                        renderPayPalButton('#paypal-button-container-2');
                    });
                }

                if (location === 'cart' && currentRoute === 'checkout/cart') {
                    waitForElement('#paypal-button-container-2', function () {
                        renderPayPalButton('#paypal-button-container-2');
                    });
                }
            });
        })
        .catch(error => {
            console.error('Error loading PayPal Button:', error);
        });

    $('.fast-checkout-block a').off('click').on('click', function (event) {
        event.preventDefault();

        $(this).disabled = true;
        $('#button-cart').click();

        const method = $(this).data('method');

        setTimeout(() => {
            $.ajax({
                url: 'index.php?route=extension/payment/' + method + '/initFastCheckout',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({method: method}),
                success: function (response) {
                    if (typeof response === "string") {
                        response = JSON.parse(response);
                    }

                    window.location.href = response.data.links.redirect;
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error.data);
                }
            });
        }, 500);
    });

    observeMiniCartChanges();
});

function getCurrentRoute() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('route') || '';
}

function loadPayPalScript(config) {
    return new Promise((resolve) => {
        if (document.getElementById('paypal-sdk')) return resolve();

        const script = document.createElement('script');
        script.id = 'paypal-sdk';
        script.src = `https://www.paypal.com/sdk/js?client-id=${config.client_id}&intent=${config.intent}&components=buttons&currency=${config.currency}`;
        script.onload = resolve;
        document.head.appendChild(script);
    });
}

function renderPayPalButton(containerSelector) {
    if (!window.paypal || !jQuery(containerSelector).is(':empty')) return;

    paypal.Buttons({
        style: {
            layout: 'horizontal',
            color: 'blue',
            shape: 'rect',
            label: 'paypal',
            height: 34
        },
        createOrder: function (data, actions) {
            $('#button-cart').click();

            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve(fetch('index.php?route=extension/payment/paynl_paypal/initFastCheckout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    }));
                }, 500);
            })
                .then(response => response.json())
                .then(orderData => {
                    return actions.order.create({
                        intent: 'CAPTURE',
                        purchase_units: [{
                            reference_id: orderData.order_id,
                            amount: {
                                value: orderData.total_amount,
                                currency_code: orderData.currency
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
                body: JSON.stringify({orderID: data.orderID})
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success' && result.redirect_url) {
                        window.location.href = result.redirect_url;
                    } else {
                        console.error('Payment failed:', result.error);
                    }
                })
                .catch(error => console.error('Payment failed:', error));
        },
        onCancel: function () {
            window.location.href = 'index.php?route=extension/payment/paynl_paypal/cancelPayment';
        }
    }).render(containerSelector);
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

function observeMiniCartChanges() {
    const miniCart = document.querySelector('#cart');

    if (!miniCart) {
        console.error('Mini-cart element not found');
        return;
    }

    const observer = new MutationObserver((mutationsList) => {
        for (let mutation of mutationsList) {
            if (mutation.type === 'childList' || mutation.type === 'subtree') {
                if (jQuery('#paypal-button-container').is(':empty')) {
                    renderPayPalButton('#paypal-button-container');
                }
            }
        }
    });

    observer.observe(miniCart, {
        childList: true,
        subtree: true
    });
}
