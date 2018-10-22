<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlPodiumkadokaart extends Pay_Model
{
    protected $_paymentOptionId = 816;
    protected $_paymentMethodName = 'paynl_podiumkadokaart';
}