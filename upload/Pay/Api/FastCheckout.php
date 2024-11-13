<?php

declare(strict_types=1);

class Pay_Api_FastCheckout extends Pay_Api
{
    protected $_version = 'v1';
    protected $_controller = 'orders';
    protected $_action = '';
    protected $_gateway = 'https://connect.payments.nl';
    private $_testmode;
    private $_token;
    private $_orderNumber;
    private $_amount;
    private $_description;
    private $_reference;
    private $_optimize;
    private $_paymentMethod;
    private $_returnUrl;
    private $_exchangeUrl;
    protected $_products = array();
    protected $_postData = array();

    /**
     * @param string $testmode
     * @return void
     */
    public function setTestmode($testmode)
    {
        $this->_testmode = $testmode;
    }

    /**
     * @param $orderNumber
     * @return void
     */
    public function setOrderNumber($orderNumber)
    {
        $this->_orderNumber = $orderNumber;
    }

    /**
     * Set the amount(in cents) of the transaction
     *
     * @param integer $amount
     * @throws Pay_Exception
     * @return void
     */
    public function setAmount($amount)
    {
        if (is_numeric($amount)) {
            $this->_amount = $amount;
        } else {
            throw new Pay_Exception('Amount is niet numeriek', 1);
        }
    }

    /**
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * @param string $reference
     * @return void
     */
    public function setReference($reference)
    {
        $this->_reference = $reference;
    }

    public function setOptimize($contactDetails = true, $shippingAddress = true, $billingAddress = true)
    {
        $this->_optimize = [
            'flow' => 'fastCheckout',
            'contactDetails' => $contactDetails,
            'shippingAddress' => $shippingAddress,
            'billingAddress' => $billingAddress,
        ];
    }

    /**
     * @param int $paymentMethod
     * @return void
     */
    public function setPaymentMethod($paymentMethod = 10)
    {
        $this->_paymentMethod = $paymentMethod;
    }

    /**
     * @param string $returnUrl
     * @return void
     */
    public function setReturnUrl($returnUrl)
    {
        $this->_returnUrl = $returnUrl;
    }

    /**
     * @param string $exchangeUrl
     * @return void
     */
    public function setExchangeUrl($exchangeUrl)
    {
        $this->_exchangeUrl = $exchangeUrl;
    }

    /**
     * Add a product to an order
     * Attention! This is purely an adminstrative option, the amount of the order is not modified.
     *
     * @param string $id
     * @param string $description
     * @param integer $price
     * @param integer $quantity
     * @param integer $vatPercentage
     * @param string $type
     * @throws Pay_Exception
     * @return void
     */
    public function addProduct($id, $description, $price, $quantity, $vatPercentage, $type = "ARTICLE")
    {
        if (!is_numeric($price)) {
            throw new Pay_Exception('Price moet numeriek zijn', 1);
        }
        if (!is_numeric($quantity)) {
            throw new Pay_Exception('Quantity moet numeriek zijn', 1);
        }

        $quantity = $quantity * 1;

        //description mag maar 45 chars lang zijn
        $description = substr($description, 0, 45);

        $arrProduct = array(
            'productId' => $id,
            'description' => $description,
            'price' => ['value' => $price],
            'quantity' => $quantity,
            'vatCode' => $vatPercentage,
            'productType' => $type
        );
        $this->_products[] = $arrProduct;
    }

    protected function _getPostData()
    {
        if (is_int($this->_paymentMethod)) {
            $paymentMethod = [
                'id' => $this->_paymentMethod,
            ];
        } else {
            $paymentMethod = $this->_paymentMethod;
        }

        $postData = [
            'serviceId' => $this->_serviceId,
            'amount' => ['value' => $this->_amount],
            'description' => $this->_description,
            'reference' => $this->_reference,
            'optimize' => $this->_optimize,
            'paymentMethod' => $paymentMethod,
            'returnUrl' => $this->_returnUrl,
            'exchangeUrl' => $this->_exchangeUrl,
            'order' => ['products' => $this->_products]
        ];

        if ($this->_testmode) {
            $postData['integration'] = ['test' => true];
        }

        return $postData;
    }

    /**
     * @return string
     * @throws Pay_Exception
     */
    protected function _getApiUrl()
    {
        if ($this->_version == '') {
            throw new Pay_Exception('version not set', 1);
        }
        if ($this->_controller == '') {
            throw new Pay_Exception('controller not set', 1);
        }

        $host = (!empty($this->_gateway) && substr($this->_gateway, 0, 4) === 'http') ? $this->_gateway : $this->_apiUrl;

        return $host . '/' . $this->_version . '/' . $this->_controller;
    }

    public function doRequest() {
        if ($this->_getPostData()) {

            $url = $this->_getApiUrl();
            $data = $this->_getPostData();

            $strData = http_build_query($data);

            $apiUrl = $url;

            $ch = curl_init();
            if ($this->_requestType == self::REQUEST_TYPE_GET) {
                $apiUrl .= '?' . $strData;
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }

            $apiToken = $this->_apiToken;
            $authorizationHeader = base64_encode("token:$apiToken");
            $headers = [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Basic $authorizationHeader"
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $result = curl_exec($ch);

            if ($result == false) {
                $error = curl_error($ch);
                throw new Pay_Api_Exception("Curl error: ".$error);
            }
            curl_close($ch);

            $arrResult = json_decode($result, true);

            if ($this->validateResult($arrResult)) {
                return $this->_processResult($arrResult);
            }
        }

        return [];
    }

    protected function validateResult($arrResult)
    {
        if (isset($arrResult['links']['redirect'])) {
            return true;
        } else {
            if(isset($arrResult['request']['errorId']) && isset($arrResult['request']['errorMessage']) ){
                throw new Pay_Api_Exception($arrResult['request']['errorId'] . ' - ' . $arrResult['request']['errorMessage']);
            } elseif(isset($arrResult['error'])){
                throw new Pay_Api_Exception($arrResult['error']);
            } else {
                throw new Pay_Api_Exception('Unexpected api result');
            }
        }
    }
}
