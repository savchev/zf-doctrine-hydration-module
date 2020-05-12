<?php

namespace PhproTest\DoctrineHydrationModule\Hydrator;

use Interop\Container\ContainerInterface;
use Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator;
use Laminas\Hydrator\ArraySerializableHydrator;

final class CustomBuildHydratorFactory
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        return new ArraySerializableHydrator();
    }
}
