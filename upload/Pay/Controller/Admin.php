<?php

class Pay_Controller_Admin extends Controller
{

    protected $_paymentOptionId;
    protected $_paymentMethodName;
    protected $_defaultLabel;

    protected $data = array();

    protected $error;

    public function index()
    {
        $this->load->language('extension/payment/' . $this->_paymentMethodName);

        $data = array();

        //translations
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
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
	        $settingsGeneral = array(
	        	'payment_paynl_general_apitoken' => $settings['payment_'.$this->_paymentMethodName.'_apitoken'],
	        	'payment_paynl_general_serviceid' => $settings['payment_'.$this->_paymentMethodName.'_serviceid'],
	        	'payment_paynl_general_gateway' => trim($settings['payment_'.$this->_paymentMethodName.'_gateway'])

	        );
	        $this->model_setting_setting->editSetting('payment_paynl_general', $settingsGeneral);

            $this->model_setting_setting->editSetting('payment_' . $this->_paymentMethodName, $settings);
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }


        foreach ($settings as $key => $setting) {
            $key = str_replace('payment_' . $this->_paymentMethodName . '_', '', $key);
            $data[$key] = $setting;
        }

        if(!isset($data['apitoken']) || empty($data['apitoken'])){
            $data['apitoken'] = $this->config->get('payment_paynl_general_apitoken');
        }
        if(!isset($data['serviceid']) || empty($data['serviceid'])){
            $data['serviceid'] = $this->config->get('payment_paynl_general_serviceid');
        }
        if(!isset($data['gateway']) || empty($data['gateway'])){
            $data['gateway'] = $this->config->get('payment_paynl_general_gateway');
        }


        $data['text_edit'] = 'PAY. - ' . $this->_defaultLabel;

        $data['error_warning'] = '';
        $data['error_apitoken'] = '';
        $data['error_serviceid'] = '';


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
        }

        $data['payment_method_name'] = 'payment_' . $this->_paymentMethodName;

        if(!isset($this->_dob)){
            $data['dob_settings'] = false;
        }else{
            $data['dob_settings'] = true;
            if (empty($data['dob'])) $data['dob'] = "0";
        }

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();


        if (empty($data['label'])) $data['label'] = $this->_defaultLabel;

        if (!isset($data['confirm_on_start'])) {
            $data['confirm_on_start'] = '1';
        }

        if (!isset($data['send_status_updates'])) {
            $data['send_status_updates'] = '1';
        }

        if (empty($data['completed_status'])) {
            $data['completed_status'] = 2;
        }
        if (empty($data['canceled_status'])) {
            $data['canceled_status'] = 7;
        }

        if (empty($data['pending_status'])) {
            $data['pending_status'] = 1;
        }

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

    public function validate()
    {
        if (!$this->user->hasPermission('modify', "extension/payment/$this->_paymentMethodName")) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        // als de betaalmethode disabled is maken de instellingen niet uit
        if ($this->request->post['payment_' . $this->_paymentMethodName . '_status'] == 0) {
            return true;
        }

        if (!@$this->request->post['payment_' . $this->_paymentMethodName . '_apitoken']) {
            $this->error['apitoken'] = $this->language->get('error_no_apitoken');
        } else {

            try {
                $this->load->model('extension/payment/paynl3');


                $serviceId = $this->request->post['payment_' . $this->_paymentMethodName . '_serviceid'];
                $apiToken = $this->request->post['payment_' . $this->_paymentMethodName . '_apitoken'];

                $gateway = '';
                if (!empty(trim($this->request->post['payment_' . $this->_paymentMethodName . '_gateway']))){
                    $gateway = trim($this->request->post['payment_' . $this->_paymentMethodName . '_gateway']);
                }

                //eerst refreshen
                $this->model_extension_payment_paynl3->refreshPaymentOptions($serviceId, $apiToken, $gateway);

                $paymentOption = $this->model_extension_payment_paynl3->getPaymentOption($serviceId, $this->_paymentOptionId);

                if (!$paymentOption) {
                    $this->error['apitoken'] = $this->language->get('error_not_activated');
                }
            } catch (Pay_Api_Exception $e) {
                $this->error['apitoken'] = $this->language->get('error_api_error') . $e->getMessage();
            } catch (Pay_Exception $e) {
                $this->error['apitoken'] = $this->language->get('error_error_occurred') . $e->getMessage();
            } catch (Exception $e) {
                $this->error['apitoken'] = $e->getMessage();
            }
        }

        if (!@$this->request->post['payment_' . $this->_paymentMethodName . '_serviceid']) {
            $this->error['serviceid'] = $this->language->get('error_no_serviceid');
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
    }

}
