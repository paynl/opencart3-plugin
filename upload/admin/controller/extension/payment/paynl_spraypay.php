<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlSpraypay extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1987;
    protected $_paymentMethodName = 'paynl_spraypay';

    protected $_defaultLabel = 'SprayPay';
}
