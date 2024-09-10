<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\DecimalValidator;
use PHPUnit\Framework\TestCase;

class DecimalValidatorTest extends TestCase
{
    public function testNullableDecimal()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DecimalValidator(nullable: true)]
            public ?float $decimalField = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidDecimalWithinRange()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DecimalValidator(min: 0.0, max: 100.0)]
            public float $decimalField = 50.5;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testDecimalBelowMin()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DecimalValidator(min: 10.0, max: 100.0)]
            public float $decimalField = 5.0;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: decimalField deve ser maior ou igual a 10.', $validator->getError());
    }

    public function testDecimalAboveMax()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DecimalValidator(min: 0.0, max: 50.0)]
            public float $decimalField = 60.0;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: decimalField deve ser menor ou igual a 50.', $validator->getError());
    }

    public function testValidDecimalWithPrecisionAndScale()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DecimalValidator(scale: 2, precision: 5)]
            public float $decimalField = 12.34;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testDecimalExceedsPrecision()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DecimalValidator(scale: 2, precision: 4)]
            public float $decimalField = 123.45;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: decimalField excede a precisão permitida.', $validator->getError());
    }

    public function testDecimalExceedsScale()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DecimalValidator(scale: 2, precision: 6)]
            public float $decimalField = 123.456;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: decimalField excede o número de casas decimais permitidas.', $validator->getError());
    }

    public function testNonNumericValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DecimalValidator()]
            public string $decimalField = 'invalid';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: decimalField deve ser um número.', $validator->getError());
    }
}
