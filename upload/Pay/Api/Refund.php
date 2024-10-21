<?php

class Pay_Api_Refund extends Pay_Api
{
    protected $_version = 'v15';
    protected $_controller = 'transaction';
    protected $_action = 'refund';

    public function setTransactionId($transactionId)
    {
        $this->_postData['transactionId'] = $transactionId;
    }

    public function setAmount($amount)
    {
        $this->_postData['amount'] = (int) $amount;
    }

    public function setCurrency($currency)
    {
        $this->_postData['currency'] = $currency;
    }

    /**
     * @return array|mixed
     * @throws Pay_Exception
     */
    protected function _getPostData() // phpcs:ignore
    {
        $data = parent::_getPostData();
        if ($this->_apiToken == '') {
            throw new Pay_Exception('apiToken not set', 1);
        } else {
            $data['token'] = $this->_apiToken;
        }
        if (!isset($this->_postData['transactionId'])) {
            throw new Pay_Exception('transactionId is not set', 1);
        }
        return $data;
    }
}
