<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlwebshopgiftcard extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 811;
    protected $_paymentMethodName = 'paynl_webshopgiftcard';
}
