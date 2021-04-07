<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlGood4fun extends Pay_Model
{
    protected $_paymentOptionId = 2628;
    protected $_paymentMethodName = 'paynl_good4fun';
}
