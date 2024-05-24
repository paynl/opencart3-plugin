<?php

$_['text_apitoken'] = 'API token';
$_['text_serviceid'] = 'Sales location';

// Text
$_['text_payment'] = 'Payment';

$_['button_save'] = 'Save';
$_['button_cancel'] = 'Cancel';

$_['text_success'] = 'Settings saved';

//errors
$_['error_not_activated'] = "This payment method is not activated for this website, go to  
    <a target='paynl' href='https://admin.pay.nl/programs/programs'>https://admin.pay.nl/programs/programs</a> to edit your website";
$_['error_api_error'] = 'The Pay. Api replied with the following error: ';
$_['error_error_occurred'] = 'An error has occurred: ';
$_['error_no_apitoken'] = 'You must enter an APItoken, you can find your APItokens on: 
    <a href="https://admin.pay.nl/company/tokens">https://admin.pay.nl/company/tokens</a>';
$_['error_no_serviceid'] = 'U moet een serviceId invoeren, u vind uw serviceId op: 
    <a href="https://admin.pay.nl/programs/programs">https://admin.pay.nl/programs/programs</a>. Een serviceId begint altijd met SL-';

//texts
$_['text_register'] = 'Not registered at Pay.? Sign up ';
$_['text_link_register'] = 'here';
$_['link_register'] = 'https://www.pay.nl/en/register';
$_['text_after_register'] = '!';

$_['text_general_settings'] = 'Pay. General settings';
$_['text_method_settings'] = 'Payment method settings';

$_['text_confirm_start_tooltip'] = 'Confirm the order when starting the transaction, before the transaction is paid. Confirmation email will be sent immediately';
$_['text_confirm_start'] = 'Confirm order on transaction start';
$_['text_send_statusupdates'] = 'Send status updates';
$_['text_send_statusupdates_tooltip'] = 'Send the customer an email everytime the status of the order changes';

$_['text_gateway'] = 'Failover gateway';
$_['text_gateway_tooltip'] = 'Only fill this in when we at Pay. provide you with a gateway to fill in here';

$_['text_prefix'] = 'Order description prefix';
$_['text_prefix_tooltip'] = 'Change the order description prefix here. If left empty the order description will just be the order number.';

$_['text_advanced_settings'] = 'Advanced settings';

$_['text_refund_processing'] = 'Refund processing';
$_['text_refund_processing_tooltip'] = 'Process refunds that are initiated in My.pay';

$_['text_auto_void'] = 'Auto void';
$_['text_auto_void_tooltip'] = 'Automatically void transactions in the state AUTHORIZE when cancelling an order.';

$_['text_auto_capture'] = 'Auto capture';
$_['text_auto_capture_tooltip'] = 'Enable auto capture for authorized transactions. Captures will be initiated when an order is set to Completed.';

$_['text_follow_payment_method'] = 'Follow payment method';
$_['text_follow_payment_method_tooltip'] = 'This will ensure the order is updated with the actual payment method used to complete the order. This can differ from the payment method initially selected'; // phpcs:ignore

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

$_['text_custom_exchange_url'] = 'Alternatieve Exchange URL';
$_['text_custom_exchange_url_tooltip'] = 'Use your own exchange-handler. Requests will be send as GET.<br/>
    Example: https://www.yourdomain.nl/exchange_handler?action=#action#&order_id=#order_id#';

$_['text_current_ip'] = 'Current user IP address: ';
$_['text_test_ip'] = 'Test IP Address';
$_['text_test_ip_tooltip'] = 'Forces test mode on these IP addresses, separate IPs by commas for multiple IPs';

$_['text_logging'] = 'Logging';
$_['text_logging_tooltip'] = "Enable logging";

$_['text_testmode'] = 'Test mode';
$_['text_testmode_tooltip'] = 'Enable or disable test mode to test the exchanges between Pay. and your webshop';

$_['text_status_pending'] = 'Order status pending payment';
$_['text_status_pending_tooltip'] = 'The status of the order when the payment is started, but not yet completed';
$_['text_status_complete'] = 'Order status payment successful';
$_['text_status_complete_tooltip'] = 'The status of the order when the payment is successful';
$_['text_status_canceled'] = 'Order status canceled';
$_['text_status_canceled_tooltip'] = 'The status of the order when the payment is canceled';
$_['text_status_refunded'] = 'Order status refunded';
$_['text_status_refunded_tooltip'] = 'The status of the order when the payment is refunded';
$_['text_minimum_amount'] = 'Minimum order amount';
$_['text_maximum_amount'] = 'Maximum order amount';
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

$_['text_extension'] = 'Pay.';

$_['text_enabled'] = 'On';
$_['text_disabled'] = 'Off';

$_['text_suggestions'] = 'If you have a feature request or other ideas, let us know!<br/>Your submission will be reviewed by our development team.<br/><br/>If needed, we will contact you for further information via the e-mail address provided.<br/>Please note: this form is not for Support requests, please contact <a href="mailto:support@pay.nl" target="_blank">support@pay.nl</a> for this.'; // phpcs:ignore

$_['text_email_label'] = 'E-mail (optional)';
$_['text_email_error'] = 'Please fill in a valid e-mail.';

$_['text_message_label'] = 'Message';
$_['text_message_error'] = 'Please fill in your message.';
$_['text_message_placeholder'] = 'Leave your suggestions here…';

$_['text_suggestions_submit'] = 'Submit';

$_['text_suggestions_success_modal'] = 'Sent! Thank you for your contribution.';
$_['text_suggestions_fail_modal'] = 'E-mail could not be sent, please try again later.';

$version = '1.9.2';
$_['version'] = $version;
$css = 'position: relative;top:0px;display: inline;left: 10px;';
$paynl_logo = '<a href="https://www.pay.nl" target="paynl"><img style="width: 30px;" 
    src="view/image/payment/main_pay_logo.png" alt="Pay." title="Pay." /></a>' . '<div style="' . $css . '">Version: ' . $version . '</div>';

$arrPaymentMethods = array(
    'afterpay',
    'afterpayint',
    'alipay',
    'amazonpay',
    'amex',
    'applepay',
    'beautycadeau',
    'biercheque',
    'biller',
    'billink',
    'bioscoopbon',
    'blik',
    'bloemencadeau',
    'boekenbon',
    'capayablegespreid',
    'cartebleue',
    'cashly',
    'creditclick',
    'cult',
    'dankort',
    'decadeaukaart',
    'dinerbon',
    'eps',
    'fashioncheque',
    'fashiongiftcard',
    'festivalcadeau',
    'gezondheidsbon',
    'giropay',
    'givacard',
    'good4fun',
    'googlepay',
    'horsesandgifts',
    'huisentuincadeau',
    'ideal',
    'in3business',
    'incasso',
    'klarnakp',
    'maestro',
    'monizze',
    'mooigiftcard',
    'mistercash',
    'multibanco',
    'nexi',
    'onlinebankbetaling',
    'overboeking',
    'parfumcadeaukaart',
    'payconiq',
    'paypal',
    'paysafecard',
    'podiumcadeaukaart',
    'phone',
    'podiumcadeaukaart',
    'postepay',
    'przelewy24',
    'shoesandsneakers',
    'sodexo',
    'sofortbanking',
    'sofortbankingds',
    'sofortbankinghr',
    'spraypay',
    'trustly',
    'visamastercard',
    'vvvgiftcard',
    'webshopgiftcard',
    'wechatpay',
    'wijncadeau',
    'winkelcheque',
    'yourgift',
    'yourgreengift',
);

foreach ($arrPaymentMethods as $paymentMethod) {
    $_['text_paynl_' . $paymentMethod] = $paynl_logo;
}
