<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlFlyingblueplus extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3615;
    protected $_paymentMethodName = 'paynl_flyingblueplus';

    protected $_defaultLabel = 'Flying blue+';
}
