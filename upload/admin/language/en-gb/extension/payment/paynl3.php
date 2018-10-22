<?php
// Text
$_['text_payment'] = 'Payment';

$_['button_save'] = 'Save';
$_['button_cancel'] = 'Cancel';

$_['text_success'] = 'Settings saved';

$_['error_not_activated'] = "This payment method is not activated for this website, go to  <a target='paynl' href='https://admin.pay.nl/programs/programs'>https://admin.pay.nl/programs/programs</a> to edit your website";
$_['error_api_error'] = 'The Pay.nl Api replied with the following error: ';
$_['error_error_occurred'] = 'An error has occurred: ';
$_['error_no_apitoken'] = 'You must enter an APItoken, you can find your APItokens on: <a href="https://admin.pay.nl/company/tokens">https://admin.pay.nl/company/tokens</a>';
$_['error_no_serviceid']= 'U moet een serviceId invoeren, u vind uw serviceId op: <a href="https://admin.pay.nl/programs/programs">https://admin.pay.nl/programs/programs</a>. Een serviceId begint altijd met SL-';

$_['text_confirm_start_tooltip'] = 'Confirm the order when starting the transaction, before the transaction is paid. Confirmation email will be sent immediately';
$_['text_confirm_start'] = 'Confirm order on transaction start';
$_['text_send_statusupdates'] = 'Send status updates';
$_['text_send_statusupdates_tooltip'] = 'Send the customer an email everytime the status of the order changes';

$_['text_display_icon'] = 'Display icon';
$_['text_display_icon_tooltip'] = 'Select if you want to display an icon and the size';

$_['text_status_pending']='Order status pending payment';
$_['text_status_pending_tooltip']='The status of the order when the payment is started, but not yet completed';
$_['text_status_complete']='Order status payment successful';
$_['text_status_complete_tooltip']='The status of the order when the payment is successful';
$_['text_status_canceled']='Order status canceled';
$_['text_status_canceled_tooltip']='The status of the order when the payment is canceled';
$_['text_minimum_amount']='Minimum order amount';
$_['text_maximum_amount']='Maximum order amount';
$_['text_payment_instructions'] = 'Instructions';
$_['text_payment_instructions_tooltip'] = 'If you want to give the customer instructions, you can give them here';

$_['entry_order_status'] = 'Order Status';
$_['entry_geo_zone']     = 'Geo Zone';
$_['entry_status']       = 'Status';
$_['entry_sort_order']   = 'Sort Order';

$paynl_logo = '<a href="https://www.pay.nl" target="paynl"><img src="view/image/payment/paynl.png" alt="Pay.nl" title="Pay.nl" /></a>';

$arrPaymentMethods = array(
    'afterpay',
    'afterpayem',
    'amex',
    'billink',
    'bitcoin',
    'capayable',
    'capayablegespreid',
    'cartebleue',
    'cashly',
    'eps',
    'fashioncheque',
    'fashiongiftcard',
    'focum',
    'gezondheidsbon',
    'giropay',
    'givacard',
    'ideal',
    'incasso',
    'maestro',
    'mistercash',
    'mybank',
    'overboeking',
    'paypal',
    'paysafecard',
    'phone',
    'podiumkadokaart',
    'postepay',
    'sofortbanking',
    'spraypay',
    'visamastercard',
    'vvvgiftcard',
    'webshopgiftcard',
    'wechatpay',
    'wijncadeau',
    'yehhpay',
    'yourgift'
);

foreach($arrPaymentMethods as $paymentMethod){
    $_['text_paynl_'.$paymentMethod] = $paynl_logo;
}