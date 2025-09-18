<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBoekencadeau extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4749;
    protected $_paymentMethodName = 'paynl_boekencadeau';

    protected $_defaultLabel = 'Boeken cadeau';
}
