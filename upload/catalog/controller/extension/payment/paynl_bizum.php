<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBizum extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 3843;
    protected $_paymentMethodName = 'paynl_bizum';
}
