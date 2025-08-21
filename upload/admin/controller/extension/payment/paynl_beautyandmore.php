<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBeautyandmore extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4173;
    protected $_paymentMethodName = 'paynl_beautyandmore';

    protected $_defaultLabel = 'Beauty & More cadeaukaart';
}
