<?php

declare(strict_types=1);

namespace PayNL\Sdk\Api;

use PayNL\Sdk\Config\ProviderInterface as ConfigProviderInterface;

/**
 * Class ConfigProvider
 *
 * @package PayNL\Sdk\Api
 */
class ConfigProvider implements ConfigProviderInterface
{
    const TGU1 = 'https://connect.pay.nl';
    const TGU2 = 'https://connect.payments.nl';
    const TGU3 = 'https://connect.achterelkebetaling.nl';

    /**
     * @inheritDoc
     */
    public function __invoke(): array
    {
        return [
            'service_manager' => $this->getDependencyConfig(),
            'api' => [
                # Defaults
                'url'     => self::TGU1,
                'version' => 1,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDependencyConfig(): array
    {
        return [
            'aliases' => [
                'Api'        => Api::class,
                'api'        => Api::class,
                'ApiService' => Service::class,
                'apiService' => Service::class,
            ],
            'factories' => [
                Api::class     => Factory::class,
                Service::class => Factory::class,
            ],
        ];
    }
}
