<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlAfterpayem extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 740;
    protected $_paymentMethodName = 'paynl_afterpayem';
    protected $_postPayment = true;
    protected $_defaultLabel = 'Afterpay - Eenmalige machtiging';
}
