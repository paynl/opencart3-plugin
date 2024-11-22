<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlideal extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 10;
    protected $_paymentMethodName = 'paynl_ideal';

    public function initFastCheckout()
    {
        $this->load->model('setting/extension');
        $this->load->model('checkout/order');

        $customer_id = $this->customer->isLogged() ? $this->customer->getId() : 0;
        $firstname = $this->customer->isLogged() ? $this->customer->getFirstName() : 'Guest';
        $lastname = $this->customer->isLogged() ? $this->customer->getLastName() : 'Customer';
        $email = $this->customer->isLogged() ? $this->customer->getEmail() : 'guest@example.com';
        $telephone = $this->customer->isLogged() ? $this->customer->getTelephone() : '';

        $order_data = array(
            'invoice_prefix' => $this->config->get('config_invoice_prefix'),
            'store_id' => $this->config->get('config_store_id'),
            'store_name' => $this->config->get('config_name'),
            'store_url' => $this->config->get('config_url'),
            'customer_id' => $customer_id,
            'customer_group_id' => $this->customer->isLogged() ? $this->customer->getGroupId() : 0,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'telephone' => $telephone,
            'payment_firstname' => '',
            'payment_lastname' => '',
            'payment_company' => '',
            'payment_address_1' => '',
            'payment_address_2' => '',
            'payment_city' => '',
            'payment_postcode' => '',
            'payment_country' => '',
            'payment_country_id' => 0,
            'payment_zone' => '',
            'payment_zone_id' => 0,
            'payment_method' => '',
            'payment_code' => '',
            'payment_address_format' => '',
            'shipping_firstname' => '',
            'shipping_lastname' => '',
            'shipping_company' => '',
            'shipping_address_1' => '',
            'shipping_address_2' => '',
            'shipping_city' => '',
            'shipping_postcode' => '',
            'shipping_country' => '',
            'shipping_country_id' => 0,
            'shipping_zone' => '',
            'shipping_zone_id' => 0,
            'shipping_method' => $this->config->get('payment_paynl_ideal_default_shipping'),
            'shipping_code' => '',
            'shipping_address_format' => ''
        );

        $order_data['products'] = array();
        foreach ($this->cart->getProducts() as $product) {
            $order_data['products'][] = array(
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'total' => $product['total'],
                'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward' => $product['reward'],
                'option' => isset($product['option']) ? $product['option'] : array()
            );
        }

        $order_data['vouchers'] = array();
        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $order_data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'code' => $voucher['code'],
                    'amount' => $voucher['amount']
                );
            }
        }

        $totals = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();

        if (!is_array($taxes)) {
            $taxes = array();
        }

        $results = $this->model_setting_extension->getExtensions('total');
        $sort_order = array();

        foreach ($results as $key => $value) {
            $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
        }

        array_multisort($sort_order, SORT_ASC, $results);

        $totalData = array(
            'totals' => &$totals,
            'total' => &$total,
            'taxes' => &$taxes
        );

        $sort_order = array();
        foreach ($results as $key => $value) {
            if ($this->config->has('total_' . $value['code'] . '_sort_order')) {
                $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
            } else {
                $sort_order[$key] = 0;
            }

            if ($this->config->get('total_' . $value['code'] . '_status')) {
                $this->load->model('extension/total/' . $value['code']);
                $this->{'model_extension_total_' . $value['code']}->getTotal($totalData);
            }
        }
        array_multisort($sort_order, SORT_ASC, $results);

        $order_data['totals'] = array();
        foreach ($totals as $total_item) {
            $order_data['totals'][] = array(
                'code' => $total_item['code'],
                'title' => $total_item['title'],
                'value' => $total_item['value'],
                'sort_order' => isset($total_item['sort_order']) ? $total_item['sort_order'] : 0
            );
        }

        $order_data['total'] = $total;

        $order_data['affiliate_id'] = 0;
        $order_data['commission'] = 0;
        $order_data['marketing_id'] = 0;
        $order_data['tracking'] = '';

        $order_data['comment'] = '';
        $order_data['language_id'] = $this->config->get('config_language_id');
        $order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
        $order_data['currency_code'] = $this->session->data['currency'];
        $order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
        $order_data['ip'] = $this->request->server['REMOTE_ADDR'];
        $order_data['forwarded_ip'] = (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) ? $this->request->server['HTTP_X_FORWARDED_FOR'] : '';
        $order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
        $order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];

        $order_id = $this->model_checkout_order->addOrder($order_data);

        $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'), '', false);

        $order_data['order_id'] = $order_id;

        $response = $this->sendRequest($order_data);

        if ($this->isAjax()) {
            die(json_encode($response));
        }

        header("Location: " . $response['data']['links']['redirect']);
    }

    private function sendRequest($orderData)
    {
        $this->load->model('extension/payment/' . $this->_paymentMethodName);
        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;

        try {
            $this->$modelName->log('start fast checkout payment : ' . $this->_paymentMethodName);
            $apiFastCheckout = new Pay_Api_IdealFastCheckout();
            $apiFastCheckout->setApiToken($this->config->get('payment_paynl_general_apitoken'));
            $apiFastCheckout->setServiceId($this->config->get('payment_paynl_general_serviceid'));

            $apiFastCheckout->setTestmode($this->isTestMode());
            $apiFastCheckout->setOrderNumber($orderData['order_id']);

            $amount = round($orderData['total'] * 100 * $orderData['currency_value']);
            $apiFastCheckout->setAmount($amount);

            //Producten toevoegen
            foreach ($this->cart->getProducts() as $product) {
                $priceWithTax = $this->tax->calculate(
                    $product['price'] * $orderData['currency_value'],
                    $product['tax_class_id'],
                    $this->config->get('config_tax')
                );

                $tax = $priceWithTax - ($product['price'] * $orderData['currency_value']);

                $price = round($priceWithTax * 100);
                $apiFastCheckout->addProduct(
                    $product['product_id'],
                    $product['name'],
                    $price,
                    $product['quantity'],
                    Pay_Helper::calculateTaxClass($priceWithTax, $tax)
                );
            }

            $apiFastCheckout->setDescription('iDEAL Fast Checkout');
            $apiFastCheckout->setReference($orderData['order_id']);
            $apiFastCheckout->setOptimize();
            $apiFastCheckout->setPaymentMethod();

            $returnUrl = $this->url->link('extension/payment/' . $this->_paymentMethodName . '/finishFastCheckout');
            $exchangeUrl = $this->url->link('extension/payment/' . $this->_paymentMethodName . '/exchangeFastCheckout');

            $customExchangeUrl = $this->config->get('payment_paynl_general_custom_exchange_url');
            $customExchangeUrl = is_null($customExchangeUrl) ? '' : trim($customExchangeUrl);

            if (!empty($customExchangeUrl)) {
                $exchangeUrl = htmlspecialchars_decode($customExchangeUrl);
            }

            $apiFastCheckout->setReturnUrl($returnUrl);
            $apiFastCheckout->setExchangeUrl($exchangeUrl);

            $response['data'] = $apiFastCheckout->doRequest();

            $this->$modelName->addTransaction(
                $response['data']['id'],
                $orderData['order_id'],
                $this->_paymentOptionId,
                $amount,
                $apiFastCheckout->getPostData(),
            );
        } catch (Pay_Api_Exception $e) {
            $message = $this->getErrorMessage($e->getMessage());
            $response['error'] = $this->language->get($message);
        } catch (Pay_Exception $e) {
            $response['error'] = "Er is een fout opgetreden: " . $e->getMessage();
        } catch (Exception $e) {
            $response['error'] = "Onbekende fout: " . $e->getMessage();
        }

        return $response;
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

    public function exchangeFastCheckout()
    {
        $webhookData = $_REQUEST;

        if (!isset($webhookData['object']['reference']) || !isset($webhookData['object']['status']['code'])) {
            die("FALSE| Invalid webhook data");
        }

        $order_id = $webhookData['object']['reference'];

        $status = Pay_Helper::getStatus($webhookData['object']['status']['code']);

        $this->load->model('extension/payment/' . $this->_paymentMethodName);
        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;

        $this->load->model('checkout/order');

        try {
            if ($status === Pay_Model::STATUS_COMPLETE) {
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

                $this->$modelName->updateTransactionStatus($webhookData['object']['id'], $status);
                $result = $this->$modelName->updateOrderAfterWebhook($order_id, $paymentData, $shippingData, $customerData);
                if ($result === false) {
                    die("FALSE| Order not found");
                }

                $this->model_checkout_order->addOrderHistory($order_id, 2, 'Order paid via fast checkout.');

                die("TRUE| processed successfully");
            }

            if ($status === Pay_Model::STATUS_CANCELED) {
                $this->model_checkout_order->addOrderHistory($order_id, 7, 'Order cancelled');

                $this->$modelName->updateTransactionStatus($webhookData['object']['id'], $status);

                die("TRUE|Order cancelled");
            }
        } catch (Pay_Api_Exception $e) {
            die("FALSE| Api Error: " . $e->getMessage());
        } catch (Pay_Exception $e) {
            die("FALSE| Plugin Error: " . $e->getMessage());
        } catch (Exception $e) {
            die("FALSE| Unknown Error: " . $e->getMessage());
        }
    }
}
