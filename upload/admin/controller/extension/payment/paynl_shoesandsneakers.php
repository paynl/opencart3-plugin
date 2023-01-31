<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlShoesandsneakers extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 2937;
    protected $_paymentMethodName = 'paynl_shoesandsneakers';

    protected $_defaultLabel = 'Shoes and sneakers';
}
