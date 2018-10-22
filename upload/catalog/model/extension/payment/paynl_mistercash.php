<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlMistercash extends Pay_Model
{
    protected $_paymentOptionId = 436;
    protected $_paymentMethodName = 'paynl_mistercash';
}