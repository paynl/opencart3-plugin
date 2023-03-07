<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBlik extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2856;
    protected $_paymentMethodName = 'paynl_blik';

    protected $_defaultLabel = 'Blik';
}
