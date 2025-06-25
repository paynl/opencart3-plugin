<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlMbway extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3846;
    protected $_paymentMethodName = 'paynl_mbway';

    protected $_defaultLabel = 'MB WAY';
}
