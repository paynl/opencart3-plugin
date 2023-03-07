<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlPodiumcadeaukaart extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 816;
    protected $_paymentMethodName = 'paynl_podiumcadeaukaart';
}
