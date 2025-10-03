<?php

$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

use PayNL\Sdk\Model\Request\OrderStatusRequest;
use PayNL\Sdk\Model\Request\TransactionStatusRequest;

class ControllerExtensionPaymentPaynl extends Controller
{
    /**
     * @return void
     * @throws Pay_Api_Exception
     */
    public function paynlOrderInfoBefore(&$route, &$data, &$template_code = null)
    {
        $this->load->model('sale/order');
        $order_info = $this->model_sale_order->getOrder($data['order_id']);

        if ((strpos($order_info['payment_code'], 'paynl') !== false)) {
            $template_buffer = $this->getTemplateBuffer($route, $template_code);

            $admin_dir = dirname(dirname(dirname(dirname(__FILE__))));

            $payContent = '<link type="text/css" href="../admin/view/stylesheet/payOrder.css" rel="stylesheet" media="screen">';
            $payContent .= '<script type="text/javascript" src="../admin/view/javascript/payOrder.js"></script>';
            $payContent .= file_get_contents($admin_dir . '/view/template/extension/payment/paynl_order.twig');
            $payContent .= '{{ footer }}';

            $template_buffer = str_replace('{{ footer }}', $payContent, $template_buffer);

            $template_code = $template_buffer;

            $this->load->model('extension/payment/paynl3');
            $transaction = $this->model_extension_payment_paynl3->getTransactionFromOrderId($data['order_id']);
            $transactionId = $transaction['id'];

            $this->load->model('setting/setting');

            $payConfig = new Pay_Controller_Config($this);

            try {
                $orderStatusRequest = new OrderStatusRequest($transactionId);
                $orderStatusRequest->setConfig($payConfig->getConfig(true));
                $payTransaction = $orderStatusRequest->start();
            } catch (\Exception $e) {
                $payTransaction = null;
            }

            if (empty($payTransaction) || ($payTransaction instanceof \PayNL\Sdk\Model\Pay\PayOrder && ($payTransaction->isPaid() || $payTransaction->isAuthorized()))) {
                $transactionStatusRequest = new TransactionStatusRequest($transactionId);
                $transactionStatusRequest->setConfig($payConfig->getConfig(true));
                $payGmsOrder = $transactionStatusRequest->start();
                if ($payGmsOrder->isRefunded() || empty($payTransaction)) {
                    $payTransaction = $payGmsOrder;
                }
                $alreadyRefunded = $payGmsOrder->getAmountRefunded();
            }

            $data['paynl_order_id'] = $this->request->get['order_id'];
            $data['paynl_transaction_id'] = $transactionId;

            $data['paynl_status_code'] = $payTransaction->getStatusCode();
            $data['paynl_status_name'] = $payTransaction->getStatusName();
            $data['paynl_currency'] = $payTransaction->getCurrency();

            $data['cart_amount'] = number_format((float) $order_info['total'], 2, '.', '');
            $data['paynl_amount'] = number_format((float) $payTransaction->getAmount(), 2, '.', '');
            $data['cart_currency'] = $order_info['currency_code'];

            $data['show_refund'] = ($payTransaction->isPaid() || $payTransaction->isRefundedPartial());
            $data['show_capture'] = ($payTransaction->isAuthorized() || $payTransaction->getStatus()['code'] == 97);

            if ($data['show_refund']) {
                $data['paynl_amount_value'] = number_format((float) ($payTransaction->getAmount()), 2, '.', '');
                if ($payTransaction->getCurrency() == 'EUR') {
                    $data['paynl_amount_refunded'] = $alreadyRefunded;
                    $data['paynl_amount'] = number_format((float) $payTransaction->getAmount() - $alreadyRefunded, 2, '.', '');
                    $data['paynl_amount_value'] = number_format((float) ($payTransaction->getAmount() - $alreadyRefunded), 2, '.', '');
                }
                $data['ajax_url'] = $this->url->link('extension/payment/' . $order_info['payment_code'], 'user_token=' . $this->session->data['user_token'] . '&transaction_id=' . $transactionId . '&action=refund');
                $data['text_button'] = 'Refund';
                $data['text_description'] = 'Amount to refund';
                $data['text_confirm'] = 'Are you sure want to refund this amount: %amount% ?';
                $data['paynl_amount_field'] = $data['paynl_amount_refunded'];
                $data['amount_field_text'] = 'Refunded';
                $data['show_refunded_field'] = true;
            } elseif ($data['show_capture']) {
                $data['paynl_amount_captured'] = number_format((float) ($payTransaction->getCapturedAmount()->getValue() / 100), 2, '.', '');
                $data['paynl_amount_value'] = number_format((float) ($data['paynl_amount'] - $data['paynl_amount_captured']), 2, '.', '');
                $data['ajax_url'] = $this->url->link('extension/payment/' . $order_info['payment_code'], 'user_token=' . $this->session->data['user_token'] . '&transaction_id=' . $transactionId . '&action=capture');
                $data['ajax_url_void'] = $this->url->link('extension/payment/' . $order_info['payment_code'], 'user_token=' . $this->session->data['user_token'] . '&transaction_id=' . $transactionId . '&action=void');
                $data['text_button'] = 'Capture';
                $data['text_description'] = 'Amount to capture';
                $data['text_confirm'] = 'Are you sure want to capture this amount: %amount% ?';
                $data['show_refunded_field'] = false;
                $data['show_void'] = (($payTransaction->isAuthorized() || $payTransaction->getStatus()['code'] == 97) && $data['paynl_amount_captured'] == 0);
                $data['text_confirm_void'] = 'Are you sure want to void this amount: %amount% ?';
            }

            return null;
        }
    }

    protected function getTemplateBuffer($route, $event_template_buffer)
    {
        if ($event_template_buffer) {
            return $event_template_buffer;
        }

        $dir_template = DIR_TEMPLATE;

        $template_file = $dir_template . $route . '.twig';
        if (file_exists($template_file) && is_file($template_file)) {
            $template_file = $this->modCheck($template_file);
            return file_get_contents($template_file);
        }

        trigger_error("Cannot find template file for route '$route'");
        exit;
    }

    protected function modCheck($file)
    {

        $original_file = $file;
        if (defined('DIR_MODIFICATION')) {
            if ($this->startsWith($file, DIR_APPLICATION)) {
                if (file_exists(DIR_MODIFICATION . 'admin/' . substr($file, strlen(DIR_APPLICATION)))) {
                    $file = DIR_MODIFICATION . 'admin/' . substr($file, strlen(DIR_APPLICATION));
                }
            } elseif ($this->startsWith($file, DIR_SYSTEM)) {
                if (file_exists(DIR_MODIFICATION . 'system/' . substr($file, strlen(DIR_SYSTEM)))) {
                    $file = DIR_MODIFICATION . 'system/' . substr($file, strlen(DIR_SYSTEM));
                }
            }
        }

        if (class_exists('VQMod', false)) {
            if (VQMod::$directorySeparator) {
                if (strpos($file, 'vq2-') !== false) {
                    return $file;
                }
                if ($original_file != $file) {
                    return VQMod::modCheck($file, $original_file);
                }
                return VQMod::modCheck($original_file);
            }
        }
        return $file;
    }

    protected function startsWith($haystack, $needle)
    {
        if (strlen($haystack) < strlen($needle)) {
            return false;
        }
        return (substr($haystack, 0, strlen($needle)) == $needle);
    }
}
