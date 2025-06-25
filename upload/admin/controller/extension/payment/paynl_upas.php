<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlUpas extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4283;
    protected $_paymentMethodName = 'paynl_upas';

    protected $_defaultLabel = 'U-Pas';
}
