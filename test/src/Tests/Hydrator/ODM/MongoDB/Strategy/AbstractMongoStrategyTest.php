<?php

namespace PhproTest\DoctrineHydrationModule\Tests\Hydrator\ODM\MongoDB\Strategy;

use Doctrine\ODM\MongoDB\Tests\BaseTest;
use Laminas\Hydrator\Strategy\StrategyInterface;
use Doctrine\Laminas\Hydrator\Strategy\AbstractCollectionStrategy;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

/**
 * Class AbstractMongoStrategyTest.
 */
abstract class AbstractMongoStrategyTest extends BaseTest
{
    /**
     * @return StrategyInterface
     */
    abstract protected function createStrategy();

    /**
     * @param $objectManager
     * @param $object
     * @param $fieldName
     *
     * @return StrategyInterface
     */
    protected function getStrategy($objectManager, $object, $fieldName)
    {
        $objectClass = get_class($object);
        $metadata = $objectManager->getClassMetadata($objectClass);

        $strategy = $this->createStrategy();
        $strategy->setObject($object);
        $strategy->setObjectManager($objectManager);
        $strategy->setCollectionName($fieldName);
        $strategy->setClassMetadata($metadata);

        return $strategy;
    }

    /**
     * @test
     */
    public function it_should_be_a_mongodb_strategy()
    {
        $strategy = $this->createStrategy();
        $this->assertInstanceOf('Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\AbstractMongoStrategy', $strategy);
    }

    /**
     * @test
     */
    public function it_should_be_a_collection_strategy()
    {
        $strategy = $this->createStrategy();
        $this->assertInstanceOf(AbstractCollectionStrategy::class, $strategy);
    }

    /**
     * @test
     */
    public function it_should_know_an_object_manager()
    {
        $strategy = $this->createStrategy();
        $this->assertInstanceOf(ObjectManagerAwareInterface::class, $strategy);
    }

    /**
     * @test
     */
    public function it_should_have_an_object_manager()
    {
        $objectManager = $this->dm;
        $strategy = $this->createStrategy();

        $strategy->setObjectManager($objectManager);
        $this->assertEquals($objectManager, $strategy->getObjectManager());
    }
}
