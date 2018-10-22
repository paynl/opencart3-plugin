<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlWijncadeau extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1666;
    protected $_paymentMethodName = 'paynl_wijncadeau';

    protected $_defaultLabel = 'Wijncadeau';
}
