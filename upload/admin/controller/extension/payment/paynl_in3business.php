<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlIn3business extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3192;
    protected $_paymentMethodName = 'paynl_in3business';
    protected $_postPayment = true;
    protected $_defaultLabel = 'Mondu';
}
