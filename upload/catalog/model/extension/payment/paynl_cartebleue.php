<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlCartebleue extends Pay_Model
{
    protected $_paymentOptionId = 710;
    protected $_paymentMethodName = 'paynl_cartebleue';
}