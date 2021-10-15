<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlAfterpayint extends Pay_Model
{
    protected $_paymentOptionId = 2561;
    protected $_paymentMethodName = 'paynl_afterpayint';
}