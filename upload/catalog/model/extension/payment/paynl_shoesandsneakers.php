<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlShoesandsneakers extends Pay_Model
{
    protected $_paymentOptionId = 2937;
    protected $_paymentMethodName = 'paynl_shoesandsneakers';
}
