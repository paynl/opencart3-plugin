<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBioscoopbon extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2133;
    protected $_paymentMethodName = 'paynl_bioscoopbon';

    protected $_defaultLabel = 'Bioscoopbon';
}
