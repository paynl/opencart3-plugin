<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBeautycadeau extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3144;
    protected $_paymentMethodName = 'paynl_beautycadeau';
    protected $_defaultLabel = 'Beauty Cadeau';
}
