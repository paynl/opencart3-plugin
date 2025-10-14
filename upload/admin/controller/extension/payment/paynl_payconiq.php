<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlPayconiq extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2379;
    protected $_paymentMethodName = 'paynl_payconiq';

    protected $_defaultLabel = 'Payconiq';
}
