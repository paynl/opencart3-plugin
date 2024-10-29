<?php

require_once DIR_SYSTEM . '/../Pay/vendor/autoload.php';

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Util\Exchange;
use PayNL\Sdk\Model\Pay\PayStatus;

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

        $orderStatusId = $this->request->get['orderStatusId'];

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

        $exchange = new Exchange();

        # Process the exchange request
        $payOrder = $exchange->process();    
        $transactionId = $payOrder->getReference();
        $action = $exchange->getAction();

        $modelName = 'model_extension_payment_' . $this->_paymentMethodName;
        if ($action == 'pending') {
            $message = 'ignoring PENDING';
            die("TRUE|" . $message);
        } elseif (empty($transactionId)) {
            die("TRUE|ignoring, invalid arguments");
        } elseif (substr($action, 0, 6) == 'refund') {
            $message = 'ignoring REFUND';

            if ($this->config->get('payment_paynl_general_refund_processing')) {
                $status = $this->$modelName->processTransaction($transactionId);
                $message = "Status updated to $status";
            }
            die("TRUE|" . $message);
        } elseif ($action == 'capture') {
            $message = 'ignoring COMPLETE';
            die("TRUE|" . $message);
        } elseif ($action == 'cancel') {
            $message = 'ignoring CANCELED';
            die("TRUE|" . $message);
        } else {
            try {
                $this->$modelName->log('Exchange: ' . $action . ' transactionId: ' . $transactionId);
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
