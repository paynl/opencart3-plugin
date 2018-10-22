<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlBillink extends Pay_Model
{
    protected $_paymentOptionId = 1672;
    protected $_paymentMethodName = 'paynl_billink';
}