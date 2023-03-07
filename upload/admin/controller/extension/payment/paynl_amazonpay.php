<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlAmazonpay extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1903;
    protected $_paymentMethodName = 'paynl_amazonpay';

    protected $_defaultLabel = 'Amazon Pay';
}
