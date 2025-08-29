<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBabycadeaubon extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4416;
    protected $_paymentMethodName = 'paynl_babycadeaubon';

    protected $_defaultLabel = 'Babycadeaubon';
}
