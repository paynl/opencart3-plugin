<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlPostepay extends Pay_Model
{
    protected $_paymentOptionId = 707;
    protected $_paymentMethodName = 'paynl_postepay';
}