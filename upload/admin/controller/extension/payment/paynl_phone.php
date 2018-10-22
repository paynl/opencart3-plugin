<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlPhone extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1600;
    protected $_paymentMethodName = 'paynl_phone';

    protected $_defaultLabel = 'Telefonisch betalen';
}
