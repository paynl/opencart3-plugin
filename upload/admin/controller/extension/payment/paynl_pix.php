<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlPix extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4803;
    protected $_paymentMethodName = 'paynl_pix';

    protected $_defaultLabel = 'Pix';
}
