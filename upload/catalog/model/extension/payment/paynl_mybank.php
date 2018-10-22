<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlMybank extends Pay_Model
{
    protected $_paymentOptionId = 1588;
    protected $_paymentMethodName = 'paynl_mybank';
}