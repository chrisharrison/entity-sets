<?php

// @codingStandardsIgnoreFile

declare(strict_types=1);

namespace ChrisHarrison\EntitySets;

use ChrisHarrison\EntitySets\Exceptions\InvalidInstantiationType;
use Funeralzone\ValueObjects\CompositeTrait;
use Funeralzone\ValueObjects\Scalars\StringTrait;
use Funeralzone\ValueObjects\ValueObject;
use PHPUnit\Framework\TestCase;

final class AbstractEntitySetTest extends TestCase
{
    public function test_when_constructing_with_null_lastId_a_new_sequence_of_entity_id_is_used()
    {
        $set = new _AbstractEntitySet;
        $this->assertEquals(0, $set->lastId()->toNative());
    }

    public function test_when_constructing_the_set_items_are_instances_of_the_entity_type()
    {
        $this->expectException(InvalidInstantiationType::class);
        new _AbstractEntitySet([
            'test',
            new _Value('value'),
        ]);
    }

    public function test_getById_returns_an_entity_with_the_id_if_it_exists()
    {
        $set = new _AbstractEntitySet([
            new _Entity(new _EntityId(0), new _Value('value1')),
            new _Entity(new _EntityId(1), new _Value('value2')),
            new _Entity(new _EntityId(2), new _Value('value3')),
        ]);

        $get = $set->getById(new _EntityId(1));

        $this->assertEquals('value2', $get->getValue()->toNative());
    }

    public function test_getById_returns_null_when_no_entities_have_the_id()
    {
        $set = new _AbstractEntitySet([
            new _Entity(new _EntityId(0), new _Value('value1')),
            new _Entity(new _EntityId(1), new _Value('value2')),
            new _Entity(new _EntityId(2), new _Value('value3')),
        ]);

        $get = $set->getById(new _EntityId(3));

        $this->assertNull($get);
    }

    public function test_adds_an_entity_made_from_the_native_value_and_the_id_is_the_next_in_the_sequence()
    {
        $set = new _AbstractEntitySet([
            new _Entity(new _EntityId(0), new _Value('value1')),
        ], new _EntityId(0));

        $set = $set->addNative([
            'value' => 'value1',
        ]);

        $this->assertEquals(1, $set->toNative()['set'][1]['id']);
    }

    public function test_updates_the_value_of_entity_in_the_set_which_matches_the_id()
    {
        $set = new _AbstractEntitySet([
            new _Entity(new _EntityId(0), new _Value('value1')),
            new _Entity(new _EntityId(1), new _Value('value2')),
            new _Entity(new _EntityId(2), new _Value('value3')),
        ]);

        $set = $set->update(new _Entity(new _EntityId(1), new _Value('UPDATED')));

        $this->assertCount(3, $set->set());
        $this->assertEquals('UPDATED', $set->toNative()['set'][1]['value']);
    }

    public function test_removes_the_entity_in_the_set_which_matches_the_id()
    {
        $set = new _AbstractEntitySet([
            new _Entity(new _EntityId(0), new _Value('value1')),
            new _Entity(new _EntityId(1), new _Value('value2')),
            new _Entity(new _EntityId(2), new _Value('value3')),
        ]);

        $set = $set->remove(new _Entity(new _EntityId(1), new _Value('UPDATED')));

        $this->assertCount(2, $set->set());

        $this->assertEquals(0, $set->toNative()['set'][0]['id']);
        $this->assertEquals(2, $set->toNative()['set'][1]['id']);
    }

    public function test_fromNative_returns_instance_with_set_and_lastId()
    {
        $expected = [
            'set' => [
                [
                    'id' => 0,
                    'value' => 'value1',
                ],
                [
                    'id' => 1,
                    'value' => 'value2',
                ],
                [
                    'id' => 2,
                    'value' => 'value3',
                ],
            ],
            'lastId' => 3,
        ];

        $set = new _AbstractEntitySet([
            new _Entity(new _EntityId(0), new _Value('value1')),
            new _Entity(new _EntityId(1), new _Value('value2')),
            new _Entity(new _EntityId(2), new _Value('value3')),
        ], new _EntityId(3));

        $this->assertEquals($expected, $set->toNative());
    }

    public function test_set_is_returned()
    {
        $expected = [
            [
                'id' => 0,
                'value' => 'value1',
            ],
            [
                'id' => 1,
                'value' => 'value2',
            ],
            [
                'id' => 2,
                'value' => 'value3',
            ],
        ];
        $set = new _AbstractEntitySet([
            new _Entity(new _EntityId(0), new _Value('value1')),
            new _Entity(new _EntityId(1), new _Value('value2')),
            new _Entity(new _EntityId(2), new _Value('value3')),
        ]);

        $this->assertEquals($expected, $set->toNative()['set']);
    }

    public function test_lastId_is_returned()
    {
        $set = new _AbstractEntitySet([], new _EntityId(0));

        $this->assertEquals(0, $set->lastId()->toNative());
    }

    public function test_id_sequence_is_retained_even_after_deletes()
    {
        $set = new _AbstractEntitySet;
        $set = $set->addNative([
            'value' => 'value1',
        ]);
        $set = $set->addNative([
            'value' => 'value2',
        ]);
        $set = $set->addNative([
            'value' => 'value3',
        ]);

        $set = $set->remove(new _Entity(new _EntityId(0), new _Value('')));
        $set = $set->remove(new _Entity(new _EntityId(1), new _Value('')));
        $set = $set->remove(new _Entity(new _EntityId(2), new _Value('')));

        $set = $set->addNative([
            'value' => 'value4',
        ]);
        $set = $set->addNative([
            'value' => 'value5',
        ]);

        $expected = [
            [
                'id' => 3,
                'value' => 'value4',
            ],
            [
                'id' => 4,
                'value' => 'value5',
            ],
        ];

        $this->assertEquals($expected, $set->toNative()['set']);
    }

    public function test_returns_set()
    {
        $set = new _AbstractEntitySet([
            new _Entity(new _EntityId(0), new _Value('value1')),
            new _Entity(new _EntityId(1), new _Value('value2')),
            new _Entity(new _EntityId(2), new _Value('value3')),
        ]);

        $this->assertCount(3, $set->set());
    }

    public function test_returns_lastId()
    {
        $set = new _AbstractEntitySet([], new _EntityId(100));
        $this->assertEquals(100, $set->lastId()->toNative());
    }
}

/**
 * @method _Entity getById(EntityId $id)
 */
final class _AbstractEntitySet extends AbstractEntitySet implements EntitySet
{
    protected static function entityType(): string
    {
        return _Entity::class;
    }

    protected static function entityIdType(): string
    {
        return _EntityId::class;
    }
}

final class _Entity implements Entity
{
    use CompositeTrait;

    private $id;
    private $value;

    public function __construct(_EntityId $id, _Value $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function fromNative($native)
    {
        return new static(
            _EntityId::fromNative($native['id']),
            _Value::fromNative($native['value'])
        );
    }
}

final class _EntityId implements EntityId
{
    use EntityIdTrait;
}

final class _Value implements ValueObject
{
    use StringTrait;
}
