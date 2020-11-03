<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlDecadeaukaart extends Pay_Model
{
    protected $_paymentOptionId = 2601;
    protected $_paymentMethodName = 'paynl_decadeaukaart';
}