<?php

class Pay_Controller_Admin extends Controller
{
    protected $_paymentOptionId;
    protected $_paymentMethodName;
    protected $_defaultLabel;

    protected $data = array();
    protected $error;

    /**
     * @param $field
     * @return null
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
            'entry_status', 'button_save', 'button_cancel', 'text_enabled', 'text_disabled', 'text_yes', 'text_no',
            'entry_geo_zone', 'text_confirm_start_tooltip', 'text_confirm_start', 'text_send_statusupdates_tooltip',
            'text_send_statusupdates', 'entry_sort_order', 'text_status_pending', 'text_status_pending_tooltip',
            'text_status_complete', 'text_status_complete_tooltip', 'text_status_canceled', 'text_status_canceled_tooltip',
            'text_minimum_amount', 'text_maximum_amount', 'text_payment_instructions', 'text_payment_instructions_tooltip',
            'text_display_icon', 'text_display_icon_tooltip'
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
                  'payment_paynl_general_testmode' => $settings['payment_paynl_general_testmode'],
                  'payment_paynl_general_gateway' => trim($settings['payment_paynl_general_gateway']),
                  'payment_paynl_general_prefix' => $settings['payment_paynl_general_prefix'],
                  'payment_paynl_general_refund_processing' => $settings['payment_paynl_general_refund_processing'],
                  'payment_paynl_general_auto_void' => $settings['payment_paynl_general_auto_void'],
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
        }

        foreach ($settings as $key => $setting) {
            $key = str_replace('payment_' . $this->_paymentMethodName . '_', '', $key);
            $data[$key] = $setting;
        }

        $data['apitoken'] = $this->configGet('apitoken');
        $data['serviceid'] = $this->configGet('serviceid');
        $data['testmode'] = $this->configGet('testmode');
        $data['gateway'] = $this->configGet('gateway');
        $data['prefix'] = $this->configGet('prefix');
        $data['refund_processing'] = $this->configGet('refund_processing');
        $data['auto_void'] = $this->configGet('auto_void');
        $data['follow_payment_method'] = $this->configGet('follow_payment_method');
        $data['custom_exchange_url'] = $this->configGet('custom_exchange_url');
        $data['test_ip'] = $this->configGet('test_ip');
        $data['logging'] = $this->configGet('logging');
        $data['display_icon'] = $this->configGet('display_icon');
        $data['text_edit'] = 'Pay. - ' . $this->_defaultLabel;
        $data['error_warning'] = '';
        $data['error_apitoken'] = '';
        $data['error_serviceid'] = '';
        $data['error_status'] = '';

        if (!empty($this->error)) {
            if (!empty($this->error['warning'])) {
                $data['error_warning'] = $this->error['warning'];
            }
            if (!empty($this->error['apitoken'])) {
                $data['error_apitoken'] = $this->error['apitoken'];
            }
            if (!empty($this->error['serviceid'])) {
                $data['error_serviceid'] = $this->error['serviceid'];
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
     * @param $field
     * @return null
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

        if (!$this->user->hasPermission('modify', "extension/payment/$this->_paymentMethodName")) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($serviceId)) {
            $this->error['serviceid'] = $this->language->get('error_no_serviceid');
        } elseif (empty($apiToken)) {
            $this->error['apitoken'] = $this->language->get('error_no_apitoken');
        } else {
            try {
                $this->load->model('extension/payment/paynl3');
                $reqGateway = trim($this->getPost('payment_paynl_general_gateway'));
                $gateway = (!empty($reqGateway) && substr($reqGateway, 0, 4) == 'http') ? $reqGateway : null;

                $this->model_extension_payment_paynl3->refreshPaymentOptions($serviceId, $apiToken, $gateway);
            } catch (Pay_Api_Exception $e) {
                $this->error['apitoken'] = $this->language->get('error_api_error') . $e->getMessage();
            } catch (Pay_Exception $e) {
                $this->error['apitoken'] = $this->language->get('error_error_occurred') . $e->getMessage();
            } catch (Exception $e) {
                $this->error['apitoken'] = $e->getMessage();
            }
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
                'payment_paynl_general_testmode' => $this->config->get('payment_paynl_general_testmode'),
                'payment_paynl_general_gateway' => $this->config->get('payment_paynl_general_gateway'),
                'payment_paynl_general_prefix' => 'Order ',
                'payment_paynl_general_refund_processing' => $this->config->get('payment_paynl_general_refund_processing'),
                'payment_paynl_general_auto_void' => $this->config->get('payment_paynl_general_auto_void'),
                'payment_paynl_general_follow_payment_method' => 1,
                'payment_paynl_general_display_icon' => $this->config->get('payment_paynl_general_display_icon'),
                'payment_paynl_general_custom_exchange_url' => $this->config->get('payment_paynl_general_custom_exchange_url'),
                'payment_paynl_general_test_ip' => $this->config->get('payment_paynl_general_test_ip'),
                'payment_paynl_general_logging' => $this->config->get('payment_paynl_general_logging')
            );
            $this->model_setting_setting->editSetting('payment_paynl_general', $settingsGeneral);
            $this->model_setting_setting->editSetting('payment_' . $this->_paymentMethodName, $settings);
        }

        $this->model_setting_event->addEvent(
            'paynl_on_order_status_change',
            'catalog/controller/api/order/history/after',
            'extension/payment/paynl/paynlOnOrderStatusChange');
    }

    /**
     * @param $suggestions_form_message
     * @param $suggestions_form_email
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
     * @param $version
     * @return void
     */
    private function checkVersion($version)
    {
        $result = false;
        $url = 'https://api.github.com/repos/paynl/opencart3-plugin/releases';
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'User-Agent:' . $_SERVER['HTTP_USER_AGENT']));

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
}
