<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBancomat extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4809;
    protected $_paymentMethodName = 'paynl_bancomat';

    protected $_defaultLabel = 'Bancomat';
}
