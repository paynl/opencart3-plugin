<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlMobilepay extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 3558;
    protected $_paymentMethodName = 'paynl_mobilepay';
}
