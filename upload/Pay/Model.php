<?php

class Pay_Model extends Model {

    const STATUS_PENDING = 'PENDING';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_COMPLETE = 'COMPLETE';

    protected $_paymentOptionId;

    public function createTables() {
        $this->db->query("                
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paynl_transactions` (
                            `id` varchar(255) NOT NULL,
                            `orderId` int(11) NOT NULL,
                            `optionId` int(11) NOT NULL,
                            `optionSubId` int(11) DEFAULT NULL,
                            `amount` int(11) NOT NULL,
                            `status` varchar(255) NOT NULL,
                            `created` int(11) NOT NULL,
                            `last_update` int(11) DEFAULT NULL,
                            `start_data` text NOT NULL,
                            PRIMARY KEY (`id`)
                          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;
		");
        $this->db->query("                
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paynl_paymentoptions` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `optionId` int(11) NOT NULL,
                            `serviceId` varchar(20) NOT NULL,
                            `name` varchar(255) NOT NULL,
                            `img` varchar(255) NOT NULL,
                            `update_date` datetime NOT NULL,
                            PRIMARY KEY (`id`)
                          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;
		");
        $this->db->query("                
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "paynl_paymentoption_subs` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `optionSubId` int(11) NOT NULL,   
                            `paymentOptionId` int(11) NOT NULL,                                                  
                            `name` varchar(255) NOT NULL,
                            `img` varchar(255) NOT NULL,
                            `update_date` datetime NOT NULL,
                            PRIMARY KEY (`id`)
                          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ;
		");
    }

    public function addTransaction($transactionId, $orderId, $optionId, $amount, $startData, $optionSubId = null) {
        $sql = "INSERT INTO `" . DB_PREFIX . "paynl_transactions` (id, orderId, optionId, optionSubId, amount, status, created, start_data) VALUES ("
                . "'" . $this->db->escape($transactionId) . "'"
                . ",'" . $this->db->escape($orderId) . "'"
                . ",'" . $this->db->escape($optionId) . "'"
                . "," . (is_null($optionSubId) ? 'NULL' : "'" . $this->db->escape($optionSubId) . "'")
                . ",'" . $this->db->escape($amount) . "'"
                . ", '" . self::STATUS_PENDING . "'"
                . ", UNIX_TIMESTAMP() "
                . ",'" . $this->db->escape(json_encode($startData)) . "'"
                . ")";
        return $this->db->query($sql);
    }

    public function refreshPaymentOptions($serviceId, $apiToken, $gateway) {      
        $serviceId = $this->db->escape($serviceId);
        //eerst de oude verwijderen
        $sql = "DELETE options,optionsubs  FROM `" . DB_PREFIX . "paynl_paymentoptions` as options "
                . "LEFT JOIN `" . DB_PREFIX . "paynl_paymentoption_subs` as optionsubs ON optionsubs.paymentOptionId = options.id ";
        $this->db->query($sql);

        //nieuwe ophalen
        $api = new Pay_Api_Getservice();
        $api->setApiToken($apiToken);
        $api->setServiceId($serviceId);

        if (!empty($gateway)){
            $api->setApiBase($gateway);
        }

        $result = $api->doRequest();       
   
        foreach ($result['paymentOptions'] as $paymentOption) {
            $img = $paymentOption['img'];

            //variabelen filteren
            $optionId = $this->db->escape($paymentOption['id']);
            $name = $this->db->escape($paymentOption['visibleName']);
            $img = $this->db->escape($img);
            $brand_id = isset($paymentOption['brand']['id']) ? $paymentOption['brand']['id'] : 0;
            $brand_id = $this->db->escape($brand_id);

            $imageArr = array('img' => $img, 'brand_id' => $brand_id);
            $imageJson = json_encode($imageArr);

            $sql = "INSERT INTO `" . DB_PREFIX . "paynl_paymentoptions` "
                    . "(optionId, serviceId, name, img, update_date) VALUES "
                    . "('$optionId', '$serviceId', '$name', '$imageJson', NOW())";
            $this->db->query($sql);

            $internalOptionId = $this->db->getLastId();
            foreach ($paymentOption['paymentOptionSubList'] as $optionSub) {

                $optionSubId = $optionSub['id'];
                $name = $optionSub['visibleName'];
                $img = $optionSub['image'];

                //variabelen filteren
                $optionSubId = $this->db->escape($optionSubId);
                $name = $this->db->escape($name);
                $img = $this->db->escape($img);                   
                
                $sql = "INSERT INTO `" . DB_PREFIX . "paynl_paymentoption_subs` "
                        . "(optionSubId, paymentOptionId, name, img, update_date) VALUES "
                        . "('$optionSubId', $internalOptionId, '$name', '$img', NOW() )";
                $this->db->query($sql);
            }
        }
    }

    public function getPaymentOption($paymentOptionId) {       

        $paymentOptionId = $this->db->escape($paymentOptionId);
        $sql = "SELECT * FROM `" . DB_PREFIX . "paynl_paymentoptions` WHERE optionId = '$paymentOptionId' LIMIT 1;";
        $result = $this->db->query($sql);

        $paymentOption = $result->row;
        if (empty($paymentOption)) {
            return false;
        }

        //kijken of er subs zijn
        $sql = "SELECT * FROM `" . DB_PREFIX . "paynl_paymentoption_subs` WHERE paymentOptionId = '" . $paymentOption['id'] . "' ORDER BY name ASC; ";
        $result = $this->db->query($sql);
        $optionSubs = $result->rows;
        $arrOptionSubs = array();
        if (!empty($optionSubs)) {
            foreach ($optionSubs as $optionSub) {
                $arrOptionSubs[] = array(
                    'id' => $optionSub['optionSubId'],
                    'name' => $optionSub['name'],
                    'img' => $optionSub['img'],
                    'update_date' => $optionSub['update_date'],
                );
            }
        }

        $imgArr = json_decode($paymentOption['img']);
        if (is_object($imgArr)) {
            $img = $imgArr->img;
            $brand_id = $imgArr->brand_id;
        }
        else{
            $img = $paymentOption['img'];
            $brand_id = 0;
        }

        $arrPaymentOption = array(
            'id' => $paymentOption['optionId'],
            'name' => $paymentOption['name'],
            'optionSubs' => $arrOptionSubs,
            'img' => $img,
            'update_date' => $paymentOption['update_date'],
            'brand_id' => $brand_id,
        );

        return $arrPaymentOption;
    }

    public function getTransaction($transactionId) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "paynl_transactions` WHERE id = '" . $this->db->escape($transactionId) . "' LIMIT 1;";
        $result = $this->db->query($sql);

        return $result->row;
    }

    /**
     * Get The statusses of the order.
     * Because the order can have multiple transactions, 
     * We have to check here if the order hasn't already been completed
     */
    public function getStatussesOfOrder($orderId) {
        $sql = "SELECT `status` FROM `" . DB_PREFIX . "paynl_transactions` WHERE orderId = '" . $this->db->escape($orderId) . "';";
        $result = $this->db->query($sql);

        $rows = $result->rows;
        $result = array();
        foreach ($rows as $row) {
            $result[] = $row['status'];
        }
        return $result;
    }

    public function updateTransactionStatus($transactionId, $status) {
        if (!in_array($status, array(self::STATUS_CANCELED, self::STATUS_COMPLETE, self::STATUS_PENDING))) {
            throw new Pay_Exception('Invalid transaction status');
        }
        //safety so processed orders cannot go to canceled
        $transaction = $this->getTransaction($transactionId);

        if (empty($transaction)) {
            throw new Pay_Exception('Transaction not found');
        }

        //Because an order can have multiple transactions, we have to look for the status complete in all transactions for this order.
        $orderStatusses = self::getStatussesOfOrder($transaction['orderId']);

        if (in_array(self::STATUS_COMPLETE, $orderStatusses) && $status != self::STATUS_COMPLETE) {
            throw new Pay_Exception('Order already complete');
        }


        if ($transaction['status'] == $status) {
            //status is not changed
            return true;
        }

        $sql = "UPDATE `" . DB_PREFIX . "paynl_transactions` SET status = '$status' , last_update = UNIX_TIMESTAMP() WHERE id = '" . $this->db->escape($transactionId) . "'";

        return $this->db->query($sql);
    }

    private function getConfig($key, $pm)
    {
        return $this->config->get('payment_' . $pm . '_' . $key);
    }

    public function getMethod($address = false, $orderAmount = false)
    {
        $pm = empty($this->_paymentMethodName) ? null : $this->_paymentMethodName;
        $config_status = $this->getConfig('status', $pm);
        $pmEnabled = !empty($config_status);
        if (!$pmEnabled) {
            return false;
        }

        $paymentOptions = $this->getPaymentOption($this->_paymentOptionId);
        $minOrderAmount = $this->getConfig('total', $pm);
        $maxOrderAmount = $this->getConfig('totalmax', $pm);
        $geozone = (int)$this->getConfig('geo_zone_id', $pm);
        $customerType = $this->getConfig('customer_type', $pm);

        if ($orderAmount >= 0) {
            if (!empty($minOrderAmount) && $orderAmount < $minOrderAmount) {
                return false;
            }
            if (!empty($maxOrderAmount) && $orderAmount > $maxOrderAmount) {
                return false;
            }
        }

        $strQuery = "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . $geozone. "' AND country_id = '" . (int) $address['country_id'] . "' ".
          " AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')";
        $query = $this->db->query($strQuery);

        if (!empty($geozone) && $query->num_rows == 0) {
            return false;
        }

        $company = (isset($address['company'])) ? trim($address['company']) : '';

        if ( ($customerType == 'private' && !empty($company)) ||
             ($customerType == 'business' && empty($company)) ) {
            return false;
        }

        $icon = "";
        if ($this->config->get('payment_paynl_general_display_icon') != '') {
            $iconSize = $this->config->get('payment_paynl_general_display_icon') ;
            $iconStyle = $this->config->get('payment_paynl_general_icon_style') ;
            $icon = "<img class='paynl_icon' src=\"https://static.pay.nl/payment_profiles/$iconSize/$this->_paymentOptionId.png\"> ";

            if($iconStyle == 'newest' && !empty($paymentOptions['brand_id'])) {
                $style = ' style="width:50px; height:50px;"';
                switch($iconSize) {
                    case '20x20': $style = ' style="width:20px; height:20px;"'; break;
                    case '25x25': $style = ' style="width:25px; height:25px;"'; break;
                    case '50x50': $style = ' style="width:50px; height:50px;"'; break;
                    case '75x75': $style = ' style="width:75px; height:75px;"'; break;
                    case '100x100': $style = ' style="width:100px; height:100px;"'; break;
                }
                $icon = "<img " . $style . " class='paynl_icon' src=\"/image/Pay/" . $paymentOptions['brand_id'] . ".png\"> ";
            }
        }

        return array(
          'code' => $pm,
          'title' => $icon . $this->getLabel(),
          'terms' => '',
          'sort_order' =>  $this->getConfig('sort_order', $pm));
    }

    public function getLabel() {
        return $this->config->get('payment_'.$this->_paymentMethodName . '_label');
    }

    public function isAlreadyPaid($orderId) {
        //Because an order can have multiple transactions, we have to look for the status complete in all transactions for this order.
        $orderStatusses = $this->getStatussesOfOrder($orderId);

        if (in_array(self::STATUS_COMPLETE, $orderStatusses)) {
            return true;
        }
        return false;
    }

    public function processTransaction($transactionId) {
        $this->load->model('setting/setting');
        $this->load->model('checkout/order');

        $settings = $this->model_setting_setting->getSetting('payment_'.$this->_paymentMethodName );

        $statusPending = $settings['payment_'.$this->_paymentMethodName . '_pending_status'];
        $statusComplete = $settings['payment_'.$this->_paymentMethodName . '_completed_status'];
        $statusCanceled = $settings['payment_'.$this->_paymentMethodName . '_canceled_status'];

        $transaction = $this->getTransaction($transactionId);
        $apiInfo = new Pay_Api_Info();
        $apiInfo->setApiToken($this->model_setting_setting->getSettingValue('payment_paynl_general_apitoken'));
        $apiInfo->setServiceId($this->model_setting_setting->getSettingValue('payment_paynl_general_serviceid'));

        if (!empty(trim($this->model_setting_setting->getSettingValue('payment_paynl_general_gateway')))){
            $apiInfo->setApiBase(trim($this->model_setting_setting->getSettingValue('payment_paynl_general_gateway')));
        }
        
        $apiInfo->setTransactionId($transactionId);

        $result = $apiInfo->doRequest();

        $state = $result['paymentDetails']['state'];
        $status = self::STATUS_PENDING;
        $orderStatusId = $statusPending;
        if ($state == 100) {
            $status = self::STATUS_COMPLETE;
            $orderStatusId = $statusComplete;
        } else if ($state < 0) {
            $status = self::STATUS_CANCELED;
            $orderStatusId = $statusCanceled;
        }
        if ($this->isAlreadyPaid($transaction['orderId'])) {
            // order is al betaald, 
            return self::STATUS_COMPLETE;
        }

        //status updaten
        $this->updateTransactionStatus($transactionId, $status);

        $message = "PAY. Updated order to $status.";

        //order updaten
        $order_info = $this->model_checkout_order->getOrder($transaction['orderId']);

        if($order_info['payment_code'] != $this->_paymentMethodName &&
           $status == self::STATUS_CANCELED){
            return 'Not cancelling because the last used method is not this method';
        }

        if ($order_info['order_status_id'] != $orderStatusId) {
            //alleen updaten als de status daadwerkelijk veranderd, ivm exchange, de order wordt 2 keer aangepast
            if ($settings['payment_'.$this->_paymentMethodName . '_send_status_updates'] == 1) {
                $send_status_update = true;
            } else {
                $send_status_update = false;
            }
            if($order_info['order_status_id'] == 0 && $status != self::STATUS_COMPLETE && !$send_status_update){
                // hij is nog niet confirmed, we gaan hem alleen opslaan als status complete is
                return $status;
            }
            $this->model_checkout_order->addOrderHistory($order_info['order_id'], $orderStatusId, $message, $send_status_update);
        }

        return $status;
    }

}
