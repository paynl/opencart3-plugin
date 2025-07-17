<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlVipps extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3834;
    protected $_paymentMethodName = 'paynl_vipps';

    protected $_defaultLabel = 'Vipps';
}
