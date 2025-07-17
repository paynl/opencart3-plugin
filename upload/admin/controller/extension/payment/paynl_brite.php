<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBrite extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4287;
    protected $_paymentMethodName = 'paynl_brite';

    protected $_defaultLabel = 'Brite';
}
