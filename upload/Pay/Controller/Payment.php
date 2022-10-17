<?php

class Pay_Controller_Payment extends Controller
{
    protected $_paymentOptionId;
    protected $_paymentMethodName;
    protected $data = array();

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

        if ($this->_paymentOptionId == 10 && !empty($paymentOption['optionSubs'])) {
            $this->data['optionSubList'] = $paymentOption['optionSubs'];
        }

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

    public function startTransaction()
    {
        $this->load->model('extension/payment/' . $this->_paymentMethodName);

        $this->load->model('checkout/order');
        $statusPending = $this->config->get('payment_' . $this->_paymentMethodName . '_pending_status');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $currency_value = $order_info['currency_value'];
        $response = array();
        try {
            $apiStart = new Pay_Api_Start();
            $apiStart->setApiToken($this->config->get('payment_paynl_general_apitoken'));
            $apiStart->setServiceId($this->config->get('payment_paynl_general_serviceid'));
            $apiStart->setTestmode($this->config->get('payment_paynl_general_testmode'));

            if (!empty(trim($this->config->get('payment_paynl_general_gateway')))) {
                $apiStart->setApiBase(trim($this->config->get('payment_paynl_general_gateway')));
            }

            $returnUrl = $this->url->link('extension/payment/' . $this->_paymentMethodName . '/finish');
            $exchangeUrl = $this->url->link('extension/payment/' . $this->_paymentMethodName . '/exchange');

            $apiStart->setFinishUrl($returnUrl);
            $apiStart->setExchangeUrl($exchangeUrl);


            $apiStart->setPaymentOptionId($this->_paymentOptionId);
            $amount = round($order_info['total'] * 100 * $currency_value);
            $apiStart->setAmount($amount);

            $apiStart->setCurrency($order_info['currency_code']);

            $optionSub = null;
            if (!empty($_POST['optionSubId'])) {
                $optionSub = $_POST['optionSubId'];
                $apiStart->setPaymentOptionSubId($optionSub);
            }

            if (!empty($_POST['dob'])) {
                $dob = preg_replace("([^0-9/])", "", htmlentities($_POST['dob']));
            } else {
                $dob = null;
            }

            if (!empty($this->config->get('payment_paynl_general_prefix'))) {
                $description = $this->config->get('payment_paynl_general_prefix') . $order_info['order_id'];
            } else {
                $description = $order_info['order_id'];
            }

            $apiStart->setDescription($description);
            $apiStart->setExtra1($order_info['order_id']);
            $apiStart->setObject('opencart3 1.5.1');


            // Klantdata verzamelen en meesturen
            $strAddress = $order_info['shipping_address_1'] . ' ' . $order_info['shipping_address_2'];
            list($street, $housenumber) = Pay_Helper::splitAddress($strAddress);
            $arrShippingAddress = array(
                'streetName' => $street,
                'streetNumber' => $housenumber,
                'zipCode' => $order_info['shipping_postcode'],
                'city' => $order_info['shipping_city'],
                'countryCode' => $order_info['shipping_iso_code_2'],
            );

            $initialsPayment = substr($order_info['payment_firstname'], 0, 1);
            $initialsShipping = substr($order_info['shipping_firstname'], 0, 1);

            $strAddress = $order_info['payment_address_1'] . ' ' . $order_info['payment_address_2'];
            list($street, $housenumber) = Pay_Helper::splitAddress($strAddress);
            $arrPaymentAddress = array(
                'initials' => $initialsPayment,
                'lastName' => $order_info['payment_lastname'],
                'streetName' => $street,
                'streetNumber' => $housenumber,
                'zipCode' => $order_info['payment_postcode'],
                'city' => $order_info['payment_city'],
                'countryCode' => $order_info['payment_iso_code_2'],
            );


            $arrEnduser = array(
                'initials' => $initialsShipping,
                'phoneNumber' => $order_info['telephone'],
                'lastName' => $order_info['shipping_lastname'],
                'language' => substr($order_info['language_code'], 0, 2),
                'emailAddress' => $order_info['email'],
                'address' => $arrShippingAddress,
                'invoiceAddress' => $arrPaymentAddress,
                'company' => array(
                    'name' => $order_info['payment_company'],
                    'cocNumber' => (!empty($_POST['coc'])) ? $_POST['coc'] : null,
                    'vatNumber' => (!empty($_POST['vat'])) ? $_POST['vat'] : null
                ),
                'dob' => str_replace("/", "-", $dob),
            );

            $apiStart->setEnduser($arrEnduser);

            //Producten toevoegen
            foreach ($this->cart->getProducts() as $product) {
                $priceWithTax = $this->tax->calculate($product['price'] * $currency_value,
                    $product['tax_class_id'], $this->config->get('config_tax'));

                $tax = $priceWithTax - ($product['price'] * $currency_value);

                $price = round($priceWithTax * 100);
                $apiStart->addProduct($product['product_id'], $product['name'],
                    $price, $product['quantity'], Pay_Helper::calculateTaxClass($priceWithTax, $tax));
            }

            $taxes = $this->cart->getTaxes();
            $this->load->model('setting/extension');
            $results = $this->model_setting_extension->getExtensions('total');
            $totals = array();
            $total = 0;
            $arrTotals = array(
                'totals' => &$totals,
                'taxes' => &$taxes,
                'total' => &$total
            );
            $taxesForTotals = array();
            foreach ($results as $result) {
                $taxesBefore = array_sum($arrTotals['taxes']);
                if ($this->config->get('total_' . $result['code'] . '_status')) {
                    $this->load->model('extension/total/' . $result['code']);
                    $this->{'model_extension_total_' . $result['code']}->getTotal($arrTotals);
                    $taxAfter = array_sum($arrTotals['taxes']);
                    $taxesForTotals[$result['code']] = $taxAfter - $taxesBefore;
                }
            }
            foreach ($arrTotals['totals'] as $total_row) {
                if (!in_array($total_row['code'], array('sub_total', 'tax', 'total'))) {
                    if (array_key_exists($total_row['code'], $taxesForTotals)) {
                        $total_row_tax = $taxesForTotals[$total_row['code']];
                    } else {
                        $total_row_tax = 0;
                    }
                    $totalIncl = $total_row['value'] + $total_row_tax;

                    $totalIncl = $totalIncl * $currency_value;
                    $total_row_tax = $total_row_tax * $currency_value;

                    switch($total_row['code']){
                        case 'shipping':
                            $type = "SHIPPING";
                            break;
                        default:
                            $type = "ARTICLE";
                            break;
                    }

                    $apiStart->addProduct($total_row['code'], $total_row['title'], round($totalIncl * 100), 1, Pay_Helper::calculateTaxClass($totalIncl, $total_row_tax), $type);
               }
            }

            $postData = $apiStart->getPostData();

            $result = $apiStart->doRequest();

            //transactie is aangemaakt, nu loggen
            $modelName = 'model_extension_payment_' . $this->_paymentMethodName;
            $this->$modelName->addTransaction($result['transaction']['transactionId'],
                $order_info['order_id'], $this->_paymentOptionId, $amount,
                $postData, $optionSub);

            $message = 'PAY. Transactie aangemaakt. TransactieId: ' . $result['transaction']['transactionId'] . ' .<br />';

            $confirm_on_start = $this->config->get($this->_paymentMethodName . '_confirm_on_start');
            if ($confirm_on_start == 1) {
                $this->model_checkout_order->addOrderHistory($order_info['order_id'], $statusPending, $message, false);
            }

            $response['success'] = $result['transaction']['paymentURL'];
        } catch (Pay_Api_Exception $e) {
            $response['error'] = "De PAY. api gaf de volgende fout: " . $e->getMessage();
        } catch (Pay_Exception $e) {
            $response['error'] = "Er is een fout opgetreden: " . $e->getMessage();
        } catch (Exception $e) {
            $response['error'] = "Onbekende fout: " . $e->getMessage();
        }

        die(json_encode($response));
    }

    public function finish()
    {
        $this->load->model('extension/payment/' . $this->_paymentMethodName);

        $transactionId = $this->request->get['orderId'];

        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;
        try {
            $status = $this->$modelName->processTransaction($transactionId);
        } catch (Exception $e) {
            // we doen er niks mee, want redirecten moeten we sowieso.
        }

        if (isset($status) && ($status == Pay_Model::STATUS_COMPLETE || $status == Pay_Model::STATUS_PENDING)) {
            header("Location: " . $this->url->link('checkout/success'));
        } else {
            $this->load->language('extension/payment/paynl3');

            $action = $this->request->get['orderStatusId'];
            if ($action == -90) {
                $this->session->data['error'] = $this->language->get('text_cancel');
            } else if ($action == -63) {
                $this->session->data['error'] = $this->language->get('text_denied');
            }

            header("Location: " . $this->url->link('checkout/checkout'));
        }
        die();
    }

    public function exchange()
    {
        $this->load->model('extension/payment/' . $this->_paymentMethodName);

        $transactionId = $_REQUEST['order_id'];
        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;
        if ($_REQUEST['action'] == 'pending') {
            $message = 'ignoring PENDING';
            die("TRUE|" . $message);
        } elseif (substr($_REQUEST['action'], 0, 6) == 'refund') {
            $message = 'ignoring REFUND';
            die("TRUE|" . $message);
        } else {
            try {
                $status = $this->$modelName->processTransaction($transactionId);
                $message = "Status updated to $status";
                die("TRUE|" . $message);
            } catch (Pay_Api_Exception $e) {
                $message = "Api Error: " . $e->getMessage();
            } catch (Pay_Exception $e) {
                $message = "Plugin error: " . $e->getMessage();
            } catch (Exception $e) {
                $message = "Unknown error: " . $e->getMessage();
            }
            die("FALSE|" . $message);
        }
    }
}
