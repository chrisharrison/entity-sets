# entity-sets

[![Build Status](https://travis-ci.org/chrisharrison/entity-sets.svg?branch=master)](https://travis-ci.org/chrisharrison/entity-sets)

Handle non-persisted sets of domain entities.

## Requirements ##

Requires PHP 7.1

## Installation ##

Through Composer, obviously:

```
composer require chrisharrison/entity-sets
```

## Why? ##

In domain driven design (DDD), persistence is abstracted from the domain model. I've found that if you're persisting at the level of the aggregate root, it may not make sense to separately persist certain sets of entities. This library models a set of entities without having to worry about a persistence layer.

## Usage ##

### Implementing an entity ###

Implement the provided `Entity` interface. The interface is the `ValueObject` interface (see [value objects library](https://github.com/funeralzone/valueobjects)) with the addition of a `getId()` method. This method should return an `EntityId`.

```php
final class Note implements Entity
{
    use CompositeTrait;

    private $id;
    private $content;

    public function __construct(NoteId $id, Content $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public function getId(): NoteId
    {
        return $this->id;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public static function fromNative($native)
    {
        return new static(
            NoteId::fromNative($native['id']),
            Content::fromNative($native['content'])
        );
    }
}
```

### Implementing an entity ID ###

An `EntityId` is also a value object with two additional methods:

```php
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
```

The `start()` method should return a new ID at the start of a sequence.

The `next()` method should return a new instance of the ID with the sequence advanced by one.

There is a provided trait: `EntityIdTrait` that implements a simple numeric integer sequence.

```php
final class NoteId implements EntityId
{
    use EntityIdTrait;
}
```

### Implementing a set ###

A set of entities should implement the `EntitySet` interface. There is a provided abstract class that can be used to easily implement this interface: `AbstractEntitySet`.

```php
final class NoteSet extends AbstractEntitySet implements EntitySet
{
    protected static function entityType(): string
    {
        return Note::class;
    }

    protected static function entityIdType(): string
    {
        return NoteId::class;
    }
}
```

### Using a set ###

#### Adding entities ####

Because the set determines an entity's identity (ID) it is not possible to directly add entity objects to a set. Instead, a native representation of the entity should be added using the `addNative()` method.

```php
$nativeNote = [
    'content' => 'This is a note.',
];

$noteSet = new NoteSet;
$noteSet = $noteSet->addNative($nativeNote);
```

The method will return a new instance of the set with the entity added. The native value will be parsed through the `fromNative` method of the provided `Entity` class and the `id` property will be populated using the `EntityId` class. 

#### Updating entities ####

To update an entity, simply pass an entity to the `update()` method. This will return a new instance of the set with the entity with the corresponding `EntityId` replaced.

#### Removing entities ####

To remove an entity, simple pass an entity to the `remove()` method. This will return a new instance of the set with the entity with the corresponding `EntityId` removed.

#### Retrieving entities ####

All entities can be retrieved from the set with `set()`. Or a single entity with `getById()`.