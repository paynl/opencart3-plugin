<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir.'/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynl3 extends Pay_Model {
    
}
