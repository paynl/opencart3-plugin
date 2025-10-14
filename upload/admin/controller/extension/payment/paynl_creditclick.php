<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlCreditclick extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2107;
    protected $_paymentMethodName = 'paynl_creditclick';

    protected $_defaultLabel = 'CreditClick';
}
