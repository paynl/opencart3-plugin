<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlpaypal extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 138;
    protected $_paymentMethodName = 'paynl_paypal';
}
