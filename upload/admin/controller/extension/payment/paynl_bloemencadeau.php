<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBloemencadeau extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2607;
    protected $_paymentMethodName = 'paynl_bloemencadeau';

    protected $_defaultLabel = 'Bloemen Cadeaukaart';
}
