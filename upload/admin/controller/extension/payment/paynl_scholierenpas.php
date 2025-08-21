<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlScholierenpas extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4434;
    protected $_paymentMethodName = 'paynl_scholierenpas';

    protected $_defaultLabel = 'Scholieren pas';
}
