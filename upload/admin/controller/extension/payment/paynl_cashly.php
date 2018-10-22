<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlCashly extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1981;
    protected $_paymentMethodName = 'paynl_cashly';

    protected $_defaultLabel = 'Cashly';
}
