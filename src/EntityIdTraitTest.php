<?php

// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace ChrisHarrison\EntitySets;

use Assert\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EntityIdTraitTest extends TestCase
{
    public function test_start_creates_new_instance_with_value_of_zero()
    {
        $test = _EntityIdTrait::start();
        $this->assertEquals(0, $test->toNative());
    }

    public function test_next_returns_new_instance_with_value_incremented_by_one()
    {
        $test = new _EntityIdTrait(100);
        $this->assertEquals(101, $test->next()->toNative());
    }

    public function test_cannot_instantiate_with_negative()
    {
        $this->expectException(InvalidArgumentException::class);
        new _EntityIdTrait(-1);
    }
}

final class _EntityIdTrait
{
    use EntityIdTrait;
}
