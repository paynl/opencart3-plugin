<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlPaypal extends Pay_Model
{
    protected $_paymentOptionId = 138;
    protected $_paymentMethodName = 'paynl_paypal';
}