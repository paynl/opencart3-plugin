<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlFashiongiftcard extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1669;
    protected $_paymentMethodName = 'paynl_fashiongiftcard';

    protected $_defaultLabel = 'Fashion Giftcard';
}
