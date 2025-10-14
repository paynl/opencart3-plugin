<?php

/**
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName
 */

require_once DIR_SYSTEM . '/../Pay/vendor/autoload.php';

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\TransactionRefundRequest;
use PayNL\Sdk\Model\Request\OrderCaptureRequest;
use PayNL\Sdk\Model\Request\OrderVoidRequest;

class Pay_Controller_Admin extends Controller
{
    protected $_paymentOptionId;
    protected $_paymentMethodName;
    protected $_defaultLabel;

    protected $data = array();
    protected $error;

    public const BUTTON_PLACES = array(
        ['value' => 'Cart', 'key' => 'cart'],
        ['value' => 'Mini cart', 'key' => 'mini_cart'],
        ['value' => 'Product', 'key' => 'product']
    );

    /**
     * @param string $field
     * @return string|null
     */
    private function configGet($field)
    {
        $config = $this->config;
        $configValue = $config->get('payment_paynl_general_' . $field);

        if (is_null($configValue)) {
            return $config->get('payment_' . $this->_paymentMethodName . '_' . $field);
        }

        return $configValue;
    }

    /**
     * @return void
     */
    public function index()
    {
        $this->load->language('extension/payment/' . $this->_paymentMethodName);

        $this->document->addStyle('view/stylesheet/pay.css');
        $this->document->addScript('view/javascript/pay.js');

        $data = array();

        $stringsToTranslate = array(
            'entry_status',
            'button_save',
            'button_cancel',
            'text_enabled',
            'text_disabled',
            'text_yes',
            'text_no',
            'entry_geo_zone',
            'text_confirm_start_tooltip',
            'text_confirm_start',
            'text_send_statusupdates_tooltip',
            'text_send_statusupdates',
            'entry_sort_order',
            'text_status_pending',
            'text_status_pending_tooltip',
            'text_status_complete',
            'text_status_complete_tooltip',
            'text_status_canceled',
            'text_status_canceled_tooltip',
            'text_minimum_amount',
            'text_maximum_amount',
            'text_payment_instructions',
            'text_payment_instructions_tooltip',
            'text_display_icon',
            'text_display_icon_tooltip'
        );

        foreach ($stringsToTranslate as $string) {
            $data[$string] = $this->language->get($string);
        }

        $this->load->model('setting/setting');
        $this->document->setTitle($this->language->get('heading_title'));

        $settings = $this->model_setting_setting->getSetting('payment_' . $this->_paymentMethodName);
        $settings = array_merge($settings, $this->request->post);
        $reqMethod = $this->request->server['REQUEST_METHOD'];

        if ((!empty($this->request->get['downloadlogs']) ? $this->request->get['downloadlogs'] : false)) {
            $this->downloadLogs();
        }

        $data['availability_fast_checkout'] = false;
        if (property_exists($this, '_fastCheckout') && $this->_fastCheckout === true) {
            $data['availability_fast_checkout'] = true;
            $data['fast_checkout'] = 'payment_' . $this->_paymentMethodName . '_display_fast_checkout';

            $defaultShipping = 'payment_' . $this->_paymentMethodName . '_default_shipping';
            $data['fast_checkout_default_shipping_name'] = $defaultShipping;
            $data['fast_checkout_default_shipping'] = isset($settings[$defaultShipping]) ? $settings[$defaultShipping] : '';

            $onlyGuest = 'payment_' . $this->_paymentMethodName . '_only_guest';
            $data['fast_checkout_only_guest_name'] = $onlyGuest;
            $data['fast_checkout_only_guest'] = isset($settings[$onlyGuest]) ? $settings[$onlyGuest] : '';

            $buttonPlaces = 'payment_' . $this->_paymentMethodName . '_button_places';
            $data['fast_checkout_button_places_name'] = $buttonPlaces;
            $data['fast_checkout_checked_button_places'] = isset($settings[$buttonPlaces]) ? $settings[$buttonPlaces] : '';

            $data['button_places_list'] = self::BUTTON_PLACES;

            $this->load->model('setting/extension');

            $installed_shipping_methods = $this->model_setting_extension->getInstalled('shipping');

            $data['shipping_methods'] = array();
            foreach ($installed_shipping_methods as $code) {
                if ($this->config->get('shipping_' . $code . '_status')) {
                    $data['shipping_methods'][] = array(
                        'code' => $code,
                        'title' => $this->config->get('shipping_' . $code . '_title') ?: ucfirst($code)
                    );
                }
            }

            if (property_exists($this, '_clientId') && $this->_clientId === true) {
                $clientIdName = 'payment_' . $this->_paymentMethodName . '_client_id';
                $data['fast_checkout_client_id_name'] = $clientIdName;
                $data['fast_checkout_client_id'] = isset($settings[$clientIdName]) ? $settings[$clientIdName] : '';

                $clientToken = 'payment_' . $this->_paymentMethodName . '_client_token';
                $data['fast_checkout_client_token_name'] = $clientToken;
                $data['fast_checkout_client_token'] = isset($settings[$clientToken]) ? $settings[$clientToken] : '';
                ;
            }
        }

        if ($reqMethod == 'POST') {
            $generalValid = $this->validateGeneral();

            if ($this->getPost('message')) {
                $this->sendSuggestionsForm($this->getPost('message'), $this->getPost('email'), $this->getPost('pluginverison'));
            }

            if ($this->getPost('versionCheck')) {
                $this->checkVersion($this->getPost('versionCheck'));
            }

            if ($generalValid) {
                $settingsGeneral = array(
                    'payment_paynl_general_apitoken' => $settings['payment_paynl_general_apitoken'],
                    'payment_paynl_general_serviceid' => $settings['payment_paynl_general_serviceid'],
                    'payment_paynl_general_tokencode' => $settings['payment_paynl_general_tokencode'],
                    'payment_paynl_general_testmode' => $settings['payment_paynl_general_testmode'],
                    'payment_paynl_general_gateway' => trim($settings['payment_paynl_general_gateway']),
                    'payment_paynl_general_prefix' => $settings['payment_paynl_general_prefix'],
                    'payment_paynl_general_refund_processing' => $settings['payment_paynl_general_refund_processing'],
                    'payment_paynl_general_auto_void' => $settings['payment_paynl_general_auto_void'],
                    'payment_paynl_general_auto_capture' => $settings['payment_paynl_general_auto_capture'],
                    'payment_paynl_general_follow_payment_method' => $settings['payment_paynl_general_follow_payment_method'],
                    'payment_paynl_general_display_icon' => $settings['payment_paynl_general_display_icon'],
                    'payment_paynl_general_custom_exchange_url' => $settings['payment_paynl_general_custom_exchange_url'],
                    'payment_paynl_general_test_ip' => $settings['payment_paynl_general_test_ip'],
                    'payment_paynl_general_logging' => $settings['payment_paynl_general_logging'],
                );
                $this->model_setting_setting->editSetting('payment_paynl_general', $settingsGeneral);

                foreach ($settingsGeneral as $strField => $strvalue) {
                    $this->config->set($strField, $strvalue);
                }
            }

            $bMethodValidate = $this->validatePaymentMethod();
            if ($bMethodValidate) {
                $this->model_setting_setting->editSetting('payment_' . $this->_paymentMethodName, $settings);
            }

            if ($generalValid && $bMethodValidate) {
                $data['success_message'] = $this->language->get('text_success');
            }
        } else {
            if (!empty($this->request->get['action'])) {
                if ($this->request->get['action'] == 'refund') {
                    $returnarray = $this->refund();
                    die(json_encode($returnarray));
                } elseif ($this->request->get['action'] == 'capture') {
                    $returnarray = $this->capture();
                    die(json_encode($returnarray));
                } elseif ($this->request->get['action'] == 'void') {
                    $returnarray = $this->void();
                    die(json_encode($returnarray));
                }
            }
        }

        if ($data['availability_fast_checkout'] == true) {
            $paynlFastCheckoutEventCode = 'paynl_fast_checkout';
            $paynlFastCheckout = $this->model_setting_event->getEventByCode($paynlFastCheckoutEventCode);
            if (!$paynlFastCheckout) {
                $this->model_setting_event->addEvent(
                    $paynlFastCheckoutEventCode,
                    'catalog/view/checkout/cart/after',
                    'extension/payment/paynl/addFastCheckoutButtons'
                );
            }

            $paynlFastCheckoutMiniCartEventCode = 'paynl_fast_checkout_minicart';
            $paynlFastCheckout = $this->model_setting_event->getEventByCode($paynlFastCheckoutMiniCartEventCode);
            if (!$paynlFastCheckout) {
                $this->model_setting_event->addEvent(
                    $paynlFastCheckoutMiniCartEventCode,
                    'catalog/view/common/cart/after',
                    'extension/payment/paynl/addFastCheckoutMiniCartButtons'
                );
            }

            $paynlFastCheckoutProductPageEventCode = 'paynl_fast_checkout_product_page';
            $paynlFastCheckoutProductPageEvent = $this->model_setting_event->getEventByCode($paynlFastCheckoutProductPageEventCode);
            if (!$paynlFastCheckoutProductPageEvent) {
                $this->model_setting_event->addEvent(
                    $paynlFastCheckoutProductPageEventCode,
                    'catalog/view/product/product/after',
                    'extension/payment/paynl/addFastCheckoutProductPageButtons'
                );
            }
        }

        foreach ($settings as $key => $setting) {
            $key = str_replace('payment_' . $this->_paymentMethodName . '_', '', $key);
            $data[$key] = $setting;
        }

        $data['apitoken'] = $settings['payment_paynl_general_apitoken'] ?? $this->configGet('apitoken');
        $data['serviceid'] = $settings['payment_paynl_general_serviceid'] ?? $this->configGet('serviceid');
        $data['tokencode'] = $settings['payment_paynl_general_tokencode'] ?? $this->configGet('tokencode');
        $data['testmode'] = $this->configGet('testmode');
        $data['gateway'] = $this->configGet('gateway');
        $data['prefix'] = $this->configGet('prefix');
        $data['refund_processing'] = $this->configGet('refund_processing');
        $data['auto_void'] = $this->configGet('auto_void');
        $data['auto_capture'] = $this->configGet('auto_capture');
        $data['follow_payment_method'] = $this->configGet('follow_payment_method');
        $data['custom_exchange_url'] = $this->configGet('custom_exchange_url');
        $data['test_ip'] = $this->configGet('test_ip');
        $data['logging'] = $this->configGet('logging');
        $data['display_icon'] = $this->configGet('display_icon');
        $data['text_edit'] = 'Pay. - ' . $this->_defaultLabel;
        $data['error_warning'] = '';
        $data['error_tokencode'] = '';
        $data['error_apitoken'] = '';
        $data['error_serviceid'] = '';
        $data['error_status'] = '';

        if (!empty($this->error)) {
            if (!empty($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
            }
            if (!empty($this->error['tokencode'])) {
                $data['error_tokencode'] = $this->error['tokencode'];
            }
            if (!empty($this->error['apitoken'])) {
                $data['error_apitoken'] = $this->error['apitoken'];
            }
            if (!empty($this->error['serviceid'])) {
                $data['error_serviceid'] = $this->error['serviceid'];
            }
            if (!empty($this->error['tokencode'])) {
                $data['error_tokencode'] = $this->error['tokencode'];
            }
            if (!empty($this->error['status'])) {
                $data['error_status'] = $this->error['status'];
            }
        }

        $data['payment_method_name'] = 'payment_' . $this->_paymentMethodName;
        $data['post_payment'] = isset($this->_postPayment);

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (empty($data['label'])) {
            $data['label'] = $this->_defaultLabel;
        }

        $data['confirm_on_start'] = !isset($data['confirm_on_start']) ? 1 : $data['confirm_on_start'];
        $data['send_status_updates'] = !isset($data['send_status_updates']) ? '1' : $data['send_status_updates'];
        $data['completed_status'] = empty($data['completed_status']) ? 2 : $data['completed_status'];
        $data['canceled_status'] = empty($data['canceled_status']) ? 7 : $data['canceled_status'];
        $data['refunded_status'] = empty($data['refunded_status']) ? 11 : $data['refunded_status'];
        $data['pending_status'] = empty($data['pending_status']) ? 1 : $data['pending_status'];
        $data['heading_title'] = $this->document->getTitle();

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['action'] = $this->url->link('extension/payment/' . $this->_paymentMethodName, 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/' . $this->_paymentMethodName, 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['url'] = $this->url->link('extension/payment/' . $this->_paymentMethodName, 'user_token=' . $this->session->data['user_token'], true);

        $data['current_IP'] = $this->request->server['REMOTE_ADDR'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/paynl3', $data));
    }

    /**
     * @param string $field
     * @return string|null
     */
    private function getPost($field)
    {
        $postArr = $this->request->post;
        return isset($postArr[$field]) ? $postArr[$field] : null;
    }

    /**
     * @return boolean
     */
    public function validateGeneral()
    {
        $apiToken = $this->getPost('payment_paynl_general_apitoken');
        $serviceId = $this->getPost('payment_paynl_general_serviceid');
        $tokencode = $this->getPost('payment_paynl_general_tokencode');

        if (!$this->user->hasPermission('modify', "extension/payment/$this->_paymentMethodName")) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($serviceId)) {
            $this->error['serviceid'] = $this->language->get('error_no_serviceid');
        } elseif (!preg_match('/SL-\d{4}-\d{4}/', $serviceId)) {
            $this->error['serviceid'] = $this->language->get('error_wrong_serviceid');
        }

        if (empty($apiToken)) {
            $this->error['apitoken'] = $this->language->get('error_no_apitoken');
        } elseif (strlen($apiToken) < 40) {
            $this->error['apitoken'] = $this->language->get('error_wrong_apitoken');
        }

        if (empty($tokencode)) {
            $this->error['tokencode'] = $this->language->get('text_tokencode');
        } elseif (!preg_match('/AT-\d{4}-\d{4}/', $tokencode)) {
            $this->error['tokencode'] = $this->language->get('error_wrong_tokencode');
        }

        try {
            if (!empty($serviceId) && !empty($apiToken) && !empty($tokencode)) {
                $this->load->model('extension/payment/paynl3');
                $reqGateway = trim($this->getPost('payment_paynl_general_gateway'));
                $gateway = (!empty($reqGateway) && substr($reqGateway, 0, 4) == 'http') ? $reqGateway : null;
                $this->model_extension_payment_paynl3->refreshPaymentOptions($serviceId, $apiToken, $tokencode, $gateway);
            }
        } catch (PayException $e) {
            $this->error['warning'] = $e->getFriendlyMessage();
        } catch (Exception $e) {
            $this->error['warning'] = $e->getMessage();
        }


        return empty($this->error);
    }

    /**
     * @return boolean
     */
    public function validatePaymentMethod()
    {
        try {
            $this->load->model('extension/payment/paynl3');
            $paymentOption = $this->model_extension_payment_paynl3->getPaymentOption($this->_paymentOptionId);
            $status = $this->request->post['payment_' . $this->_paymentMethodName . '_status'];
            if (!$paymentOption && $status == 1) {
                $this->error['status'] = $this->language->get('error_not_activated');
            }
        } catch (Exception $e) {
            $this->error['apitoken'] = $e->getMessage();
        }
        if (empty($this->error)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return void
     */
    public function install()
    {
        $this->load->model('extension/payment/paynl3');

        $this->model_extension_payment_paynl3->createTables();
        if ($this->config->get('payment_paynl_general_prefix') == null) {
            $this->load->model('setting/setting');
            $settings = $this->model_setting_setting->getSetting('payment_' . $this->_paymentMethodName);
            $settingsGeneral = array(
                'payment_paynl_general_apitoken' => $this->config->get('payment_paynl_general_apitoken'),
                'payment_paynl_general_serviceid' => $this->config->get('payment_paynl_general_serviceid'),
                'payment_paynl_general_tokencode' => $this->config->get('payment_paynl_general_tokencode'),
                'payment_paynl_general_testmode' => $this->config->get('payment_paynl_general_testmode'),
                'payment_paynl_general_gateway' => $this->config->get('payment_paynl_general_gateway'),
                'payment_paynl_general_prefix' => 'Order ',
                'payment_paynl_general_refund_processing' => $this->config->get('payment_paynl_general_refund_processing'),
                'payment_paynl_general_auto_void' => $this->config->get('payment_paynl_general_auto_void'),
                'payment_paynl_general_auto_capture' => $this->config->get('payment_paynl_general_auto_capture'),
                'payment_paynl_general_follow_payment_method' => 1,
                'payment_paynl_general_display_icon' => $this->config->get('payment_paynl_general_display_icon'),
                'payment_paynl_general_custom_exchange_url' => $this->config->get('payment_paynl_general_custom_exchange_url'),
                'payment_paynl_general_test_ip' => $this->config->get('payment_paynl_general_test_ip'),
                'payment_paynl_general_logging' => $this->config->get('payment_paynl_general_logging')
            );
            $this->model_setting_setting->editSetting('payment_paynl_general', $settingsGeneral);
            $this->model_setting_setting->editSetting('payment_' . $this->_paymentMethodName, $settings);
        }

        if (!$this->model_setting_event->getEventByCode('paynl_on_order_status_change')) {
            $this->model_setting_event->addEvent(
                'paynl_on_order_status_change',
                'catalog/controller/api/order/history/after',
                'extension/payment/paynl/paynlOnOrderStatusChange'
            );
        }

        if (!$this->model_setting_event->getEventByCode('paynl_set_order_tab')) {
            $this->model_setting_event->addEvent(
                'paynl_set_order_tab',
                'admin/view/sale/order_info/before',
                'extension/payment/paynl/paynlOrderInfoBefore'
            );
        }
    }

    /**
     * @param string $suggestions_form_message
     * @param string $suggestions_form_email
     * @return void
     */
    public function sendSuggestionsForm($suggestions_form_message, $suggestions_form_email, $suggestions_form_plugin_version)
    {
        try {
            $opencartVersion = VERSION;
            $pluginVersion = strtolower($suggestions_form_plugin_version);
            $phpVersion = phpversion();
            $message = isset($suggestions_form_message) ? nl2br($suggestions_form_message) : null;

            $email = null;
            if (isset($suggestions_form_email) && !empty($suggestions_form_email)) {
                $email = '<b>Client Email:</b><span style="width: 100%;box-sizing: border-box; display:inline-block; padding: 10px; border:1px solid #cccccc;">' . strtolower($suggestions_form_email) . '</span><br/><br/>'; // phpcs:ignore
            }

            if (empty($message)) {
                throw new Exception('Empty message');
            }

            $to = 'webshop@pay.nl';
            $subject = 'Feature Request Opencart3';
            $body = '
            <table role="presentation" style="margin-top:50px; margin-bottom:50px; width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;">
                <tr>
                    <td align="center" style="padding:0;">
                        <table role="presentation" style="width:600px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;">
                            <tr>
                                <td style="padding:25px;">
                                    <h1 style="font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;">Pay. Suggestion</h1>
                                    <p style="margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;">
                                        Opencart version: ' . $opencartVersion . '.<br/>
                                        Pay. plugin version: ' . $pluginVersion . '.<br/>
                                        PHP version: ' . $phpVersion . '.
                                        <br/><br/>
                                        ' . $email . '
                                        <b>Message:</b>
                                        <span style="width: 100%;box-sizing: border-box; display:inline-block; padding: 10px; border:1px solid #cccccc;">' . $message . '</span>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            ';
            $headers = "Content-Type: text/html; charset=UTF-8";

            mail($to, $subject, $body, $headers);
            $result = true;
        } catch (Exception $e) {
            $result = false;
        }
        header('Content-Type: application/json;charset=UTF-8');
        $returnarray = array(
            'success' => $result
        );
        die(json_encode($returnarray));
    }

    /**
     * @param string $version
     * @return void
     */
    private function checkVersion($version)
    {
        $result = false;
        $url = 'https://api.github.com/repos/paynl/opencart3-plugin/releases';
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'User-Agent:' . $_SERVER['HTTP_USER_AGENT']
            )
        );

        $context = stream_context_create($options);

        try {
            $output = file_get_contents($url, false, $context);
            $json = json_decode($output);

            $response = '';
            if (isset($json[0])) {
                $response = $json[0]->tag_name;
                $result = true;
            }
        } catch (\Exception $e) {
            $response = '';
        }
        header('Content-Type: application/json;charset=UTF-8');
        $returnarray = array(
            'success' => $result,
            'version' => $response,
        );
        die(json_encode($returnarray));
    }

    /**
     * @return void
     */
    private function downloadLogs()
    {
        if (file_exists(DIR_LOGS)) {
            if (class_exists('ZipArchive') && is_writable(DIR_LOGS)) {
                $file = DIR_LOGS . '/logs.zip';
                $zipArchive = new ZipArchive();
                $zipArchive->open($file, (ZipArchive::CREATE | ZipArchive::OVERWRITE));
                if (file_exists(DIR_LOGS . 'error.log')) {
                    $zipArchive->addFile(DIR_LOGS . 'error.log', 'error.log');
                }
                if (file_exists(DIR_LOGS . 'pay.log')) {
                    $zipArchive->addFile(DIR_LOGS . 'pay.log', 'pay.log');
                }
                $zipArchive->close();
            } else {
                $file = DIR_LOGS . '/pay.log';
            }
            if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                unlink(DIR_LOGS . '/logs.zip');
                exit;
            }
        }
    }

    /**
     * @return array
     */
    private function refund()
    {
        $json = array();
        $transactionId = $this->request->get['transaction_id'] ?? null;
        $amount = (float) $this->request->get['amount'] ?? null;
        $currency = $this->request->get['currency'] ?? null;
        try {
            $payConfig = new Pay_Controller_Config($this);
            $transactionRefundRequest = new TransactionRefundRequest($transactionId, $amount, $currency);
            $transactionRefundRequest->setConfig($payConfig->getConfig());
            $transactionRefundRequest->start();
            $json['success'] = 'Pay. refunded ' . $currency . ' ' . $this->request->get['amount'] . ' successfully!';
        } catch (\Exception $e) {
            $json['error'] = 'Pay. couldn\'t refund, please try again later.' . $e->getMessage();
        }
        return $json;
    }

    /**
     * @return array
     */
    private function capture()
    {
        $json = array();
        $transactionId = $this->request->get['transaction_id'] ?? null;
        $amount = (float) $this->request->get['amount'] ?? null;
        $currency = $this->request->get['currency'] ?? null;
        try {
            $payConfig = new Pay_Controller_Config($this);
            $orderCaptureRequest = new OrderCaptureRequest($transactionId);
            $orderCaptureRequest->setAmount($amount);
            $orderCaptureRequest->setConfig($payConfig->getConfig());
            $orderCaptureRequest->start();
            $json['success'] = 'Pay. capture ' . $currency . ' ' . $this->request->get['amount'] . ' successfully!';
        } catch (\Exception $e) {
            $json['error'] = 'Pay. couldn\'t capture, please try again later.' . $e->getMessage();
        }
        return $json;
    }

    /**
     * @return array
     */
    public function void()
    {
        $json = array();
        $transactionId = $this->request->get['transaction_id'] ?? null;
        $amount = (float) $this->request->get['amount'] ?? null;
        $currency = $this->request->get['currency'] ?? null;
        try {
            $payConfig = new Pay_Controller_Config($this);
            $orderVoidRequest = new OrderVoidRequest($transactionId);
            $orderVoidRequest->setConfig($payConfig->getConfig());
            $orderVoidRequest->start();
            $json['success'] = 'Pay. voided ' . $currency . ' ' . $this->request->get['amount'] . ' successfully!';
        } catch (\Exception $e) {
            $json['error'] = 'Pay. couldn\'t void, please try again later.' . $e->getMessage();
        }
        return $json;
    }
}
