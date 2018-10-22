<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlMistercash extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 436;
    protected $_paymentMethodName = 'paynl_mistercash';

    protected $_defaultLabel = 'Mistercash/Bancontact';
}
