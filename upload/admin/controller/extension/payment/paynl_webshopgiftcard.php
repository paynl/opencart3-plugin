<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlWebshopgiftcard extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 811;
    protected $_paymentMethodName = 'paynl_webshopgiftcard';

    protected $_defaultLabel = 'Webshopgiftcard';
}
