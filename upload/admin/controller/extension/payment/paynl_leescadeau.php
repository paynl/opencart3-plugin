<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlLeescadeau extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 4758;
    protected $_paymentMethodName = 'paynl_leescadeau';

    protected $_defaultLabel = 'Lees cadeau';
}
