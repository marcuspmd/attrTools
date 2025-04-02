<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\PercentualValidator;
use PHPUnit\Framework\TestCase;

class PercentualValidatorTest extends TestCase
{
    public function testValidPercentualWithinRange()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(min: 10, max: 90)]
            public float $field = 50.5;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidPercentualWithoutRange()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator()]
            public float $field = 75.5;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNonNumericValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator()]
            public string $field = 'not_a_number';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um valor percentual.', $validator->getError());
    }

    public function testInvalidNegativePercentual()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(min: 0)]
            public float $field = -10;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ter no mínimo 0.', $validator->getError());
    }

    public function testInvalidPercentualAbove100()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(max: 100)]
            public float $field = 110;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ter no máximo 100.', $validator->getError());
    }

    public function testInvalidPercentualBelowMinimum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(min: 20)]
            public float $field = 10;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ter no mínimo 20.', $validator->getError());
    }

    public function testInvalidPercentualAboveMaximum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(max: 80)]
            public float $field = 90;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ter no máximo 80.', $validator->getError());
    }

    public function testValidNullableField()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(nullable: true)]
            public ?float $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullableFieldWithValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(nullable: true, min: 20)]
            public ?float $field = 10;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ter no mínimo 20.', $validator->getError());
    }

    public function testValidPercentualAtExactMinimum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(min: 20)]
            public float $field = 20;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidPercentualAtExactMaximum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[PercentualValidator(max: 80)]
            public float $field = 80;
        };

        $this->assertTrue($validator->isValid($class));
    }
}
