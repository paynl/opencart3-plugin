<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlSaunaandwellness extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 4269;
    protected $_paymentMethodName = 'paynl_saunaandwellness';
}
