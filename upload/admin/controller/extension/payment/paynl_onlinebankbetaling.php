<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlOnlinebankbetaling extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2970;
    protected $_paymentMethodName = 'paynl_onlinebankbetaling';

    protected $_defaultLabel = 'Onlinebankbetaling';
}
