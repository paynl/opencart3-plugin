<?php

class Pay_Api_Refund extends Pay_Api
{
    protected $_version = 'v19';
    protected $_controller = 'transaction';
    protected $_action = 'refund';

    /**
     * @param $transactionId
     * @return void
     */
    public function setTransactionId($transactionId)
    {
        $this->_postData['transactionId'] = $transactionId;
    }

    /**
     * @param $amount
     * @return void
     */
    public function setAmount($amount)
    {
        $this->_postData['amount'] = $amount;
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
