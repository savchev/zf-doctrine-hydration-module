<?php

use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\DateTimeField;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\DefaultRelation;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedCollection;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedField;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedCollection;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\ReferencedField;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedReferenceCollection;
use Phpro\DoctrineHydrationModule\Hydrator\ODM\MongoDB\Strategy\EmbeddedReferenceField;
use Phpro\DoctrineHydrationModule\Service\DoctrineHydratorFactory;

return [
    'service_manager' => [
      'invokables' => [
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\DateTimeField' => DateTimeField::class,
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\DefaultRelation' => DefaultRelation::class,
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedCollection' => EmbeddedCollection::class,
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedField' => EmbeddedField::class,
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection' => ReferencedCollection::class,
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedField' => ReferencedField::class,
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedReferenceCollection' => EmbeddedReferenceCollection::class,
        'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedReferenceField' => EmbeddedReferenceField::class,
      ],

      'shared' => [
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\DateTimeField' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\DefaultRelation' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedCollection' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedField' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedCollection' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\ReferencedField' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedReferenceCollection' => false,
          'DoctrineHydrationModule\Strategy\ODM\MongoDB\EmbeddedReferenceField' => false,
      ],
    ],
    'hydrators' => [
        'abstract_factories' => [
            DoctrineHydratorFactory::class,
        ],
    ],
];
