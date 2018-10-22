<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlGezondheidsbon extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 812;
    protected $_paymentMethodName = 'paynl_gezondheidsbon';

    protected $_defaultLabel = 'Gezondheidsbon';
}
