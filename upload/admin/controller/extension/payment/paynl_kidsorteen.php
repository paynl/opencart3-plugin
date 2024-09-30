<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlKidsorteen extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3597;
    protected $_paymentMethodName = 'paynl_kidsorteen';
    protected $_defaultLabel = 'Kids or teen';
}
