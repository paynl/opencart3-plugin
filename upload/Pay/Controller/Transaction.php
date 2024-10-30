<?php

require_once DIR_SYSTEM . '/../Pay/vendor/autoload.php';

use PayNL\Sdk\Model\Request\OrderCreateRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Product;

class Pay_Controller_Transaction extends Controller
{

    public $openCart;
    public $payConfig;
    public function __construct($openCart) // phpcs:ignore
    {
        $this->openCart = $openCart;
        $this->payConfig = new Pay_Controller_Config($openCart);
    }

    /**
     * @return PayNL\Sdk\Model\Response\OrderCreateResponse
     */
    public function startTransaction($order_info, $paymentOption, $paymentMethodName)
    {       
        $request = new OrderCreateRequest();
        $request->setConfig($this->payConfig->getConfig(true));
        $request->setServiceId($this->payConfig->getServiceId());
        $request->setDescription($order_info['order_id']);
        $request->setReference($order_info['order_id']);
        $request->setCurrency('EUR');
        $request->setPaymentMethodId((int) $paymentOption);
        if (!empty($this->openCart->request->post['optionSubId'])) {
            $request->setIssuerId($this->openCart->request->post['optionSubId']);
        }

        $returnUrl = $this->openCart->url->link('extension/payment/' . $paymentMethodName . '/finish');
        $exchangeUrl = $this->openCart->url->link('extension/payment/' . $paymentMethodName . '/exchange');

        $customExchangeUrl = $this->payConfig->getCustomExchangeURL();
        $customExchangeUrl = is_null($customExchangeUrl) ? '' : trim($customExchangeUrl);

        if (!empty($customExchangeUrl)) {
            $exchangeUrl = htmlspecialchars_decode($customExchangeUrl);
        }

        $request->setReturnurl($returnUrl);
        $request->setExchangeUrl($exchangeUrl);
        $request->setTestmode($this->payConfig->isTestMode());

        $customer = new \PayNL\Sdk\Model\Customer();
        $customer->setFirstName($order_info['firstname'] ?? '');
        $customer->setLastName($order_info['lastname'] ?? '');
        if (!empty($this->openCart->request->post['dob'])) {
            $customer->setBirthDate(preg_replace("([^0-9/])", "", htmlentities($this->openCart->request->post['dob'])));
        }
        $customer->setPhone($order_info['telephone'] ?? '');
        $customer->setEmail($order_info['email'] ?? '');
        $customer->setLanguage(substr($order_info['language_code'], 0, 2));

        $company = new \PayNL\Sdk\Model\Company();
        $company->setName($order_info['payment_company'] ?? '');
        $company->setCoc($this->openCart->request->post['coc'] ?? null);
        $company->setVat($this->openCart->request->post['vat'] ?? null);
        $company->setCountryCode($order_info['payment_iso_code_2']);

        $customer->setCompany($company);
        $request->setCustomer($customer);

        $order = new \PayNL\Sdk\Model\Order();
        $order->setCountryCode('NL');

        $strAddress = $order_info['shipping_address_1'] . ' ' . $order_info['shipping_address_2'];
        list($street, $housenumber) = Pay_Helper::splitAddress($strAddress);
        $devAddress = new \PayNL\Sdk\Model\Address();
        $devAddress->setCode('dev');
        $devAddress->setStreetName($street);
        $devAddress->setStreetNumber($housenumber);
        $devAddress->setZipCode($order_info['shipping_postcode']);
        $devAddress->setCity($order_info['shipping_city']);
        $devAddress->setRegionCode($order_info['shipping_zone_code']);
        $devAddress->setCountryCode($order_info['shipping_iso_code_2']);
        $order->setDeliveryAddress($devAddress);

        $strAddress = $order_info['payment_address_1'] . ' ' . $order_info['payment_address_2'];
        list($street, $housenumber) = Pay_Helper::splitAddress($strAddress);
        $invAddress = new \PayNL\Sdk\Model\Address();
        $invAddress->setCode('inv');
        $invAddress->setStreetName($street);
        $invAddress->setStreetNumber($housenumber);
        $invAddress->setZipCode($order_info['payment_postcode']);
        $invAddress->setCity($order_info['payment_city']);
        $invAddress->setRegionCode($order_info['payment_zone_code']);
        $invAddress->setCountryCode($order_info['payment_iso_code_2']);
        $order->setInvoiceAddress($invAddress);

        $products = new \PayNL\Sdk\Model\Products();
        $currency_value = $order_info['currency_value'];

        foreach ($this->openCart->cart->getProducts() as $productItem) {
            $priceWithTax = $this->openCart->tax->calculate(
                $productItem['price'] * $currency_value,
                $productItem['tax_class_id'],
                $this->openCart->config->get('config_tax')
            );

            $tax = $priceWithTax - ($productItem['price'] * $currency_value);

            $price = round($priceWithTax);

            $product = new Product();
            $product->setId($productItem['product_id']);
            $product->setDescription($productItem['name']);
            $product->setType(Product::TYPE_ARTICLE);
            $product->setAmount($price);
            $product->setCurrency('EUR');
            $product->setQuantity($productItem['quantity']);
            $product->setVatPercentage(($tax / $priceWithTax * 100));
            $products->addProduct($product);
        }

        $taxes = $this->openCart->cart->getTaxes();
        $this->openCart->load->model('setting/extension');
        $results = $this->openCart->model_setting_extension->getExtensions('total');
        $totals = array();
        $total = 0;
        $arrTotals = array(
            'totals' => &$totals,
            'taxes' => &$taxes,
            'total' => &$total
        );
        $taxesForTotals = array();
        foreach ($results as $result) {
            $taxesBefore = array_sum($arrTotals['taxes']);
            if ($this->openCart->config->get('total_' . $result['code'] . '_status')) {
                $this->openCart->load->model('extension/total/' . $result['code']);
                $this->openCart->{'model_extension_total_' . $result['code']}->getTotal($arrTotals);
                $taxAfter = array_sum($arrTotals['taxes']);
                $taxesForTotals[$result['code']] = $taxAfter - $taxesBefore;
            }
        }
        foreach ($arrTotals['totals'] as $total_row) {
            if (!in_array($total_row['code'], array('sub_total', 'tax', 'total'))) {
                if (array_key_exists($total_row['code'], $taxesForTotals)) {
                    $total_row_tax = $taxesForTotals[$total_row['code']];
                } else {
                    $total_row_tax = 0;
                }
                $totalIncl = $total_row['value'] + $total_row_tax;

                $totalIncl = $totalIncl * $currency_value;
                $total_row_tax = $total_row_tax * $currency_value;

                switch ($total_row['code']) {
                    case 'shipping':
                        $type = "SHIPPING";
                        break;
                    default:
                        $type = "ARTICLE";
                        break;
                }

                $product = new Product();
                $product->setId($total_row['code'], );
                $product->setDescription($total_row['title']);
                $product->setType(Product::TYPE_ARTICLE);
                $product->setAmount(round($totalIncl));
                $product->setCurrency('EUR');
                $product->setQuantity(1);
                $product->setVatPercentage(($total_row_tax / $totalIncl * 100));
                $products->addProduct($product);
            }
        }

        $order->setProducts($products);
        $request->setOrder($order);
        $request->setStats((new \PayNL\Sdk\Model\Stats())->setObject($this->payConfig->getObject()));

        $amount = round($order_info['total'] * $currency_value);
        $request->setAmount((float) $amount);

        $transaction = $request->start();
        $modelName = 'model_extension_payment_' . $paymentMethodName;
        $this->openCart->$modelName->addTransaction(
            $transaction->getOrderId(),
            $order_info['order_id'],
            $paymentOption,
            $amount,
            '',
            $this->openCart->request->post['optionSubId'] ?? 0
        );

        return $transaction->getPaymentUrl();
    }
}