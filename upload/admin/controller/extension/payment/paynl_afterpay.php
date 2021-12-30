<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlAfterpay extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 739;
    protected $_paymentMethodName = 'paynl_afterpay';
    protected $_postPayment = true;
    protected $_defaultLabel = 'Afterpay';
}
