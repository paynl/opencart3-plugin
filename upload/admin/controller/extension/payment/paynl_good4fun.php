<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlGood4fun extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2628;
    protected $_paymentMethodName = 'paynl_good4fun';

    protected $_defaultLabel = 'Good4fun';
}
