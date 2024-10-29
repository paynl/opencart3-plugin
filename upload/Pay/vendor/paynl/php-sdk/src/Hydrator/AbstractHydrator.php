<?php

declare(strict_types=1);

namespace PayNL\Sdk\Hydrator;

use DateTime as stdDateTime;
use PayNL\Sdk\{
    Common\DateTime,
    Common\DebugAwareInterface,
    Common\DebugAwareTrait,
    Hydrator\Manager as HydratorManager,
    Model\Manager as ModelManager,
    Validator\ValidatorManagerAwareInterface,
    Validator\ValidatorManagerAwareTrait
};
use Exception;
use PayNL\Sdk\Packages\Laminas\Hydrator\ClassMethodsHydrator;

/**
 * Class AbstractHydrator
 *
 * @package PayNL\Sdk\Hydrator
 */
abstract class AbstractHydrator extends ClassMethodsHydrator implements DebugAwareInterface, ValidatorManagerAwareInterface
{
    use DebugAwareTrait;
    use ValidatorManagerAwareTrait;

    /**
     * @var HydratorManager
     */
    protected $hydratorManager;

    /**
     * @var ModelManager
     */
    protected $modelManager;

    /**
     * AbstractHydrator constructor.
     *
     * @param HydratorManager $hydratorManager
     * @param ModelManager $modelManager
     */
    public function __construct(HydratorManager $hydratorManager, ModelManager $modelManager)
    {
        $this->hydratorManager = $hydratorManager;
        $this->modelManager = $modelManager;

        // override the given params
        parent::__construct(false, true);
    }

    /**
     * @inheritDoc
     *
     * @internal also automatically sets links and filters to remove all null values
     */
    public function hydrate(array $data, $object)
    {
        $data = array_filter($data, static function ($item) {
            return null !== $item;
        });

        return parent::hydrate($data, $object);
    }

    /**
     * @param string|stdDateTime $dateTime
     *
     * @throws Exception
     * @return DateTime|null
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getSdkDateTime($dateTime): ?DateTime
    {
        if ($dateTime instanceof DateTime) {
            return $dateTime;
        }

        if ($dateTime instanceof stdDateTime) {
            $dateTime = $dateTime->format(stdDateTime::ATOM);
        }

        if (true === is_string($dateTime) && '' === $dateTime) {
            return null;
        }

        return DateTime::createFromFormat(DateTime::ATOM, $dateTime) ?: null;
    }
}
