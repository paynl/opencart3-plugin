<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlFestivalcadeau extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2511;
    protected $_paymentMethodName = 'paynl_festivalcadeau';

    protected $_defaultLabel = 'Festival Cadeaukaart';
}
