<?php

declare(strict_types=1);

namespace ChrisHarrison\EntitySets\Exceptions;

final class InvalidInstantiationType extends \InvalidArgumentException
{
    public function __construct(string $actualType, string $expectedType)
    {
        parent::__construct(sprintf(
            "Tried to instantiate an entity set with a %s. Only accepts objects of type %s.",
            $actualType,
            $expectedType
        ));
    }
}
