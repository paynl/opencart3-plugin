<?php

declare(strict_types=1);

namespace PayNL\Sdk\Application;

use Psr\Container\ContainerInterface;
use PayNL\Sdk\{
    Common\FactoryInterface,
    Service\Manager as ServiceManager
};

/**
 * Class ApplicationFactory
 *
 * @package PayNL\Sdk\Factory
 */
class Factory implements FactoryInterface
{
    /**
     * @inheritDoc
     *
     * @return Application
     */
    public function __invoke(ContainerInterface $container, string $requestedName, ?array $options = null): Application
    {
        # Init "default" response
        $response = $container->get('Response');
        $response->setStatusCode(500);

        /** @var ServiceManager $serviceManager */
        $serviceManager = $container;

        return new Application(
            $serviceManager,
            $response
        );
    }
}
