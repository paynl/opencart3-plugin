<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir.'/Pay/Autoload.php';

require_once $autoload;
class ModelExtensionPaymentPaynlCartebleue extends Pay_Model {
    protected $_paymentMethodName = 'paynl_cartebleue';
    
     public function getLabel(){
        return parent::getLabel();
    }
}
?>