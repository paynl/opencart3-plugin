<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlPrzelewy24 extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2151;
    protected $_paymentMethodName = 'paynl_przelewy24';

    protected $_defaultLabel = 'Przelewy 24';
}
