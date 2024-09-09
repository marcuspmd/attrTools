<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\CountValidator;
use PHPUnit\Framework\TestCase;

class CountClass
{
    #[CountValidator(min: 1, max: 3)]
    public ?array $items = [];
}

class CountNullableClass
{
    #[CountValidator(min: 1, nullable: true)]
    public ?array $items = null;
}

class CountValidatorTest extends TestCase
{
    public function testAttrClassAllowNullable()
    {
        $validator = new AttrValidator();
        $class = new CountNullableClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullable()
    {
        $validator = new AttrValidator();
        $class = new CountClass();
        $class->items = [1, 2];

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullableError()
    {
        $validator = new AttrValidator();
        $class = new CountClass();
        $class->items = null;

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: items deve ser um array ou coleção.', $validator->getError());
    }

    public function testAttrClassMinLimitError()
    {
        $validator = new AttrValidator();
        $class = new CountClass();
        $class->items = [];

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: items deve ter no mínimo 1 objeto(s).', $validator->getError());
    }

    public function testAttrClassMaxLimitError()
    {
        $validator = new AttrValidator();
        $class = new CountClass();
        $class->items = [1, 2, 3, 4];

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: items deve ter no máximo 3 objeto(s).', $validator->getError());
    }

    public function testAttrClassEmptyArrayError()
    {
        $validator = new AttrValidator();
        $class = new CountClass();
        $class->items = [];

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: items deve ter no mínimo 1 objeto(s).', $validator->getError());
    }

    public function testValidCountWithinLimits()
    {
        $validator = new CountValidator(min: 1, max: 3);
        $this->assertTrue($validator->isValid([1, 2]));
    }

    public function testInvalidMinCount()
    {
        $validator = new CountValidator(min: 2);
        $validator->field = 'items';
        $this->assertFalse($validator->isValid([1]));
        $this->assertEquals('Campo: items deve ter no mínimo 2 objeto(s).', $validator->getError());
    }

    public function testInvalidMaxCount()
    {
        $validator = new CountValidator(max: 3);
        $validator->field = 'items';
        $this->assertFalse($validator->isValid([1, 2, 3, 4]));
        $this->assertEquals('Campo: items deve ter no máximo 3 objeto(s).', $validator->getError());
    }

    public function testNullableArray()
    {
        $validator = new CountValidator(null, null, true);
        $this->assertTrue($validator->isValid(null));
    }

    public function testInvalidNonArrayType()
    {
        $validator = new CountValidator();
        $validator->field = 'items';
        $this->assertFalse($validator->isValid('string'));
        $this->assertEquals('Campo: items deve ser um array ou coleção.', $validator->getError());
    }

    public function testFieldCannotBeEmpty()
    {
        $validator = new CountValidator();
        $validator->field = 'items';
        $this->assertFalse($validator->isValid([]));
        $this->assertEquals('Campo: items não pode estar vazio.', $validator->getError());
    }
}
