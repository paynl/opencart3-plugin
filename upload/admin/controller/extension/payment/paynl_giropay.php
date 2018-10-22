<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlGiropay extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 694;
    protected $_paymentMethodName = 'paynl_giropay';

    protected $_defaultLabel = 'Giropay';
}
