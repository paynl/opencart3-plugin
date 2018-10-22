<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlVVVGiftcard extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1714;
    protected $_paymentMethodName = 'paynl_vvvgiftcard';

    protected $_defaultLabel = 'VVV Giftcard';
}
