<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlUpas extends Pay_Model
{
    protected $_paymentOptionId = 4283;
    protected $_paymentMethodName = 'paynl_upas';
}
