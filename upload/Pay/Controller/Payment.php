<?php

require_once DIR_SYSTEM . '/../Pay/vendor/autoload.php';

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Util\Exchange;

class Pay_Controller_Payment extends Controller
{
    protected $_paymentOptionId;
    protected $_paymentMethodName;
    protected $data = array();

    /**
     * @return mixed
     */
    public function index()
    {
        $this->load->language('extension/payment/paynl3');
        $this->data['text_choose_bank'] = $this->language->get('text_choose_bank');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_loading'] = $this->language->get('text_loading');

        $this->data['paymentMethodName'] = $this->_paymentMethodName;

        // paymentoption ophalen
        $this->load->model('extension/payment/' . $this->_paymentMethodName);
        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;
        $paymentOption = $this->$modelName->getPaymentOption($this->_paymentOptionId);

        if (!$paymentOption) {
            die('Payment method not available');
        }

        $this->data['instructions'] = $this->config->get('payment_' . $this->_paymentMethodName . '_instructions');

        $this->data['optionSubList'] = array();

        if (!empty($this->config->get('payment_' . $this->_paymentMethodName . '_coc'))) {
            $this->data['coc'] = $this->config->get('payment_' . $this->_paymentMethodName . '_coc');
        }

        $company = (isset($this->session->data['payment_address']['company'])) ? trim($this->session->data['payment_address']['company']) : '';
        if (!empty($this->config->get('payment_' . $this->_paymentMethodName . '_vat')) && strlen($company) > 0) {
            $this->data['vat'] = $this->config->get('payment_' . $this->_paymentMethodName . '_vat');
        }

        if (!empty($this->config->get('payment_' . $this->_paymentMethodName . '_dob'))) {
            $this->data['dob'] = $this->config->get('payment_' . $this->_paymentMethodName . '_dob');
        }

        $this->data['terms'] = '';

        return $this->load->view('payment/paynl3', $this->data);
    }

    /**
     * @return void
     */
    public function startTransaction()
    {
        $this->load->language('extension/payment/paynl3');
        $this->load->model('extension/payment/' . $this->_paymentMethodName);
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $response = array();
        try {
            $modelName = 'model_extension_payment_' . $this->_paymentMethodName;
            $this->$modelName->log('start payment : ' . $this->_paymentMethodName);
            $transaction = new Pay_Controller_Transaction($this);
            $response['success'] = $transaction->startTransaction($order_info, $this->_paymentOptionId, $this->_paymentMethodName);
        } catch (PayException $e) {
            $response['error'] = "Er is een fout opgetreden: " . $e->getMessage();
        } catch (Exception $e) {
            $response['error'] = "Onbekende fout: " . $e->getMessage();
        }

        die(json_encode($response));
    }

    public function finish()
    {
        $this->load->model('extension/payment/' . $this->_paymentMethodName);
        $orderStatusId = $this->request->get['statusCode'];
        $status = Pay_Helper::getStatus($orderStatusId);

        if (isset($status) && ($status == Pay_Model::STATUS_COMPLETE || $status == Pay_Model::STATUS_PENDING)) {
            header("Location: " . $this->url->link('checkout/success'));
        } else {
            $this->load->language('extension/payment/paynl3');

            $action = $this->request->get['orderStatusId'];
            if ($action == -90) {
                $this->session->data['error'] = $this->language->get('text_cancel');
            } elseif ($action == -63) {
                $this->session->data['error'] = $this->language->get('text_denied');
            }

            header("Location: " . $this->url->link('checkout/checkout'));
        }
        die();
    }

    /**
     * @return void
     */
    public function exchange()
    {
        $this->load->model('extension/payment/' . $this->_paymentMethodName);

        $payConfig = new Pay_Controller_Config($this);
        $config = $payConfig->getConfig();
        $exchange = new Exchange();
        $payOrder = $exchange->process($config);
        $transactionId = $payOrder->getOrderId();
        $action = $exchange->getAction();
        $statusName = Pay_Helper::getStatus($payOrder->getStatusCode());

        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;
        if ($action == 'pending') {
            $message = 'ignoring PENDING';
            die("TRUE|" . $message);
        } elseif (empty($transactionId)) {
            die("TRUE|ignoring, invalid arguments");
        } elseif (substr($action, 0, 6) == 'refund') {
            $message = 'ignoring REFUND';
            if ($this->config->get('payment_paynl_general_refund_processing')) {
                if ($statusName != Pay_Model::STATUS_REFUNDED) {
                    die("FALSE|unexpected status for refund: $statusName");
                }
                $status = $this->$modelName->processTransaction($transactionId, $payOrder);
                $message = "Status updated to $status";
            }
            die("TRUE|" . $message);
        } elseif ($action == 'cancel') {
            $message = 'ignoring CANCELED';
            die("TRUE|" . $message);
        } else {
            try {
                $this->$modelName->log('Exchange: ' . $action . ' transactionId: ' . $transactionId);
                $status = $this->$modelName->processTransaction($transactionId, $payOrder);
                $message = "Status updated to $status";
                die("TRUE|" . $message);
            } catch (Pay_Api_Exception $e) {
                $message = "Api Error: " . $e->getMessage();
            } catch (Pay_Exception $e) {
                $message = "Plugin error: " . $e->getMessage();
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
            }
            die("FALSE|" . $message);
        }
    } 

    /**
     * @return bool
     */
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * @param $message
     * @return string
     */
    public function getErrorMessage($message)
    {
        $message = strtolower(trim($message));

        if (
            stripos($message, 'minimum amount') !== false
            || stripos($message, 'maximum amount') !== false
            || stripos($message, 'Amount is not allowed') !== false
        ) {
            $errorMessage = 'text_pay_api_error_amount';
        } elseif (stripos($message, 'is not activated for this sales location') !== false) {
            $errorMessage = 'text_pay_api_error_activated';
        } elseif (stripos($message, 'not allowed in country') !== false) {
            $errorMessage = 'text_pay_api_error_country';
        } else {
            $errorMessage = 'text_pay_api_error_general';
        }

        return $errorMessage;
    }

    protected function createBlankFastCheckoutOrder($defaultShippingMethodConfigKey)
    {
        $this->load->model('setting/extension');
        $this->load->model('checkout/order');

        $customer_id = $this->customer->isLogged() ? $this->customer->getId() : 0;
        $firstname = $this->customer->isLogged() ? $this->customer->getFirstName() : 'Guest';
        $lastname = $this->customer->isLogged() ? $this->customer->getLastName() : 'Customer';
        $email = $this->customer->isLogged() ? $this->customer->getEmail() : 'guest@example.com';
        $telephone = $this->customer->isLogged() ? $this->customer->getTelephone() : '';

        $order_data = array(
            'invoice_prefix'        => $this->config->get('config_invoice_prefix'),
            'store_id'              => $this->config->get('config_store_id'),
            'store_name'            => $this->config->get('config_name'),
            'store_url'             => $this->config->get('config_url'),
            'customer_id'           => $customer_id,
            'customer_group_id'     => $this->customer->isLogged() ? $this->customer->getGroupId() : 0,
            'firstname'             => $firstname,
            'lastname'              => $lastname,
            'email'                 => $email,
            'telephone'             => $telephone,
            'payment_firstname'     => '',
            'payment_lastname'      => '',
            'payment_company'       => '',
            'payment_address_1'     => '',
            'payment_address_2'     => '',
            'payment_city'          => '',
            'payment_postcode'      => '',
            'payment_country'       => '',
            'payment_country_id'    => 0,
            'payment_zone'          => '',
            'payment_zone_id'       => 0,
            'payment_method'        => '',
            'payment_code'          => '',
            'payment_address_format'=> '',
            'shipping_firstname'    => '',
            'shipping_lastname'     => '',
            'shipping_company'      => '',
            'shipping_address_1'    => '',
            'shipping_address_2'    => '',
            'shipping_city'         => '',
            'shipping_postcode'     => '',
            'shipping_country'      => '',
            'shipping_country_id'   => 0,
            'shipping_zone'         => '',
            'shipping_zone_id'      => 0,
            'shipping_method'       => $this->config->get($defaultShippingMethodConfigKey),
            'shipping_code'         => '',
            'shipping_address_format' => ''
        );

        $order_data['products'] = array();
        foreach ($this->cart->getProducts() as $product) {
            $order_data['products'][] = array(
                'product_id'   => $product['product_id'],
                'name'         => $product['name'],
                'model'        => $product['model'],
                'quantity'     => $product['quantity'],
                'price'        => $product['price'],
                'total'        => $product['total'],
                'tax'          => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward'       => $product['reward'],
                'option'       => isset($product['option']) ? $product['option'] : array()
            );
        }

        $order_data['vouchers'] = array();
        if (!empty($this->session->data['vouchers'])) {
            foreach ($this->session->data['vouchers'] as $voucher) {
                $order_data['vouchers'][] = array(
                    'description' => $voucher['description'],
                    'code'        => $voucher['code'],
                    'amount'      => $voucher['amount']
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
                'code'  => $total_item['code'],
                'title' => $total_item['title'],
                'value' => $total_item['value'],
                'sort_order'=> isset($total_item['sort_order']) ? $total_item['sort_order'] : 0
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

        return $order_data;
    }

    protected function sendRequest($orderData)
    {
        $this->load->model('extension/payment/' . $this->_paymentMethodName);
        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;

        try {
            $this->$modelName->log('start fast checkout payment : ' . $this->_paymentMethodName);
            $apiFastCheckout = new Pay_Api_FastCheckout();
            $apiFastCheckout->setApiToken($this->config->get('payment_paynl_general_apitoken'));
            $apiFastCheckout->setServiceId($this->config->get('payment_paynl_general_serviceid'));

            $payConfig = new Pay_Controller_Config($this);
            $apiFastCheckout->setTestmode($payConfig->isTestMode());
            $apiFastCheckout->setOrderNumber($orderData['order_id']);

            $amount = round($orderData['total'] * 100 * $orderData['currency_value']);
            $apiFastCheckout->setAmount($amount);
            $apiFastCheckout->setCurrency($orderData['currency_code']);

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

            $apiFastCheckout->setDescription('');
            $apiFastCheckout->setReference($orderData['order_id']);
            $apiFastCheckout->setOptimize();

            $paymentMethod = $orderData['payment_method'] ?:$this->_paymentOptionId;
            $apiFastCheckout->setPaymentMethod($paymentMethod);

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
                $response['data']['orderId'],
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
}
