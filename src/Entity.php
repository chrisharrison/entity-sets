<?php

declare(strict_types=1);

namespace ChrisHarrison\EntitySets;

use Funeralzone\ValueObjects\ValueObject;

interface Entity extends ValueObject
{
    /**
     * @return EntityId
     */
    public function getId();
}
