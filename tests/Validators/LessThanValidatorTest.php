<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\LessThanValidator;
use PHPUnit\Framework\TestCase;

class LessThanValidatorTest extends TestCase
{
    public function testValidValueLessThanFixedValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: '10')]
            public int $field = 5;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidValueEqualToFixedValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: '10')]
            public int $field = 10;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor que 10.', $validator->getError());
    }

    public function testInvalidValueGreaterThanFixedValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: '10')]
            public int $field = 15;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor que 10.', $validator->getError());
    }

    public function testValidValueLessThanFieldToCompare()
    {
        $validator = new AttrValidator();
        $class = new class {
            public int $otherField = 10;

            #[LessThanValidator(fieldToCompare: 'otherField')]
            public int $field = 5;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidValueEqualToFieldToCompare()
    {
        $validator = new AttrValidator();
        $class = new class {
            public int $otherField = 10;

            #[LessThanValidator(fieldToCompare: 'otherField')]
            public int $field = 10;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor que 10.', $validator->getError());
    }

    public function testInvalidValueGreaterThanFieldToCompare()
    {
        $validator = new AttrValidator();
        $class = new class {
            public int $otherField = 10;

            #[LessThanValidator(fieldToCompare: 'otherField')]
            public int $field = 15;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor que 10.', $validator->getError());
    }

    public function testFieldToCompareNotFound()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(fieldToCompare: 'nonExistentField')]
            public int $field = 5;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: nonExistentField não encontrado na classe.', $validator->getError());
    }

    public function testNullableFieldValid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: '10', nullable: true)]
            public ?int $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testNullableFieldInvalid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: '10', nullable: true)]
            public ?int $field = 15;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor que 10.', $validator->getError());
    }

    public function testValidStringLengthComparison()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: '12345')]
            public string $field = 'test';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidStringLengthComparison()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: 'test')]
            public string $field = 'longerString';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor que 4.', $validator->getError());
    }

    public function testValidArrayLengthComparison()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: [1, 2, 3])]
            public array $field = [1, 2];
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidArrayLengthComparison()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LessThanValidator(valueToCompare: [1, 2])]
            public array $field = [1, 2, 3, 4];
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser menor que 2.', $validator->getError());
    }
}
