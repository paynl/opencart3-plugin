<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynl extends Controller
{
    /**
     * @return void
     * @throws Pay_Api_Exception
     */
    public function paynlOnOrderStatusChange()
    {
        $orderId = $_REQUEST['order_id'];
        $orderStatusId = $_REQUEST['order_status_id'];

        $this->load->model('setting/setting');
        $apiToken = $this->model_setting_setting->getSettingValue('payment_paynl_general_apitoken');
        $serviceId = $this->model_setting_setting->getSettingValue('payment_paynl_general_serviceid');

        $autoVoid = $this->config->get('payment_paynl_general_auto_void');
        $autoCapture = $this->config->get('payment_paynl_general_auto_capture');

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
            $this->paynlDoAutoVoid($apiToken, $serviceId, $transactionId, $orderId, $orderStatusId);
        } elseif (
            $orderStatusId == 5 &&
            $transactionState == 'AUTHORIZE' &&
            $autoCapture
        ) {
            $this->paynlDoAutoCapture($apiToken, $serviceId, $transactionId, $orderId, $orderStatusId);
        }
    }

    /**
     * @return void
     * @throws Pay_Api_Exception
     */
    public function paynlOrderInforBefore(&$route, &$args)
    {
        $orderId = $_REQUEST['order_id'];
        $orderStatusId = $_REQUEST['order_status_id'];

        var_dump($orderId);
        exit;

        $this->load->model('setting/setting');
        $apiToken = $this->model_setting_setting->getSettingValue('payment_paynl_general_apitoken');
        $serviceId = $this->model_setting_setting->getSettingValue('payment_paynl_general_serviceid');

        $autoVoid = $this->config->get('payment_paynl_general_auto_void');
        $autoCapture = $this->config->get('payment_paynl_general_auto_capture');

        $this->load->model('extension/payment/paynl3');
        $transaction = $this->model_extension_payment_paynl3->getTransactionFromOrderId($orderId);
        $transactionId = $transaction['id'];

        $apiInfo = new Pay_Api_Info();
        $apiInfo->setApiToken($apiToken);
        $apiInfo->setServiceId($serviceId);
        $apiInfo->setTransactionId($transactionId);
        $infoResult = $apiInfo->doRequest();

        $transactionState = $infoResult['paymentDetails']['stateName'];
      
    }

    /**
     * @param $apiToken
     * @param $serviceId
     * @param $transactionId
     * @param $orderId
     * @param $orderStatusId
     * @return void
     * @throws Pay_Api_Exception
     */
    public function paynlDoAutoVoid($apiToken, $serviceId, $transactionId, $orderId, $orderStatusId)
    {
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

    /**
     * @param $apiToken
     * @param $serviceId
     * @param $transactionId
     * @param $orderId
     * @param $orderStatusId
     * @return void
     * @throws Pay_Api_Exception
     */
    public function paynlDoAutoCapture($apiToken, $serviceId, $transactionId, $orderId, $orderStatusId)
    {
        $apiCapture = new Pay_Api_Capture();
        $apiCapture->setApiToken($apiToken);
        $apiCapture->setServiceId($serviceId);
        $apiCapture->setTransactionId($transactionId);

        $result = $apiCapture->doRequest();

        if (!$result['request']['errorMessage']) {
            $autoVoidMessage = 'Auto-Capture completed';
        } else {
            $autoVoidMessage = 'Auto-Capture: something went wrong. ' . $result['request']['errorMessage'];
        }

        $this->model_checkout_order->addOrderHistory($orderId, $orderStatusId, $autoVoidMessage, false);
    }
}
