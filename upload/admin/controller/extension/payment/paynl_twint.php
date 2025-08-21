<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlTwint extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3840;
    protected $_paymentMethodName = 'paynl_twint';

    protected $_defaultLabel = 'Twint';
}
