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

    public function index()
    {
        $this->load->language('extension/payment/' . $this->_paymentMethodName);

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

        if ($reqMethod == 'POST') {
            $generalValid = $this->validateGeneral();

            if ($generalValid) {
                $settingsGeneral = array(
                  'payment_paynl_general_apitoken' => $settings['payment_paynl_general_apitoken'],
                  'payment_paynl_general_serviceid' => $settings['payment_paynl_general_serviceid'],
                  'payment_paynl_general_testmode' => $settings['payment_paynl_general_testmode'],
                  'payment_paynl_general_gateway' => trim($settings['payment_paynl_general_gateway']),
                  'payment_paynl_general_prefix' => $settings['payment_paynl_general_prefix'],
                  'payment_paynl_general_display_icon' => $settings['payment_paynl_general_display_icon'],
                  'payment_paynl_general_icon_style' => $settings['payment_paynl_general_icon_style']
                );
                $this->model_setting_setting->editSetting('payment_paynl_general', $settingsGeneral);

                foreach($settingsGeneral as $strField => $strvalue) {
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
        $data['icon_style'] = $this->configGet('icon_style');
        $data['display_icon'] = $this->configGet('display_icon');
        $data['text_edit'] = 'PAY. - ' . $this->_defaultLabel;
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

        if (empty($data['label'])) $data['label'] = $this->_defaultLabel;

        $data['confirm_on_start'] = !isset($data['confirm_on_start']) ? 1 : $data['confirm_on_start'];
        $data['send_status_updates'] = !isset($data['send_status_updates']) ? '1' : $data['send_status_updates'];
        $data['completed_status'] = empty($data['completed_status']) ? 2 : $data['completed_status'];
        $data['canceled_status'] = empty($data['canceled_status']) ? 7 : $data['canceled_status'];
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

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/paynl3', $data));
    }

    private function getPost($field)
    {
        $postArr = $this->request->post;
        return isset($postArr[$field]) ? $postArr[$field] : null;
    }

    public function validateGeneral()
    {
        $apiToken = $this->getPost('payment_paynl_general_apitoken');
        $serviceId = $this->getPost('payment_paynl_general_serviceid');

        if (!$this->user->hasPermission('modify', "extension/payment/$this->_paymentMethodName")) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($serviceId)) {
            $this->error['serviceid'] = $this->language->get('error_no_serviceid');
        }elseif (empty($apiToken)) {
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
	        	'payment_paynl_general_display_icon' => $this->config->get('payment_paynl_general_display_icon'),
	        	'payment_paynl_general_icon_style' => $this->config->get('payment_paynl_general_icon_style')
	        );
            $this->model_setting_setting->editSetting('payment_paynl_general', $settingsGeneral);
            $this->model_setting_setting->editSetting('payment_' . $this->_paymentMethodName, $settings);
        }
    }

}
