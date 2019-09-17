<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlApplepay extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2277;
    protected $_paymentMethodName = 'paynl_applepay';

    protected $_defaultLabel = 'Apple Pay';
}
