<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlBiercheque extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2622;
    protected $_paymentMethodName = 'paynl_biercheque';

    protected $_defaultLabel = 'Biercheque';
}
