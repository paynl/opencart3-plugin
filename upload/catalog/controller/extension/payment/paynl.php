<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;
require_once DIR_SYSTEM . '/../Pay/vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderVoidRequest;
use PayNL\Sdk\Model\Request\OrderCaptureRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\OrderStatusRequest;

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

        $payConfig = new Pay_Controller_Config(openCart: $this);
        $request = new OrderStatusRequest($transactionId ?? '');
        $request->setConfig($payConfig->getConfig());

        try {
            $transaction = $request->start();
        } catch (PayException $e) {        
            exit();
        }

        $transactionState = $transaction->getStatusName();     

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
        $payConfig = new Pay_Controller_Config(openCart: $this);

        $orderVoidRequest = new OrderVoidRequest($transactionId);
        $orderVoidRequest->setConfig($payConfig->getConfig());    
        try {
            $orderVoidRequest->start();
            $autoVoidMessage = 'Auto-Void completed';
        } catch (PayException $e) {
            $autoVoidMessage = 'Auto-Void: something went wrong. ' . $e->getMessage();
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
        $payConfig = new Pay_Controller_Config($this);

        $orderCaptureRequest = new OrderCaptureRequest($transactionId);
        $orderCaptureRequest->setConfig($payConfig->getConfig());    
        try {
            $orderCaptureRequest->start();
            $autoCaptureMessage = 'Auto-Capture completed';
        } catch (PayException $e) {
            $autoCaptureMessage = 'Auto-Capture: something went wrong. ' . $e->getMessage();
        }     

        $this->model_checkout_order->addOrderHistory($orderId, $orderStatusId, $autoCaptureMessage, false);
    }
}
