<?php

require_once DIR_SYSTEM . '/../Pay/vendor/autoload.php';

use PayNL\Sdk\Config\Config;

class Pay_Controller_Config extends Controller
{
    public $openCart;
    public function __construct($openCart) // phpcs:ignore
    {
        $this->openCart = $openCart;       
    }

    /**
     * @return true
     */
    public function isTestMode()
    {
        $ip = $this->openCart->request->server['REMOTE_ADDR'];
        $ipconfig = $this->openCart->config->get('payment_paynl_general_test_ip');

        if (!empty($ipconfig)) {
            $allowed_ips = explode(',', $ipconfig);

            if (
                in_array($ip, $allowed_ips) &&
                filter_var($ip, FILTER_VALIDATE_IP) &&
                strlen($ip) > 0 &&
                count($allowed_ips) > 0
            ) {
                return true;
            }
        }
        return $this->openCart->config->get('payment_paynl_general_testmode');
    }
  
    /**
     * @param boolean $useCore
     * @return Config
     * @throws Exception
     */
    public function getConfig($useCore = false)
    {
        $config = new Config();
        $config->setUsername($this->getTokencode());
        $config->setPassword($this->getApiToken());

        if (!empty($this->openCart->config->get('payment_paynl_general_gateway')) && $useCore === true) {
            $config->setCore($this->openCart->config->get('payment_paynl_general_gateway'));
        }

        return $config;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->openCart->config->get('payment_paynl_general_apitoken');
    }

    /**
     * @return string
     */
    public function getTokencode()
    {
        return $this->openCart->config->get('payment_paynl_general_tokencode');
    }

    /**
     * @return string
     */
    public function getServiceId()
    {
        return $this->openCart->config->get('payment_paynl_general_serviceid');
    }  

    /**
     * @return string
     */
    public function getCustomExchangeURL()
    {
        return trim($this->openCart->config->get('payment_paynl_general_custom_exchange_url'));
    }    

    /**
     * @return string
     */
    public function getObject()
    {
        $object_string = 'opencart 3 ';
        $object_string .= '1.7.1';
        $object_string .= ' | ';
        $object_string .= VERSION ?? '-';
        $object_string .= ' | ';
        $object_string .= substr(phpversion(), 0, 3);

        return $object_string;
    }
}
