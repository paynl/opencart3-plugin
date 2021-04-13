<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlTrustly extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2718;
    protected $_paymentMethodName = 'paynl_trustly';

    protected $_defaultLabel = 'Trustly';
}
