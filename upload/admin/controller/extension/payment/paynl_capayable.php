<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlCapayable extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1744;
    protected $_paymentMethodName = 'paynl_capayable';
    protected $_postPayment = true;
    protected $_defaultLabel = 'Capayable Achteraf Betalen';
}
