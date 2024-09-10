<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\GreaterThanOrEqualsValidator;
use PHPUnit\Framework\TestCase;

class GreaterThanOrEqualsValidatorTest extends TestCase
{
    public function testValidValueGreaterThanFixedValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[GreaterThanOrEqualsValidator(valueToCompare: '10')]
            public int $field = 20;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidValueEqualToFixedValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[GreaterThanOrEqualsValidator(valueToCompare: '10')]
            public int $field = 10;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidValueLessThanFixedValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[GreaterThanOrEqualsValidator(valueToCompare: '10')]
            public int $field = 5;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser maior ou igual a 10.', $validator->getError());
    }

    public function testValidNullable()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[GreaterThanOrEqualsValidator(nullable:true)]
            public ?int $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidValueGreaterThanFieldToCompare()
    {
        $validator = new AttrValidator();
        $class = new class {
            public int $otherField = 10;

            #[GreaterThanOrEqualsValidator(fieldToCompare: 'otherField')]
            public int $field = 20;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidValueLessThanFieldToCompare()
    {
        $validator = new AttrValidator();
        $class = new class {
            public int $otherField = 10;

            #[GreaterThanOrEqualsValidator(fieldToCompare: 'otherField')]
            public int $field = 5;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser maior ou igual a 10.', $validator->getError());
    }

    public function testFieldToCompareNotFound()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[GreaterThanOrEqualsValidator(fieldToCompare: 'nonExistentField')]
            public int $field = 10;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: nonExistentField não encontrado na classe.', $validator->getError());
    }

    public function testValidWithStringLengthComparison()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[GreaterThanOrEqualsValidator(valueToCompare: 'test')]
            public string $field = 'longerString';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidWithArrayLengthComparison()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[GreaterThanOrEqualsValidator(valueToCompare: [1, 2, 3])]
            public array $field = [1, 2, 3, 4, 5];
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidArrayLengthComparison()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[GreaterThanOrEqualsValidator(valueToCompare: [1, 2, 3, 4, 5])]
            public array $field = [1, 2];
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser maior ou igual a 5.', $validator->getError());
    }
}
