<?php

declare(strict_types=1);

namespace ChrisHarrison\EntitySets;

use Funeralzone\ValueObjects\ValueObject;

interface EntityId extends ValueObject
{
    /**
     * @return static
     */
    public static function start();

    /**
     * @return static
     */
    public function next();
}
