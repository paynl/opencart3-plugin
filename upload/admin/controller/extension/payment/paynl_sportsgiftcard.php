<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlSportsgiftcard extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4422;
    protected $_paymentMethodName = 'paynl_sportsgiftcard';

    protected $_defaultLabel = 'Sports giftcard';
}
