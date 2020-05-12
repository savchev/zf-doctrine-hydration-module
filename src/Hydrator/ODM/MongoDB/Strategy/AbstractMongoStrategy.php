<?php

namespace Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy;

use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use Doctrine\Laminas\Hydrator\Strategy\AbstractCollectionStrategy;
use Doctrine\Laminas\Hydrator\Strategy\AllowRemoveByValue;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject;

/**
 * Abstract AbstractMongoStrategy.
 */
abstract class AbstractMongoStrategy extends AbstractCollectionStrategy implements ObjectManagerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Set the object manager.
     *
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get the object manager.
     *
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager Possibly injected by hydrator factory
     */
    public function __construct($objectManager = null)
    {
        if ($objectManager) {
            $this->setObjectManager($objectManager);
        }
    }

    /**
     * @return DoctrineObject
     */
    protected function getDoctrineHydrator()
    {
        return new DoctrineObject($this->getObjectManager());
    }

    /**
     * Use default collection strategy.
     *
     * @param $value
     *
     * @return array|mixed
     */
    protected function hydrateCollection($value)
    {
        $strategy = new AllowRemoveByValue();
        $strategy->setObject($this->object);
        $strategy->setClassMetadata($this->getClassMetadata());
        $strategy->setCollectionName($this->getCollectionName());

        return $strategy->hydrate($value, null);
    }

    /**
     * @param $targetDocument
     * @param $targetId
     *
     * @return object
     */
    protected function findTargetDocument($targetDocument, $targetId)
    {
        $repo = $this->getObjectManager()->getRepository($targetDocument);
        return $repo->find($targetId);
    }
}
