<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlAfterpayint extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2561;
    protected $_paymentMethodName = 'paynl_afterpayint';
    protected $_postPayment = true;
    protected $_defaultLabel = 'Afterpay International';
}
