<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\InArrayValidator;
use PHPUnit\Framework\TestCase;

class InArrayValidatorTest extends TestCase
{
    public function testValidValueInArray()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InArrayValidator(allowedValues: ['apple', 'banana', 'orange'])]
            public string $field = 'banana';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidValueNotInArray()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InArrayValidator(allowedValues: ['apple', 'banana', 'orange'])]
            public string $field = 'grape';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um dos seguintes valores: apple, banana, orange.', $validator->getError());
    }

    public function testValidNullableField()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InArrayValidator(allowedValues: ['apple', 'banana', 'orange'], nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullableFieldWithValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InArrayValidator(allowedValues: ['apple', 'banana', 'orange'], nullable: true)]
            public ?string $field = 'grape';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um dos seguintes valores: apple, banana, orange.', $validator->getError());
    }

    public function testValidIntegerInArray()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InArrayValidator(allowedValues: [1, 2, 3])]
            public int $field = 2;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidIntegerNotInArray()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InArrayValidator(allowedValues: [1, 2, 3])]
            public int $field = 4;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um dos seguintes valores: 1, 2, 3.', $validator->getError());
    }
}
