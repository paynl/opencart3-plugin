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

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'order_id' => $order_data['order_id'],
            'total_amount' => number_format($totalAmount, 2, '.', '')
        ]));
    }

    public function handleResult()
    {
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        $order_data = $this->session->data['fast_checkout_paypal_order'];
        $order_data['payment_method'] = [
            'id' => $this->_paymentOptionId,
            'input' => [
                'orderId' => $data['orderID']
            ]
        ];

        //TODO: need to add request to paynl to capture payment and get payer info
        $response = $this->sendRequest($order_data);

        $this->response->setOutput(json_encode($response));



    }
}
