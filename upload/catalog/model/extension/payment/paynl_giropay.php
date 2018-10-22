<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlGiropay extends Pay_Model
{
    protected $_paymentOptionId = 694;
    protected $_paymentMethodName = 'paynl_giropay';
}