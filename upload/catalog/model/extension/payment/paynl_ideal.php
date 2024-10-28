<?php
$dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$autoload = $dir . '/Pay/Autoload.php';

require_once $autoload;

class ModelExtensionPaymentPaynlIdeal extends Pay_Model
{
    protected $_paymentOptionId = 10;
    protected $_paymentMethodName = 'paynl_ideal';

    public function updateOrderAfterWebhook($order_id, $payment_data, $shipping_data, $customer_data) {
        $order_query = $this->db->query("SELECT customer_id FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");

        if ($order_query->num_rows) {
            $query = "UPDATE `" . DB_PREFIX . "order` SET ";
            $fields = array();

            $fields[] = "payment_firstname = '" . $this->db->escape($payment_data['firstname']) . "'";
            $fields[] = "payment_lastname = '" . $this->db->escape($payment_data['lastname']) . "'";
            $fields[] = "payment_address_1 = '" . $this->db->escape($payment_data['address_1']) . "'";
            $fields[] = "payment_city = '" . $this->db->escape($payment_data['city']) . "'";
            $fields[] = "payment_postcode = '" . $this->db->escape($payment_data['postcode']) . "'";
            $fields[] = "payment_country = '" . $this->db->escape($payment_data['country']) . "'";
            $fields[] = "payment_method = '" . $this->db->escape($payment_data['method']) . "'";

            $fields[] = "shipping_firstname = '" . $this->db->escape($shipping_data['firstname']) . "'";
            $fields[] = "shipping_lastname = '" . $this->db->escape($shipping_data['lastname']) . "'";
            $fields[] = "shipping_address_1 = '" . $this->db->escape($shipping_data['address_1']) . "'";
            $fields[] = "shipping_city = '" . $this->db->escape($shipping_data['city']) . "'";
            $fields[] = "shipping_postcode = '" . $this->db->escape($shipping_data['postcode']) . "'";
            $fields[] = "shipping_country = '" . $this->db->escape($shipping_data['country']) . "'";

            if ($order_query->row['customer_id'] == 0) {
                $fields[] = "firstname = '" . $this->db->escape($customer_data['firstname']) . "'";
                $fields[] = "lastname = '" . $this->db->escape($customer_data['lastname']) . "'";
                $fields[] = "email = '" . $this->db->escape($customer_data['email']) . "'";
                $fields[] = "telephone = '" . $this->db->escape($customer_data['phone']) . "'";
            }

            $query .= implode(", ", $fields);
            $query .= " WHERE order_id = '" . (int)$order_id . "'";

            $this->db->query($query);

            echo "Order information updated successfully.";
        } else {
            echo "Order not found.";
        }
    }
}
