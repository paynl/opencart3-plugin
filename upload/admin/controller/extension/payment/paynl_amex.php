<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlAmex extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1705;
    protected $_paymentMethodName = 'paynl_amex';

    protected $_defaultLabel = 'American Express';
}
