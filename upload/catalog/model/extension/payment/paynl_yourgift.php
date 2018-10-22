<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlYourgift extends Pay_Model
{
    protected $_paymentOptionId = 1645;
    protected $_paymentMethodName = 'paynl_yourgift';
}