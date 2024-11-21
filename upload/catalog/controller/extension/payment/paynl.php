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

    public function addFastCheckoutButtons(&$route, &$data, &$output) {
        $configButtonPlaces = $this->config->get('payment_paynl_ideal_button_places');
        if (!is_array($configButtonPlaces) || !in_array('Cart', $configButtonPlaces)) {
            return;
        }

        $this->prepareOutput($output);
        $payMethodsWithFastCheckout = $this->getFastCheckoutButtons();

        if (!empty($payMethodsWithFastCheckout)) {
            $data['fast_checkout_buttons'] = array_filter($payMethodsWithFastCheckout);
            $fastCheckoutButtonsHtml = $this->load->view('payment/fast_checkout_buttons', $data);

            $checkoutButtonUrl = $data['checkout'];
            $checkoutButtonText = $data['button_checkout'];

            $checkoutButtonHtml = '<a href="' . $checkoutButtonUrl . '" class="btn btn-primary">' . $checkoutButtonText . '</a>';

            $output = str_replace($checkoutButtonHtml, $checkoutButtonHtml . $fastCheckoutButtonsHtml, $output);
        }
    }

    public function addFastCheckoutMiniCartButtons(&$route, &$data, &$output) {
        $configButtonPlaces = $this->config->get('payment_paynl_ideal_button_places');
        if (!is_array($configButtonPlaces) || !in_array('mini_cart', $configButtonPlaces)) {
            return;
        }

        $styleTag = '<link href="catalog/view/theme/default/stylesheet/paynl.css" rel="stylesheet" type="text/css">';
        $output = str_replace('<div id="cart" class="btn-group btn-block">', $styleTag . '<div id="cart" class="btn-group btn-block">', $output);

        $payMethodsWithFastCheckout = $this->getFastCheckoutButtons();

        if (!empty($payMethodsWithFastCheckout)) {
            $data['fast_checkout_buttons'] = array_filter($payMethodsWithFastCheckout);
            $fastCheckoutButtonsHtml = $this->load->view('payment/fast_checkout_mini_cart_buttons', $data);

            $checkoutButtonUrl = $data['checkout'];
            $checkoutButtonText = $data['button_checkout'];

            $searchString = '<a href="' . $checkoutButtonUrl . '"><strong><i class="fa fa-share"></i> ' . $checkoutButtonText . '</strong></a></p>';

            $output = str_replace($searchString, $searchString . $fastCheckoutButtonsHtml, $output);
        }
    }

    public function addFastCheckoutProductPageButtons(&$route, &$data, &$output) {
        $configButtonPlaces = $this->config->get('payment_paynl_ideal_button_places');
        if (!is_array($configButtonPlaces) || !in_array('product', $configButtonPlaces)) {
            return;
        }

        $this->prepareOutput($output);
        $scriptTag = '<script src="catalog/view/theme/default/javascript/paynl.js"></script>';
        $output = str_replace('</head>', $scriptTag . '</head>', $output);

        $payMethodsWithFastCheckout = $this->getFastCheckoutButtons();

        if (!empty($payMethodsWithFastCheckout)) {
            $data['fast_checkout_buttons'] = array_filter($payMethodsWithFastCheckout);
            $fastCheckoutButtonsHtml = $this->load->view('payment/fast_checkout_product_buttons', $data);

            $textLoading = $data['text_loading'];
            $buttonCart = $data['button_cart'];

            $searchString = '<button type="button" id="button-cart" data-loading-text="' . $textLoading . '" class="btn btn-primary btn-lg btn-block">' . $buttonCart . '</button>';
            $output = str_replace($searchString, $searchString . $fastCheckoutButtonsHtml, $output);
        }
    }

    private function prepareOutput(&$output) {
        $styleTag = '<link href="catalog/view/theme/default/stylesheet/paynl.css" rel="stylesheet" type="text/css">';
        $output = str_replace('</head>', $styleTag . '</head>', $output);
    }

    private function getFastCheckoutButtons() {
        $this->load->model('setting/extension');
        $results = $this->model_setting_extension->getExtensions('payment');
        $payMethodsWithFastCheckout = array();

        foreach ($results as $result) {
            if ($this->config->get('payment_' . $result['code'] . '_status')) {
                $fastCheckout = (bool) $this->config->get('payment_' . $result['code'] . '_display_fast_checkout');

                $onlyGuests = (bool) $this->config->get('payment_' . $result['code'] . '_only_guest');
                $customerIsLogged = $this->customer->isLogged();
                $allowedToProceed = !($onlyGuests && $customerIsLogged);

                if ($fastCheckout === true && $allowedToProceed === true) {
                    $payMethodsWithFastCheckout[] = $this->getFastCheckoutButtonLayout($result['code']);
                }
            }
        }

        return $payMethodsWithFastCheckout;
    }

    private function getFastCheckoutButtonLayout($methodCode) {
        switch ($methodCode) {
            case 'paynl_ideal':
                $url = 'index.php?route=extension/payment/' . $methodCode . '/initFastCheckout';

                return '<a href="' . $url . '" data-method="' . $methodCode . '" class="btn btn-lg btn-block fast-checkout-button" style="width: 100%">
                <img src="image/Pay/1fc.png" alt="iDEAL" class="checkout-logo ">
                Fast Checkout
                </a>';
            default: null;
        }
    }
}
