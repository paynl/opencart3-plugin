<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlCapayable extends Pay_Model
{
    protected $_paymentOptionId = 1744;
    protected $_paymentMethodName = 'paynl_capayable';
}
