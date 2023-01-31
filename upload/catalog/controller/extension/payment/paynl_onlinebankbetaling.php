<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlOnlinebankbetaling extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 2970;
    protected $_paymentMethodName = 'paynl_onlinebankbetaling';
}
