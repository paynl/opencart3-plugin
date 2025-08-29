<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlSofort extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4761;
    protected $_paymentMethodName = 'paynl_sofort';

    protected $_defaultLabel = 'Sofort (by Klarna)';
}
