<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlPhone extends Pay_Model
{
    protected $_paymentOptionId = 1600;
    protected $_paymentMethodName = 'paynl_phone';
}