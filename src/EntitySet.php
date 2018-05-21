<?php

declare(strict_types=1);

namespace ChrisHarrison\EntitySets;

use Funeralzone\ValueObjects\ValueObject;

interface EntitySet extends ValueObject
{
    /**
     * @param EntityId $id
     * @return Entity|null
     */
    public function getById(EntityId $id);

    /**
     * @param $entity
     * @return static
     */
    public function addNative(array $entity);

    /**
     * @param $entity
     * @return static
     */
    public function update(Entity $entity);

    /**
     * @param $entity
     * @return static
     */
    public function remove(Entity $entity);

    /**
     * @return array
     */
    public function set(): array;

    /**
     * @return EntityId
     */
    public function lastId();
}
