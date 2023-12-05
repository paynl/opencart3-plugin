<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlHorsesandgifts extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3222;
    protected $_paymentMethodName = 'paynl_horsesandgifts';
    protected $_defaultLabel = 'Horses & Gifts';
}
