<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBitcoin extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1594;
    protected $_paymentMethodName = 'paynl_bitcoin';

    protected $_defaultLabel = 'Bitcoin';
}
