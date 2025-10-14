<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlKlarnakp extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1717;
    protected $_paymentMethodName = 'paynl_klarnakp';
    protected $_postPayment = true;
    protected $_defaultLabel = 'Klarna (Achteraf betalen)';
}
