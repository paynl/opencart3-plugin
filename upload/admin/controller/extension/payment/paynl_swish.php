<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlSwish extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3837;
    protected $_paymentMethodName = 'paynl_swish';

    protected $_defaultLabel = 'Swish';
}
