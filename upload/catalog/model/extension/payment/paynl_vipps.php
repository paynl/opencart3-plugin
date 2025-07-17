<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlVipps extends Pay_Model
{
    protected $_paymentOptionId = 3834;
    protected $_paymentMethodName = 'paynl_vipps';
}