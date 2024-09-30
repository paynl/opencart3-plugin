<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlStadspasamsterdam extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 3609;
    protected $_paymentMethodName = 'paynl_stadspasamsterdam';
}
