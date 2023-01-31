<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBiller extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2931;
    protected $_paymentMethodName = 'paynl_biller';
    protected $_postPayment = true;
    protected $_defaultLabel = 'Biller';
}
