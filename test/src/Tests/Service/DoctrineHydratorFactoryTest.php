<?php

namespace PhproTest\DoctrineHydrationModule\Tests\Service;

use Phpro\DoctrineHydrationModule\Hydrator\DoctrineHydrator;
use PhproTest\DoctrineHydrationModule\Hydrator\CustomBuildHydratorFactory;
use Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Hydrator\HydratorPluginManager;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;
use Laminas\Hydrator\Filter\FilterInterface;
use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Laminas\Hydrator\DoctrineObject;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\DoctrineObject as MongoDoctrineObject;
use Doctrine\ODM\MongoDB\Hydrator\HydratorFactory;
use Doctrine\ODM\MongoDB\Hydrator\HydratorInterface;
use Laminas\Hydrator\ArraySerializableHydrator;

class DoctrineHydratorFactoryTest extends TestCase
{
    /**
     * @var array
     */
    protected $serviceConfig;

    /**
     * @var HydratorPluginManager
     */
    protected $hydratorManager;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Setup the service manager.
     */
    protected function setUp(): void
    {
        $this->serviceConfig = include TEST_BASE_PATH.'/config/module.config.php';

        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('config', $this->serviceConfig);
        $this->serviceManager->setService(
            'custom.strategy',
            $this->getMockBuilder(StrategyInterface::class)->getMock()
        );
        $this->serviceManager->setService(
            'custom.filter',
            $this->getMockBuilder(FilterInterface::class)->getMock()
        );
        $this->serviceManager->setService(
            'custom.naming_strategy',
            $this->getMockBuilder(NamingStrategyInterface::class)->getMock()
        );

        $this->hydratorManager = $this->getMockBuilder(HydratorPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->hydratorManager
            ->method('getServiceLocator')
            ->willReturn($this->serviceManager);
    }

    /**
     * @param $objectManagerClass
     *
     * @return MockObject
     */
    protected function stubObjectManager($objectManagerClass): MockObject
    {
        $objectManager = $this->getMockBuilder($objectManagerClass)
            ->disableOriginalConstructor()
            ->getMock();
        $this->serviceManager->setService('doctrine.default.object-manager', $objectManager);

        return $objectManager;
    }

    /**
     * @return DoctrineHydrator
     */
    protected function createOrmHydrator(): DoctrineHydrator
    {
        $this->stubObjectManager(EntityManager::class);

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        return $hydrator;
    }

    /**
     * @return DoctrineHydrator
     */
    protected function createOdmHydrator(): DoctrineHydrator
    {
        $this->stubObjectManager('Doctrine\ODM\MongoDb\DocumentManager');

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        return $hydrator;
    }

    /**
     * @test
     */
    public function it_should_be_initializable(): void
    {
        $factory = new DoctrineHydratorFactory();
        $this->assertInstanceOf(DoctrineHydratorFactory::class, $factory);
    }

    /**
     * @test
     */
    public function it_should_be_an_abstract_factory(): void
    {
        $factory = new DoctrineHydratorFactory();
        $this->assertInstanceOf(AbstractFactoryInterface::class, $factory);
    }

    /**
     * @test
     */
    public function it_should_know_which_services_it_can_create()
    {
        // $this->stubObjectManager('Doctrine\Common\Persistence\ObjectManager');
        $factory = new DoctrineHydratorFactory();

        $result = $factory->canCreateServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');
        $this->assertTrue($result);

        $result = $factory->canCreateServiceWithName($this->hydratorManager, 'invalidhydrator', 'invalid-hydrator');
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function it_should_create_a_custom_ORM_hydrator(): void
    {
        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf(DoctrineObject::class, $hydrator->getExtractService());
        $this->assertInstanceOf(DoctrineObject::class, $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function it_should_create_a_custom_ODM_hydrator()
    {
        $hydrator = $this->createOdmHydrator();

        $this->assertInstanceOf(MongoDoctrineObject::class, $hydrator->getExtractService());
        $this->assertInstanceOf(MongoDoctrineObject::class, $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function it_should_create_a_custom_ODM_hydrator_which_uses_the_auto_generated_hydrators(): void
    {
        $this->serviceConfig['doctrine-hydrator']['custom-hydrator']['use_generated_hydrator'] = true;
        $this->serviceManager->setService('config', $this->serviceConfig);
        $objectManager = $this->stubObjectManager('Doctrine\ODM\MongoDb\DocumentManager');

        $hydratorFactory = $this->getMockBuilder(HydratorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $generatedHydrator = $this->getMockBuilder(HydratorInterface::class)->getMock();

        $objectManager
            ->method('getHydratorFactory')
            ->willReturn($hydratorFactory);

        $hydratorFactory
            ->method('getHydratorFor')
            ->with('App\Entity\EntityClass')
            ->willReturn($generatedHydrator);

        $factory = new DoctrineHydratorFactory();
        $hydrator = $factory->createServiceWithName($this->hydratorManager, 'customhydrator', 'custom-hydrator');

        $this->assertInstanceOf(DoctrineHydrator::class, $hydrator);
        $this->assertInstanceOf(MongoDoctrineObject::class, $hydrator->getExtractService());
        $this->assertEquals($generatedHydrator, $hydrator->getHydrateService());
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_a_custom_hydrator(): void
    {
        $this->serviceConfig['doctrine-hydrator']['custom-hydrator']['hydrator'] = 'custom.hydrator';
        $this->serviceManager->setService('config', $this->serviceConfig);

        $this->serviceManager->setService(
            'custom.hydrator',
            $this->getMockBuilder(ArraySerializableHydrator::class)->getMock()
        );

        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf(ArraySerializableHydrator::class, $hydrator->getHydrateService());
        $this->assertInstanceOf(ArraySerializableHydrator::class, $hydrator->getExtractService());
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_a_custom_hydrator_as_factory(): void
    {
        $this->serviceConfig['doctrine-hydrator']['custom-hydrator']['hydrator'] = 'custom.build.hydrator';
        $this->serviceManager->setService('config', $this->serviceConfig);

        $this->serviceManager->setFactory(
            'custom.build.hydrator',
            new CustomBuildHydratorFactory()
        );

        $hydrator = $this->createOrmHydrator();

        $this->assertInstanceOf(ArraySerializableHydrator::class, $hydrator->getHydrateService());
        $this->assertInstanceOf(ArraySerializableHydrator::class, $hydrator->getExtractService());
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_hydration_stategies(): void
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasStrategy('fieldname'));
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_a_naming_stategy(): void
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasNamingStrategy());
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_configure_hydration_filters(): void
    {
        $hydrator = $this->createOrmHydrator();
        $realHydrator = $hydrator->getExtractService();

        $this->assertTrue($realHydrator->hasFilter('custom.filter.name'));
    }
}
