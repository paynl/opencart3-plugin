<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlPaypal extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 138;
    protected $_paymentMethodName = 'paynl_paypal';

    protected $_defaultLabel = 'PayPal';
}
