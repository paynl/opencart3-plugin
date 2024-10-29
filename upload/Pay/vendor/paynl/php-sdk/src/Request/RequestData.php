<?php

declare(strict_types=1);

namespace PayNL\Sdk\Request;

use Exception;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Application\Application;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Util\Text;

/**
 * Class RequestData
 *
 * @package PayNL\Sdk\Request
 */
abstract class RequestData implements RequestDataInterface
{
    protected string $mapperName = '';
    protected string $uri = '';
    protected string $methodType = 'GET';
    protected ?Config $config;

    /**
     * @param string $mapperName Internal name of the call to make
     * @param string $uri Path for API
     * @param $requestMethod Should be for example RequestInterface::METHOD_POST, RequestInterface::METHOD_GET, etc.
     */
    public function __construct(string $mapperName, string $uri, $requestMethod = 'POST')
    {
        $this->mapperName = $mapperName;
        $this->methodType = $requestMethod;
        $this->uri = $uri;
        $this->config = new Config();
    }

    /**
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return mixed
     * @throws PayException
     * @throws Exception
     */
    public function start()
    {
        $config = (new Config(require __DIR__ . '/../../config/config.global.php'));
        if (!empty($this->config)) {
            $config->merge($this->config);
        }

        try {
            $response = (Application::init($config))->request($this)->run();
        } catch (\Exception $e) {
            throw (new PayException('Could not initiate API call:' . $e->getMessage(), 0, 0))
                ->setFriendlyMessage(text::getFriendlyMessage($e->getMessage()));
        }

        if ($response->hasErrors()) {
            $jsonData = json_decode($response->getRawBody(), true);
            $jsonError = json_last_error();
            if (empty($jsonError) && !empty($jsonData)) {
                $code = $jsonData['violations'][0]['code'] ?? 'PAY-';
                $detail = $jsonData['detail'] ?? '';
                $errorMessage = empty($detail) ? ($jsonData['title'] ?? '') : $detail;
                throw (new PayException($code . ' - ' . $detail, (int)substr($code, 4), $response->getStatusCode()))->setFriendlyMessage(text::getFriendlyMessage($errorMessage));
            } else {
                throw (new PayException($response->getErrors(), 0, $response->getStatusCode()))->setFriendlyMessage(text::getFriendlyMessage($response->getErrors()));
            }
        } else {
            $responseBody = $response->getBody();
            if (gettype($responseBody) != 'object') {
                throw new PayException('Unexpected result, could not transform.', 0, $response->getStatusCode());
            }
            return $responseBody;
        }
    }


    /*
     * For defining the arguments used in the requestpath
     */
    abstract public function getPathParameters(): array;

    /*
     * For defining the arguments used in the body of the request
     */
    abstract public function getBodyParameters(): array;

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->methodType;
    }

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->mapperName;
    }
}