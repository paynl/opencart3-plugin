<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlCartebleue extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 710;
    protected $_paymentMethodName = 'paynl_cartebleue';

    protected $_defaultLabel = 'Cartebleue';
}
