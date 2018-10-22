<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlFashioncheque extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 815;
    protected $_paymentMethodName = 'paynl_fashioncheque';

    protected $_defaultLabel = 'Fashioncheque';
}
