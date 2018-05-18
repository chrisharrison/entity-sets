<?php

declare(strict_types=1);

namespace ChrisHarrison\EntitySets;

use Assert\Assert;
use Funeralzone\ValueObjects\Scalars\IntegerTrait;

trait EntityIdTrait
{
    use IntegerTrait;

    /**
     * @var int
     */
    protected $int;

    /**
     * IntegerTrait constructor.
     * @param int $int
     */
    public function __construct(int $int)
    {
        Assert::that($int)->greaterOrEqualThan(0);
        $this->int = $int;
    }

    /**
     * @return static
     */
    public static function start()
    {
        return new static(0);
    }

    /**
     * @return static
     */
    public function next()
    {
        return new static($this->int + 1);
    }
}
