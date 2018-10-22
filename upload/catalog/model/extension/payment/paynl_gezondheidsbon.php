<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlGezondheidsbon extends Pay_Model
{
    protected $_paymentOptionId = 812;
    protected $_paymentMethodName = 'paynl_gezondheidsbon';
}