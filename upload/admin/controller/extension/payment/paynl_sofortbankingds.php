<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlSofortbankingds extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 577;
    protected $_paymentMethodName = 'paynl_sofortbankingds';

    protected $_defaultLabel = 'Sofortbanking Digital Services';
}
