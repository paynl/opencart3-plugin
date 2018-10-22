<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlAfterpayem extends Pay_Model
{
    protected $_paymentOptionId = 740;
    protected $_paymentMethodName = 'paynl_afterpayem';
}