<?php

declare(strict_types=1);

namespace ChrisHarrison\EntitySets;

use function array_filter;
use Funeralzone\ValueObjects\CompositeTrait;

abstract class AbstractEntitySet implements EntitySet
{
    use CompositeTrait;

    /**
     * @var array
     */
    private $set;

    /**
     * @var EntityId
     */
    private $lastId;

    /**
     * @return string
     */
    abstract protected static function entityType(): string;

    /**
     * @return string
     */
    abstract protected static function entityIdType(): string;

    /**
     * AbstractEntitySet constructor.
     * @param array $set
     * @param EntityId|null $lastId
     */
    public function __construct(array $set = [], EntityId $lastId = null)
    {
        if ($lastId === null) {
            $lastId = call_user_func(static::entityIdType() .'::start');
        }

        // TODO: Enforce types

        $this->set = $set;
        $this->lastId = $lastId;
    }

    /**
     * @param EntityId $id
     * @return Entity|null
     */
    public function getById(EntityId $id)
    {
        foreach ($this->set as $entity) {
            /* @var Entity $entity */
            $entityId = $entity->getId();

            if ($entityId->isSame($id)) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * @param array $entity
     * @return static
     */
    public function addNativeEntity(array $entity)
    {
        $nextId = $this->lastId->next();
        $entityObject = call_user_func(static::entityType() .'::fromNative', array_merge($entity, [
            'id' => $nextId->toNative(),
        ]));

        $set = $this->set;
        $set[] = $entityObject;

        return new static($set, $nextId);
    }

    /**
     * @param Entity $entity
     * @return static
     */
    public function updateEntity(Entity $entity)
    {
        $set = array_map(function (Entity $i) use ($entity) {

            if (!$i->getId()->isSame($entity->getId())) {
                return $i;
            }

            return $entity;

        }, $this->set);

        return new static($set, $this->lastId);
    }

    /**
     * @param Entity $entity
     * @return static
     */
    public function remove(Entity $entity)
    {
        $set = array_filter($this->set, function (Entity $i) use ($entity) {
            return !$i->getId()->isSame($entity->getId());
        });

        return new static($set, $this->lastId);
    }

    /**
     * @param mixed $native
     * @return static
     */
    public static function fromNative($native)
    {
        return new static($native['set'], $native['lastId']);
    }
}
