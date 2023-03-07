<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlPodiumcadeaukaart extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 816;
    protected $_paymentMethodName = 'paynl_podiumcadeaukaart';

    protected $_defaultLabel = 'Podium Cadeaukaart';
}
