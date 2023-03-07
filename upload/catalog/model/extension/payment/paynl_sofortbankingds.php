<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlSofortbankingds extends Pay_Model
{
    protected $_paymentOptionId = 577;
    protected $_paymentMethodName = 'paynl_sofortbankingds';
}
