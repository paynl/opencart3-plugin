<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlWijncadeau extends Pay_Model
{
    protected $_paymentOptionId = 1666;
    protected $_paymentMethodName = 'paynl_wijncadeau';
}