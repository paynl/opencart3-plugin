<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlDankort extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1939;
    protected $_paymentMethodName = 'paynl_dankort';

    protected $_defaultLabel = 'Dankort';
}
