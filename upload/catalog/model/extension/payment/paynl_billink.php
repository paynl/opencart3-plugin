<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir.'/Pay/Autoload.php';

require_once $autoload;
class ModelExtensionPaymentPaynlBillink extends Pay_Model {
    protected $_paymentMethodName = 'paynl_billink';
    
     public function getLabel(){
        return parent::getLabel();
    }
}
?>