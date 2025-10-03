<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlVisamastercard extends Pay_Model
{
    protected $_paymentOptionId = 706;
    protected $_paymentMethodName = 'paynl_visamastercard';
}
