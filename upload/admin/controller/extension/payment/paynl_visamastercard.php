<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlVisamastercard extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 706;
    protected $_paymentMethodName = 'paynl_visamastercard';

    protected $_defaultLabel = 'Visa/Mastercard';
}
