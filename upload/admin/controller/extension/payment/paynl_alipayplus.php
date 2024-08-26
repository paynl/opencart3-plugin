<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlAlipayplus extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2907;
    protected $_paymentMethodName = 'paynl_alipayplus';
    protected $_defaultLabel = 'Alipay Plus';
}
