<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlEps extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2062;
    protected $_paymentMethodName = 'paynl_eps';

    protected $_defaultLabel = 'EPS Überweisung';
}
