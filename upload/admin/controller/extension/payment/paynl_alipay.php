<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlAlipay extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2080;
    protected $_paymentMethodName = 'paynl_alipay';

    protected $_defaultLabel = 'AliPay';
}
