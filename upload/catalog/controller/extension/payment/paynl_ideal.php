<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ControllerExtensionPaymentPaynlideal extends Pay_Controller_Payment
{
    protected $_paymentOptionId = 10;
    protected $_paymentMethodName = 'paynl_ideal';

    public function initFastCheckout() {
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
}
