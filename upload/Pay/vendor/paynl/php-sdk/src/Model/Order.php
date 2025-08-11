<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use JsonSerializable;
use PayNL\Sdk\Common\JsonSerializeTrait;

/**
 * Class Order
 *
 * @package PayNL\Sdk\Model
 */
class Order implements
    ModelInterface,
    JsonSerializable
{
    use JsonSerializeTrait;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @var string
     */
    protected $deliveryDate;

    /**
     * @var string
     */
    protected $invoiceDate;

    /**
     * @var Address
     */
    protected $deliveryAddress;

    /**
     * @var Address
     */
    protected $invoiceAddress;

    /**
     * @var Products
     */
    protected $products;


    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return (string)$this->countryCode;
    }

    /**
     * @param string $countryCode
     *
     * @return Order
     */
    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeliveryDate(): string
    {
        return (string)$this->deliveryDate;
    }

    /**
     * @param string $deliveryDate
     *
     * @return Order
     */
    public function setDeliveryDate(string $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceDate(): string
    {
        return (string)$this->invoiceDate;
    }

    /**
     * @param string $invoiceDate
     *
     * @return Order
     */
    public function setInvoiceDate(string $invoiceDate): self
    {
        $this->invoiceDate = $invoiceDate;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        if (null === $this->customer) {
            $this->setCustomer(new Customer());
        }
        return $this->customer;
    }

    /**
     * @param Customer $customer
     *
     * @return Order
     */
    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return Address
     */
    public function getDeliveryAddress(): Address
    {
        if (null === $this->deliveryAddress) {
            $this->setDeliveryAddress(new Address());
        }
        return $this->deliveryAddress;
    }

    /**
     * @param Address $deliveryAddress
     *
     * @return Order
     */
    public function setDeliveryAddress(Address $deliveryAddress): self
    {
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    /**
     * Alias for setDeliveryAddress
     * @param Address $shippingAddress
     * @return $this
     */
    public function setShippingAddress(Address $shippingAddress): self
    {
        return $this->setDeliveryAddress($shippingAddress);
    }

    /**
     * @return Address
     */
    public function getInvoiceAddress(): Address
    {
        if (null === $this->invoiceAddress) {
            $this->setInvoiceAddress(new Address());
        }
        return $this->invoiceAddress;
    }

    /**
     * @param Address $invoiceAddress
     *
     * @return Order
     */
    public function setInvoiceAddress(Address $invoiceAddress): self
    {
        $this->invoiceAddress = $invoiceAddress;
        return $this;
    }

    /**
     * @return Products
     */
    public function getProducts(): Products
    {
        if (null === $this->products) {
            $this->setProducts(new Products());
        }
        return $this->products;
    }

    /**
     * @param Products $products
     *
     * @return Order
     */
    public function setProducts(Products $products): self
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @param Product $product
     *
     * @return Order
     */
    public function addProduct(Product $product): self
    {
        $this->getProducts()->addProduct($product);
        return $this;
    }
}
