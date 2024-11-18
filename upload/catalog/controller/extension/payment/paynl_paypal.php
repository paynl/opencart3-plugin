<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlpaypal extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 138;
    protected $_paymentMethodName = 'paynl_paypal';

    public function initFastCheckout() {
        $order_data = $this->createBlankFastCheckoutOrder('payment_paynl_paypal_default_shipping');

        $this->session->data['fast_checkout_paypal_order'] = $order_data;

        $totalAmount = $this->cart->getTotal();
        $currency_code = $this->session->data['currency'];
        $formatted_total = $this->currency->format($totalAmount, $currency_code, '', false);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'order_id' => $order_data['order_id'],
            'total_amount' => number_format($formatted_total, 2, '.', ''),
            'currency' => $currency_code
        ]));
    }

    public function handleResult()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }

        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        if (empty($data['orderID'])) {
            $this->response->setOutput(json_encode(['error' => 'Missing orderID']));
            return;
        }

        if (isset($this->session->data['fast_checkout_paypal_order'])) {
            $order_data = $this->session->data['fast_checkout_paypal_order'];
        } else {
            $this->response->setOutput(json_encode(['error' => 'Order data not found']));
            return;
        }

        $order_data['payment_method'] = [
            'id' => $this->_paymentOptionId,
            'input' => [
                'orderId' => $data['orderID']
            ]
        ];

        $response = $this->sendRequest($order_data);

        if (!$response || !isset($response['data']['links']['redirect'])) {
            $this->response->setOutput(json_encode(['error' => 'Payment processing failed']));
            return;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'status' => 'success',
            'redirect_url' => $response['data']['links']['redirect']
        ]));
    }

    /**
     * @return void
     */
    public function finishFastCheckout()
    {
        $this->load->model('extension/payment/' . $this->_paymentMethodName);

        $statusCode = $this->request->get['statusCode'];

        $status = Pay_Helper::getStatus($statusCode);

        if (isset($status) && ($status == Pay_Model::STATUS_COMPLETE || $status == Pay_Model::STATUS_PENDING)) {
            $this->cart->clear();

            header("Location: " . $this->url->link('checkout/success'));
        } else {
            $this->load->language('extension/payment/paynl3');

            $action = $this->request->get['statusCode'];
            if ($action == -90) {
                $this->session->data['error'] = $this->language->get('text_cancel');
            } elseif ($action == -63) {
                $this->session->data['error'] = $this->language->get('text_denied');
            }

            header("Location: " . $this->url->link('checkout/cart'));
        }
        die();
    }

    public function cancelPayment()
    {
        $order = $this->session->data['fast_checkout_paypal_order'];

        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory($order['order_id'], 7, 'Order cancelled');

        $this->load->language('extension/payment/paynl3');

        $this->session->data['error'] = $this->language->get('text_cancel');

        $this->response->redirect($this->url->link('checkout/cart', '', true));
    }

    public function getButtonConfig() {
        $client_id = $this->config->get('payment_paynl_paypal_client_id');
        $currency = $this->session->data['currency'];
        $intent = 'capture';
        $appearsIn = $this->config->get('payment_paynl_paypal_button_places');
        $route = isset($this->request->get['route']) ? $this->request->get['route'] : '';

        $config = [
            'client_id' => $client_id,
            'intent' => $intent,
            'currency' => $currency,
            'appearsIn' => $appearsIn,
            'current_route' => $route
        ];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($config));
    }

    public function exchangeFastCheckout() {
        $rawData = file_get_contents('php://input');
        $webhookData = json_decode($rawData, true);

        if (empty($webhookData)) {
            $webhookData = $this->request->post;
        }

        $orderId = isset($webhookData['object']['payments'][0]['paymentMethod']['input']['orderId'])
            ? $webhookData['object']['payments'][0]['paymentMethod']['input']['orderId']
            : null;

        $accessToken = $this->getAccessToken();
        $paypalOrderDetails = $this->getOrderDetails($orderId, $accessToken);

        if (!$paypalOrderDetails) {
            return;
        }

        $paypalPayer = [
            'firstname' => $paypalOrderDetails['payer']['name']['given_name'],
            'lastname' => $paypalOrderDetails['payer']['name']['surname'],
            'countryCode' => $paypalOrderDetails['payer']['address']['country_code'],
        ];

        $shipping = $paypalOrderDetails['purchase_units'][0]['shipping'];
        $paypalShipping = [
            'full_name' => $shipping['name']['full_name'],
            'address_1' => $shipping['address']['address_line_1'],
            'city' => $shipping['address']['admin_area_2'],
            'country' => $shipping['address']['postal_code'],
            'post_code' => $shipping['address']['country_code'],
        ];

        $order_id = $webhookData['object']['reference'];

        $status = Pay_Helper::getStatus($webhookData['object']['status']['code']);

        $this->load->model('extension/payment/' . $this->_paymentMethodName);
        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;

        $this->load->model('checkout/order');

        if ($status === Pay_Model::STATUS_COMPLETE) {
            $billingAddress = $webhookData['object']['checkoutData']['billingAddress'];
            $shippingAddress = $webhookData['object']['checkoutData']['shippingAddress'];
            $customer = $webhookData['object']['checkoutData']['customer'];

            $paymentData = [
                'firstname' => $customer['firstName'] ?: $paypalPayer['firstname'],
                'lastname' => $customer['lastName'] ?: $paypalPayer['lastname'],
                'address_1' => $billingAddress['streetName'] . ' ' . $billingAddress['streetNumber'],
                'city' => $billingAddress['city'],
                'postcode' => $billingAddress['zipCode'],
                'country' => $billingAddress['countryCode'] ?: $paypalPayer['countryCode'],
                'method' => $webhookData['object']['payments'][0]['paymentMethod']['id']
            ];

            $shippingData = [
                'firstname' => $customer['firstName'] ?: $paypalShipping['full_name'],
                'lastname' => $customer['lastName'],
                'address_1' => $shippingAddress['streetName'] ? $shippingAddress['streetName'] . ' ' . $shippingAddress['streetNumber'] : $paypalShipping['address_1'],
                'city' => $shippingAddress['city'] ?: $paypalShipping['city'],
                'postcode' => $shippingAddress['zipCode'] ?: $paypalShipping['post_code'],
                'country' => $shippingAddress['countryCode'] ?: $paypalShipping['country']
            ];

            $customerData = [
                'email' => $customer['email'] ?: $paypalOrderDetails['payer']['email_address'],
                'phone' => $customer['phone'],
                'lastname' => $customer['lastName'],
                'firstname' => $customer['firstName'],
            ];

            $transactionId = $webhookData['object']['id'];

            $this->$modelName->addTransaction(
                $transactionId,
                $order_id,
                $this->_paymentOptionId,
                $webhookData['object']['amount']['value'],
                ['type' => 'paypal fast checkout'],
            );
            $this->$modelName->updateTransactionStatus($transactionId, $status);
            $this->$modelName->updateOrderAfterWebhook($order_id, $paymentData, $shippingData, $customerData);
            $this->model_checkout_order->addOrderHistory($order_id, 2, 'Order paid via fast checkout.');

            $this->response->setOutput(json_encode(['status' => 'success']));
        }

        if ($status === Pay_Model::STATUS_CANCELED) {
            $this->model_checkout_order->addOrderHistory($order_id, 7, 'Order cancelled');

            $this->$modelName->updateTransactionStatus($webhookData['object']['id'], $status);

            $this->response->setOutput(json_encode(['status' => 'cancelled']));
        }
    }

    private function getAccessToken() {
        $clientId = $this->config->get('payment_' . $this->_paymentMethodName . '_client_id');
        $clientSecret = $this->config->get('payment_' . $this->_paymentMethodName . '_client_token');

        if (empty($clientId) || empty($clientSecret)) {
            $this->log->write('PayPal Error: Missing Client ID or Client Secret.');

            return false;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Accept-Language: en_US"
        ]);


        curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$clientSecret");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        $result = json_decode($response, true);

        return $result['access_token'] ?? false;
    }

    public function getOrderDetails($orderID, $accessToken) {
        if (empty($orderID) || empty($accessToken)) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v2/checkout/orders/{$orderID}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer $accessToken"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
