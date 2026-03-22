<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlStadspaspurmerend extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 5177;
    protected $_paymentMethodName = 'paynl_stadspaspurmerend';

    protected $_defaultLabel = 'Stadspas Purmerend';
}
