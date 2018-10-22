<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlFashioncheque extends Pay_Model
{
    protected $_paymentOptionId = 815;
    protected $_paymentMethodName = 'paynl_fashioncheque';
}