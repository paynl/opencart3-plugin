<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlFashionchequebeauty extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4428;
    protected $_paymentMethodName = 'paynl_fashionchequebeauty';

    protected $_defaultLabel = 'Fashion cheque Beauty';
}
