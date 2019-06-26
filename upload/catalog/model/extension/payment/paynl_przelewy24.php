<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlPrzelewy24 extends Pay_Model
{
    protected $_paymentOptionId = 2151;
    protected $_paymentMethodName = 'paynl_przelewy24';
}