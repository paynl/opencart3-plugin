<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlCult extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 3177;
    protected $_paymentMethodName = 'paynl_cult';
    protected $_defaultLabel = 'Cult+';
}
