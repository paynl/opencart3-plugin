<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlFocum extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1702;
    protected $_paymentMethodName = 'paynl_focum';
    protected $_postPayment = true;
    protected $_defaultLabel = 'Achteraf betalen';
}
