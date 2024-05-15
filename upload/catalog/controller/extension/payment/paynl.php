<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynl extends Controller
{
    public function paynlOnOrderStatusChange()
    {
        $orderId = $_REQUEST['order_id'];
        $orderStatusId = $_REQUEST['order_status_id'];

        $this->load->model('setting/setting');
        $apiToken = $this->model_setting_setting->getSettingValue('payment_paynl_general_apitoken');
        $serviceId = $this->model_setting_setting->getSettingValue('payment_paynl_general_serviceid');

        $autoVoid = $this->config->get('payment_paynl_general_auto_void');

        $this->load->model('extension/payment/paynl3');
        $transaction = $this->model_extension_payment_paynl3->getTransactionFromOrderId($orderId);
        $transactionId = $transaction['id'];

        $apiInfo = new Pay_Api_Info();
        $apiInfo->setApiToken($apiToken);
        $apiInfo->setServiceId($serviceId);
        $apiInfo->setTransactionId($transactionId);
        $infoResult = $apiInfo->doRequest();

        $transactionState = $infoResult['paymentDetails']['stateName'];

        if (
            $orderStatusId == 7 &&
            $transactionState == 'AUTHORIZE' &&
            $autoVoid
        ) {
            $apiVoid = new Pay_Api_Void();
            $apiVoid->setApiToken($apiToken);
            $apiVoid->setServiceId($serviceId);
            $apiVoid->setTransactionId($transactionId);

            $result = $apiVoid->doRequest();

            if (!$result['request']['errorMessage']) {
                $autoVoidMessage = 'Auto-Void completed';
            } else {
                $autoVoidMessage = 'Auto-Void: something went wrong. ' . $result['request']['errorMessage'];
            }

            $this->model_checkout_order->addOrderHistory($orderId, $orderStatusId, $autoVoidMessage, false);
        }
    }
}