<?php

/**
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName
 */

class Pay_Api
{
    private const REQUEST_TYPE_POST = 1;
    private const REQUEST_TYPE_GET = 0;

    protected $_apiUrl = 'https://rest-api.pay.nl';
    protected $_version = 'v3';
    protected $_controller = '';
    protected $_action = '';
    protected $_serviceId = '';
    protected $_apiToken = '';
    protected $_gateway = '';
    protected $_requestType = self::REQUEST_TYPE_POST;
    protected $_postData = array();


    /**
     * Set the serviceid
     * The serviceid always starts with SL- and can be found on: https://admin.pay.nl/programs/programs
     *
     * @param string $serviceId
     * @return void
     */
    public function setServiceId($serviceId)
    {
        $this->_serviceId = $serviceId;
    }

    /**
     * Set the API token
     * The API token is used to identify your company.
     * The API token can be found on: https://admin.pay.nl/my_merchant on the bottom
     *
     * @param string $apiToken
     * @return void
     */
    public function setApiToken($apiToken)
    {
        $this->_apiToken = $apiToken;
    }

    /**
     * @param string $gateway
     * @return void
     */
    public function setApiBase($gateway)
    {
        $this->_gateway = trim($gateway);
    }

    /**
     * @return object
     */
    protected function _getPostData() // phpcs:ignore
    {
        return $this->_postData;
    }

    /**
     * @param object $data
     * @return object
     */
    protected function _processResult($data) // phpcs:ignore
    {
        return $data;
    }

    /**
     * @return string
     * @throws Pay_Exception
     */
    protected function _getApiUrl() // phpcs:ignore
    {
        if ($this->_version == '') {
            throw new Pay_Exception('version not set', 1);
        }
        if ($this->_controller == '') {
            throw new Pay_Exception('controller not set', 1);
        }
        if ($this->_action == '') {
            throw new Pay_Exception('action not set', 1);
        }

        $host = (!empty($this->_gateway) && substr($this->_gateway, 0, 4) === 'http') ? $this->_gateway : $this->_apiUrl;

        return $host . '/' . $this->_version . '/' . $this->_controller . '/' . $this->_action . '/json/';
    }

    /**
     * @return object
     */
    public function getPostData()
    {
        return $this->_getPostData();
    }

    /**
     * @return object|void
     * @throws Pay_Api_Exception
     */
    public function doRequest()
    {
        if ($this->_getPostData()) {
            $url = $this->_getApiUrl();
            $data = $this->_getPostData();

            $strData = http_build_query($data);

            $apiUrl = $url;

            $ch = curl_init();
            if ($this->_requestType == self::REQUEST_TYPE_GET) {
                $apiUrl .= '?' . $strData;
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $strData);
            }


            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);


            if ($result == false) {
                $error = curl_error($ch);
                throw new Pay_Api_Exception("Curl error: " . $error);
            }
            curl_close($ch);

            $arrResult = json_decode($result, true);

            if ($this->validateResult($arrResult)) {
                return $this->_processResult($arrResult);
            }
        }
    }

    /**
     * @return boolean|void
     * @throws Pay_Api_Exception
     */
    protected function validateResult($arrResult)
    {
        if ($arrResult['request']['result'] == 1) {
            return true;
        } else {
            if (isset($arrResult['request']['errorId']) && isset($arrResult['request']['errorMessage'])) {
                throw new Pay_Api_Exception($arrResult['request']['errorId'] . ' - ' . $arrResult['request']['errorMessage']);
            } elseif (isset($arrResult['error'])) {
                throw new Pay_Api_Exception($arrResult['error']);
            } else {
                throw new Pay_Api_Exception('Unexpected api result');
            }
        }
    }
}
