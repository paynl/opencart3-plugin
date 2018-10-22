<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlYourgift extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1645;
    protected $_paymentMethodName = 'paynl_yourgift';

    protected $_defaultLabel = 'Yourgift';
}
