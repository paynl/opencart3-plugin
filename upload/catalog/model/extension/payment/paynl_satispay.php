<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlSatispay extends Pay_Model
{
    protected $_paymentOptionId = 4146;
    protected $_paymentMethodName = 'paynl_satispay';
}