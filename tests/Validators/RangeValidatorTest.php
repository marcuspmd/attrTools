<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\RangeValidator;
use PHPUnit\Framework\TestCase;

class RangeValidatorTest extends TestCase
{
    public function testValidValueWithinRange()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RangeValidator(min: 10, max: 100)]
            public float $field = 50.5;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidValueBelowMinimum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RangeValidator(min: 10, max: 100)]
            public float $field = 5.5;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser maior ou igual a 10.', $validator->getError());
    }

    public function testInvalidValueAboveMaximum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RangeValidator(min: 10, max: 100)]
            public float $field = 150.75;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor ou igual a 100.', $validator->getError());
    }

    public function testInvalidNonNumericValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RangeValidator(min: 10, max: 100)]
            public string $field = 'not_a_number';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um número.', $validator->getError());
    }

    public function testMissingMinMaxParameters()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RangeValidator()]
            public float $field = 50.5;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('O parametro min e max são obrigatórios.', $validator->getError());
    }

    public function testValidNullableField()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RangeValidator(min: 10, max: 100, nullable: true)]
            public ?float $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidValueAtExactMinimum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RangeValidator(min: 10, max: 100)]
            public float $field = 10;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidValueAtExactMaximum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RangeValidator(min: 10, max: 100)]
            public float $field = 100;
        };

        $this->assertTrue($validator->isValid($class));
    }
}
