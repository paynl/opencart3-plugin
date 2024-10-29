<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Response\OrderStatusResponse;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class OrderStatusRequest
 * Request the status of a transaction using this method.
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderStatusRequest extends RequestData
{
    private string $orderId;

    /**
     * @param string $orderId
     */
    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
        parent::__construct('orderStatus', '/orders/%transactionId%/status', RequestInterface::METHOD_GET);
    }

    public function getPathParameters(): array
    {
        return [
          'transactionId' => $this->orderId
        ];
    }

    public function getBodyParameters(): array
    {
        return [];
    }

    /**
     * @return OrderStatusResponse
     * @throws PayException
     */
    public function start(): OrderStatusResponse
    {
        return parent::start();
    }
}