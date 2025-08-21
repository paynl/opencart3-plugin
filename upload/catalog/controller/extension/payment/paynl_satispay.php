<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlSatispay extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 4146;
    protected $_paymentMethodName = 'paynl_satispay';
}