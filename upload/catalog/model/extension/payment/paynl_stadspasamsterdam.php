<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlStadspasamsterdam extends Pay_Model
{
    protected $_paymentOptionId = 3609;
    protected $_paymentMethodName = 'paynl_stadspasamsterdam';
}
