<?php
// Text
$_['text_payment'] = 'Payment';

$_['button_save'] = 'Save';
$_['button_cancel'] = 'Cancel';

$_['text_success'] = 'Settings saved';

//errors
$_['error_not_activated'] = "This payment method is not activated for this website, go to  <a target='paynl' href='https://admin.pay.nl/programs/programs'>https://admin.pay.nl/programs/programs</a> to edit your website";
$_['error_api_error'] = 'The PAY. Api replied with the following error: ';
$_['error_error_occurred'] = 'An error has occurred: ';
$_['error_no_apitoken'] = 'You must enter an APItoken, you can find your APItokens on: <a href="https://admin.pay.nl/company/tokens">https://admin.pay.nl/company/tokens</a>';
$_['error_no_serviceid']= 'U moet een serviceId invoeren, u vind uw serviceId op: <a href="https://admin.pay.nl/programs/programs">https://admin.pay.nl/programs/programs</a>. Een serviceId begint altijd met SL-';

//texts
$_['text_register'] = 'Not registered at PAY.? Sign up ';
$_['text_link_register'] = 'here';
$_['link_register'] = 'https://www.pay.nl/en/register';
$_['text_after_register'] = '!';

$_['text_general_settings'] = 'PAY. General settings';
$_['text_method_settings'] = 'Payment method settings';

$_['text_confirm_start_tooltip'] = 'Confirm the order when starting the transaction, before the transaction is paid. Confirmation email will be sent immediately';
$_['text_confirm_start'] = 'Confirm order on transaction start';
$_['text_send_statusupdates'] = 'Send status updates';
$_['text_send_statusupdates_tooltip'] = 'Send the customer an email everytime the status of the order changes';

$_['text_gateway'] = 'Failover gateway';
$_['text_gateway_tooltip'] = 'Only fill this in when we at PAY. provide you with a gateway to fill in here';

$_['text_prefix'] = 'Order description prefix';
$_['text_prefix_tooltip'] = 'Change the order description prefix here. If left empty the order description will just be the order number.';

$_['text_coc'] = 'Show COC number field';
$_['text_coc_tooltip'] = 'When enabled the customer can additionally enter their COC number before finishing the transaction';
$_['text_coc_disabled'] = 'No';
$_['text_coc_enabled'] = 'Yes, as optional';
$_['text_coc_required'] = 'Yes, as required';

$_['text_vat'] = 'Show VAT-id field for non-private customers';
$_['text_vat_tooltip'] = 'When enabled the customer can additionally enter their VAT-id before finishing the transaction';
$_['text_vat_disabled'] = 'Off';
$_['text_vat_enabled'] = 'Optional for business customers';
$_['text_vat_required'] = 'Required for business-customers ';

$_['text_dob'] = 'Show date of birth field';
$_['text_dob_tooltip'] = 'When enabled the customer can additionally enter their date of birth before finishing the transaction';
$_['text_dob_disabled'] = 'No';
$_['text_dob_enabled'] = 'Yes, as optional';
$_['text_dob_required'] = 'Yes, as required';

$_['text_display_icon'] = 'Display icon';
$_['text_display_icon_tooltip'] = 'Select if you want to display an icon and the size';

$_['text_icon_style'] = 'Icon style';
$_['text_icon_style_tooltip'] = 'Select whether you want to use the classic or newest images during checkout.';
$_['text_classic'] = 'Classic';
$_['text_newest'] = 'Newest';

$_['text_testmode'] = 'Testmode';
$_['text_testmode_tooltip'] = 'Enable or disable test mode to test the exchanges between PAY. and your webshop';

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

$_['text_customer_type'] = 'Allowed customer type';
$_['text_customer_type_tooltip'] = 'Select which customer type you want to be able to access the payment method.';
$_['text_both'] = 'Both';
$_['text_private'] = 'Private';
$_['text_business'] = 'Business';

$_['text_extension'] = 'PAY.';

$version = '1.5.1';
$css = 'position: relative;top:0px;display: inline;left: 10px;';
$paynl_logo = '<a href="https://www.pay.nl" target="paynl"><img style="width: 30px;" src="view/image/payment/main_pay_logo.png" alt="PAY." title="PAY." /></a>' .
    '<div style="' . $css . '">Version: ' . $version . '</div>';;

$arrPaymentMethods = array(
    'afterpay',
    'afterpayem',
    'afterpayint',
    'amex',
    'applepay',
    'biercheque',
    'billink',
    'bitcoin',
    'capayable',
    'capayablegespreid',
    'cartebleue',
    'cashly',
    'creditclick',
    'decadeaukaart',
    'eps',
    'fashioncheque',
    'fashiongiftcard',
    'focum',
    'gezondheidsbon',
    'giropay',
    'givacard',
    'good4fun',
    'googlepay',
    'ideal',
    'incasso',
    'klarnakp',
    'maestro',
    'mistercash',
    'multibanco',
    'mybank',
    'nexi',
    'overboeking',
    'payconiq',
    'paypal',
    'paysafecard',
    'phone',
    'podiumkadokaart',
    'postepay',
    'przelewy24',
    'sofortbanking',
    'spraypay',
    'tikkie',
    'trustly',
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