<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlVVVGiftcard extends Pay_Model
{
    protected $_paymentOptionId = 1714;
    protected $_paymentMethodName = 'paynl_vvvgiftcard';
}