<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use JsonSerializable;
use PayNL\Sdk\Common\JsonSerializeTrait;

/**
 * Class Address
 *
 * @package PayNL\Sdk\Model
 */
class Address implements ModelInterface, JsonSerializable
{
    use JsonSerializeTrait;


    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $streetName;

    /**
     * @var string
     */
    protected $streetNumber;

    /**
     * @var string
     */
    protected $streetNumberExtension = '';

    /**
     * @var string
     */
    protected $zipCode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $regionCode;

    /**
     * @var string
     */
    protected $countryCode;

    /**
     * @return string
     */
    public function getStreetName(): string
    {
        return (string)$this->streetName;
    }

    /**
     * @param string $streetName
     *
     * @return Address
     */
    public function setStreetName(string $streetName): self
    {
        $this->streetName = $streetName;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetNumber(): string
    {
        return (string)$this->streetNumber;
    }

    /**
     * @param string|int $streetNumber
     *
     * @return Address
     */
    public function setStreetNumber($streetNumber): self
    {
        $this->streetNumber = (string)$streetNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetNumberExtension(): string
    {
        return $this->streetNumberExtension;
    }

    /**
     * @param string $streetNumberExtension
     *
     * @return Address
     */
    public function setStreetNumberExtension(string $streetNumberExtension): self
    {
        $this->streetNumberExtension = $streetNumberExtension;
        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return (string)$this->zipCode;
    }

    /**
     * @param string $zipCode
     *
     * @return Address
     */
    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return (string)$this->city;
    }

    /**
     * @param string $city
     *
     * @return Address
     */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegionCode(): string
    {
        return (string)$this->regionCode;
    }

    /**
     * @param string $regionCode
     *
     * @return Address
     */
    public function setRegionCode(string $regionCode): self
    {
        $this->regionCode = $regionCode;
        return $this;
    }

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
     * @return Address
     */
    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return (string)$this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
