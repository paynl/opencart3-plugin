<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlWechatpay extends Pay_Controller_Admin
{
    protected $_paymentOptionId = 1978;
    protected $_paymentMethodName = 'paynl_wechatpay';

    protected $_defaultLabel = 'Wechat Pay';
}
