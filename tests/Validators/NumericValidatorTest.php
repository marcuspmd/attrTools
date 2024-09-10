<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\NumericValidator;
use PHPUnit\Framework\TestCase;

class NumericValidatorTest extends TestCase
{
    public function testValidNumericValueWithinRange()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator(min: 10, max: 100)]
            public float $field = 50.5;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidNumericValueWithoutRange()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator()]
            public float $field = 123.45;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNonNumericValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator()]
            public string $field = 'not_a_number';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um valor numérico.', $validator->getError());
    }

    public function testInvalidValueBelowMinimum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator(min: 10)]
            public float $field = 5.5;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser maior ou igual a 10.', $validator->getError());
    }

    public function testInvalidValueAboveMaximum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator(max: 100)]
            public float $field = 150.75;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor ou igual a 100.', $validator->getError());
    }

    public function testValidNullableField()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator(nullable: true)]
            public ?float $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullableFieldWithValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator(nullable: true, min: 10)]
            public ?float $field = 5.5;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser maior ou igual a 10.', $validator->getError());
    }

    public function testValidValueAtExactMinimum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator(min: 10)]
            public float $field = 10;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidValueAtExactMaximum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[NumericValidator(max: 100)]
            public float $field = 100;
        };

        $this->assertTrue($validator->isValid($class));
    }
}
