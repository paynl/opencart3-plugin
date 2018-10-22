<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlGivacard extends Pay_Model
{
    protected $_paymentOptionId = 1657;
    protected $_paymentMethodName = 'paynl_givacard';
}