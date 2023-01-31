<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlFestivalcadeau extends Pay_Model
{
    protected $_paymentOptionId = 2511;
    protected $_paymentMethodName = 'paynl_festivalcadeau';
}
