<?php

declare(strict_types=1);

namespace ChrisHarrison\EntitySets;

use Assert\Assert;
use Funeralzone\ValueObjects\ValueObject;

trait EntityIdTrait
{
    /**
     * @var int
     */
    protected $value;

    /**
     * IntegerTrait constructor.
     * @param int|null $value
     */
    public function __construct(?int $value)
    {
        Assert::that($value)->nullOr()->greaterOrEqualThan(0);
        $this->value = $value;
    }

    /**
     * @return static
     */
    public static function start()
    {
        return new static(null);
    }

    /**
     * @return static
     */
    public function next()
    {
        if ($this->value === null) {
            return new static(0);
        }
        return new static($this->value + 1);
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return ($this->value === null);
    }

    /**
     * @param ValueObject $object
     * @return bool
     */
    public function isSame(ValueObject $object): bool
    {
        return ($this->toNative() === $object->toNative());
    }

    /**
     * @param mixed $native
     * @return static
     */
    public static function fromNative($native)
    {
        return new static($native);
    }

    /**
     * @return int|null
     */
    public function toNative()
    {
        return $this->value;
    }
}
