<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlOverboeking extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 136;
    protected $_paymentMethodName = 'paynl_overboeking';

    protected $_defaultLabel = 'Handmatige overboeking';
}
