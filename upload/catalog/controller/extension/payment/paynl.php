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

        $payConfig = new Pay_Controller_Config($this);
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

    /**
     * @param $route
     * @param $data
     * @param $output
     * @return void
     */
    public function addFastCheckoutButtons(&$route, &$data, &$output) {
        if (!$this->isButtonAllowed('cart')) {
            return;
        }

        if (!$this->cart->hasProducts()) {
            return;
        }

        $this->loadResources($output);
        $payMethodsWithFastCheckout = $this->getFastCheckoutButtons(['paypal_container_id' => 'paypal-button-container-2'], 'cart');

        if (!empty($payMethodsWithFastCheckout)) {
            $data['fast_checkout_buttons'] = array_filter($payMethodsWithFastCheckout);
            $fastCheckoutButtonsHtml = $this->load->view('payment/fast_checkout_buttons', $data);

            $checkoutButtonUrl = $data['checkout'];
            $checkoutButtonText = $data['button_checkout'];

            $checkoutButtonHtml = '<a href="' . $checkoutButtonUrl . '" class="btn btn-primary">' . $checkoutButtonText . '</a>';

            $output = str_replace($checkoutButtonHtml, $checkoutButtonHtml . $fastCheckoutButtonsHtml, $output);
        }
    }

    /**
     * @param $route
     * @param $data
     * @param $output
     * @return void
     */
    public function addFastCheckoutMiniCartButtons(&$route, &$data, &$output) {
        if (!$this->isButtonAllowed('mini_cart')) {
            return;
        }

        $this->loadResources($output);
        $payMethodsWithFastCheckout = $this->getFastCheckoutButtons([], 'mini_cart');

        if (!empty($payMethodsWithFastCheckout)) {
            $data['fast_checkout_buttons'] = array_filter($payMethodsWithFastCheckout);
            $fastCheckoutButtonsHtml = $this->load->view('payment/fast_checkout_mini_cart_buttons', $data);

            $checkoutButtonUrl = $data['checkout'];
            $checkoutButtonText = $data['button_checkout'];

            $searchString = '<a href="' . $checkoutButtonUrl . '"><strong><i class="fa fa-share"></i> ' . $checkoutButtonText . '</strong></a></p>';

            $output = str_replace($searchString, $searchString . $fastCheckoutButtonsHtml, $output);
        }
    }

    /**
     * @param $route
     * @param $data
     * @param $output
     * @return void
     */
    public function addFastCheckoutProductPageButtons(&$route, &$data, &$output) {
        if (!$this->isButtonAllowed('product')) {
            return;
        }

        $this->loadResources($output);

        $payMethodsWithFastCheckout = $this->getFastCheckoutButtons(['paypal_container_id' => 'paypal-button-container-2'], 'product');

        if (!empty($payMethodsWithFastCheckout)) {
            $data['fast_checkout_buttons'] = array_filter($payMethodsWithFastCheckout);
            $fastCheckoutButtonsHtml = $this->load->view('payment/fast_checkout_product_buttons', $data);

            $textLoading = $data['text_loading'];
            $buttonCart = $data['button_cart'];

            $searchString = '<button type="button" id="button-cart" data-loading-text="' . $textLoading . '" class="btn btn-primary btn-lg btn-block">' . $buttonCart . '</button>';
            $output = str_replace($searchString, $searchString . $fastCheckoutButtonsHtml, $output);
        }
    }

    private function getFastCheckoutButtons($options = array(), $page = null) {
        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->getExtensions('payment');
        $payMethodsWithFastCheckout = array();

        foreach ($results as $result) {
            if ($this->config->get('payment_' . $result['code'] . '_status')) {
                $fastCheckout = (bool) $this->config->get('payment_' . $result['code'] . '_display_fast_checkout');

                $availablePlaces = $this->config->get('payment_' . $result['code'] . '_button_places');

                if ($availablePlaces == null || !in_array($page, $availablePlaces)) {
                    continue;
                }

                $onlyGuests = (bool) $this->config->get('payment_' . $result['code'] . '_only_guest');
                $customerIsLogged = $this->customer->isLogged();
                $allowedToProceed = !($onlyGuests && $customerIsLogged);

                if ($fastCheckout === true && $allowedToProceed === true) {
                    $paypalContainerId = isset($options['paypal_container_id']) ?  $options['paypal_container_id'] : null;

                    $payMethodsWithFastCheckout[] = $this->getFastCheckoutButtonLayout($result['code'],  $paypalContainerId);
                }
            }
        }

        return $payMethodsWithFastCheckout;
    }

    /**
     * @param $methodCode
     * @return string|void
     */
    private function getFastCheckoutButtonLayout($methodCode, $paypalContainerId) {
        if ($paypalContainerId === null) {
            $paypalContainerId = 'paypal-button-container';
        }

        $url = 'index.php?route=extension/payment/' . $methodCode . '/initFastCheckout';

        switch ($methodCode) {
            case 'paynl_ideal':
                return '<div class="fast-checkout-btn-margin"><a href="' . $url . '" data-method="' . $methodCode . '" class="btn btn-lg btn-block fast-checkout-button" style="width: 100%">
                <img src="image/Pay/1fc.png" alt="iDEAL" class="checkout-logo ">
                Fast Checkout
                </a></div>';
            case 'paynl_paypal':
                return '<div class="fast-checkout-btn-margin" id="' . $paypalContainerId . '" data-init-url="' . $url . '"></div>';
            default: null;
        }
    }

    private function loadResources(&$output) {
        $styleTag = '<link href="catalog/view/theme/default/stylesheet/paynl.css" rel="stylesheet" type="text/css">';
        $scriptTag = '<script src="catalog/view/theme/default/javascript/paynl.js"></script>';

        $output = str_replace('<div id="cart" class="btn-group btn-block">', '<div id="cart" class="btn-group btn-block">' . $styleTag . $scriptTag, $output);
    }

    private function isButtonAllowed($placeName) {
        $configKeys = $this->getButtonPlacesConfigKeys();

        foreach ($configKeys as $configKey) {
            $configButtonPlaces = $this->config->get($configKey);

            if (is_array($configButtonPlaces) && in_array($placeName, $configButtonPlaces)) {
                return true;
            }
        }

        return false;
    }

    private function getButtonPlacesConfigKeys() {
        return [
            'payment_paynl_ideal_button_places',
            'payment_paynl_paypal_button_places',
        ];
    }
}
