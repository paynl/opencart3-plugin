<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlmistercash extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 436;
    protected $_paymentMethodName = 'paynl_mistercash';
}
