<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlTikkie extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 2104;
    protected $_paymentMethodName = 'paynl_tikkie';
}
