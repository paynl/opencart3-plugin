<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlCapayableGespreid extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1813;
    protected $_paymentMethodName = 'paynl_capayablegespreid';
    protected $_postPayment = true;
    protected $_defaultLabel = 'in3 keer betalen, 0% rente';
}
