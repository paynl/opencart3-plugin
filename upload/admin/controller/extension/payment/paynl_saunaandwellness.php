<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlSaunaandwellness extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4269;
    protected $_paymentMethodName = 'paynl_saunaandwellness';

    protected $_defaultLabel = 'Sauna & Wellness cadeaukaart';
}
