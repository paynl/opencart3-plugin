<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

use PayNL\Sdk\Util\Exchange;

class ControllerExtensionPaymentPaynlideal extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 10;
    protected $_paymentMethodName = 'paynl_ideal';

    public function initFastCheckout()
    {
        if (empty($this->cart->getProducts())) {
            header("Location: " . $this->url->link('checkout/cart'));
            exit;
        }

        $order_data = $this->createBlankFastCheckoutOrder('payment_paynl_ideal_default_shipping');

        $response = $this->sendRequest($order_data);

        if ($this->isAjax()) {
            die(json_encode($response));
        }
        if (isset($response['data']['links']['redirect'])) {
            header("Location: " . $response['data']['links']['redirect']);
        }

        die('SL-code issues');
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

    /**
     * @param $orderId
     * @return string
     */
    private function getCustomerGroupId($orderId)
    {
        $sql = "SELECT `customer_group_id` FROM `" . DB_PREFIX . "order` WHERE order_Id = '" . $this->db->escape($orderId) . "';";
        $result = $this->db->query($sql);

        $rows = $result->rows;

        $result = '';
        foreach ($rows as $row) {
            $result = $row['customer_group_id'];
        }
        return $result;
    }

    /**
     * @return void
     */
    public function exchangeFastCheckout()
    {
        $rawData = file_get_contents('php://input');
        $webhookData = json_decode($rawData, true);

        if (empty($webhookData)) {
            $webhookData = $this->request->post;
        }

        if (!isset($webhookData['object']['reference']) || !isset($webhookData['object']['status']['code'])) {
            die("FALSE| Invalid webhook data");
        }

        $order_id = $webhookData['object']['reference'];

        $this->load->model('setting/setting');
        $transactionId = $webhookData['object']['orderId'];

        try {
            $payConfig = new Pay_Controller_Config($this);
            $config = $payConfig->getConfig();
            $exchange = new Exchange();
            $payOrder = $exchange->process($config);
            $transactionId = $payOrder->getOrderId();
            $action = $exchange->getAction();
            $statusCode = $payOrder->getStatusCode();
            $status = Pay_Helper::getStatus($statusCode);
        } catch (\Exception $e) {
            die('FALSE| Error fetching transaction. ' . $e->getMessage());
        }

        $this->load->model('extension/payment/' . $this->_paymentMethodName);
        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;

        try {
            if ($status === Pay_Model::STATUS_COMPLETE && !empty($webhookData['object']['checkoutData'])) {
                $billingAddress = $webhookData['object']['checkoutData']['billingAddress'];
                $shippingAddress = $webhookData['object']['checkoutData']['shippingAddress'];
                $customer = $webhookData['object']['checkoutData']['customer'];

                $paymentData = [
                    'firstname' => $customer['firstName'],
                    'lastname' => $customer['lastName'],
                    'address_1' => $billingAddress['streetName'] . ' ' . $billingAddress['streetNumber'],
                    'city' => $billingAddress['city'],
                    'postcode' => $billingAddress['zipCode'],
                    'country' => $billingAddress['countryCode'],
                    'method' => $webhookData['object']['payments'][0]['paymentMethod']['id']
                ];

                $shippingData = [
                    'firstname' => $customer['firstName'],
                    'lastname' => $customer['lastName'],
                    'address_1' => $shippingAddress['streetName'] . ' ' . $shippingAddress['streetNumber'],
                    'city' => $shippingAddress['city'],
                    'postcode' => $shippingAddress['zipCode'],
                    'country' => $shippingAddress['countryCode']
                ];

                $customerData = [
                    'email' => $customer['email'],
                    'phone' => $customer['phone'],
                    'lastname' => $customer['lastName'],
                    'firstname' => $customer['firstName'],
                ];

                $this->$modelName->updateTransactionStatus($webhookData['object']['orderId'], $status);
                $result = $this->$modelName->updateOrderAfterWebhook($order_id, $paymentData, $shippingData, $customerData, 'paynl_ideal');
                if ($result === false) {
                    die("FALSE| Order not found");
                }

                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                $order_info['customer_group_id'] = $this->getCustomerGroupId($order_id);
                $order_info['payment_method'] = 'iDeal Fastcheckout';
                $order_info['payment_code'] = 'paynl_ideal';
                $this->model_checkout_order->editOrder($order_id, $order_info);

                $this->model_checkout_order->addOrderHistory($order_id, 2, 'Order paid via fast checkout. iDeal');

                die("TRUE| processed successfully");
            }

            if ($status === Pay_Model::STATUS_CANCELED) {
                $this->model_checkout_order->addOrderHistory($order_id, 7, 'Order cancelled');

                $this->$modelName->updateTransactionStatus($webhookData['object']['orderId'], $status);

                die("TRUE| Order cancelled");
            }
        } catch (Pay_Api_Exception $e) {
            die("FALSE| Api Error: " . $e->getMessage());
        } catch (Pay_Exception $e) {
            die("FALSE| Plugin Error: " . $e->getMessage());
        } catch (Exception $e) {
            die("FALSE| Unknown Error: " . $e->getMessage());
        }

        if ($action == 'pending') {
            die("TRUE| ignoring pending");
        } else {
            die("FALSE| Unexpected status: $status for action: $action");
        }
    }
}
