<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\IsNotEqualsValidator;
use PHPUnit\Framework\TestCase;

class IsNotEqualsValidatorTest extends TestCase
{
    public function testValidValueNotEqualToFixedValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotEqualsValidator(valueToCompare: 'test')]
            public string $field = 'different';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidValueEqualToFixedValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotEqualsValidator(valueToCompare: 'test')]
            public string $field = 'test';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser diferente de test.', $validator->getError());
    }

    public function testValidValueNotEqualToFieldToCompare()
    {
        $validator = new AttrValidator();
        $class = new class {
            public string $otherField = 'valueToCompare';

            #[IsNotEqualsValidator(fieldToCompare: 'otherField')]
            public string $field = 'differentValue';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidValueEqualToFieldToCompare()
    {
        $validator = new AttrValidator();
        $class = new class {
            public string $otherField = 'sameValue';

            #[IsNotEqualsValidator(fieldToCompare: 'otherField')]
            public string $field = 'sameValue';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser diferente de sameValue.', $validator->getError());
    }

    public function testFieldToCompareNotFound()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotEqualsValidator(fieldToCompare: 'nonExistentField')]
            public string $field = 'someValue';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: nonExistentField não encontrado na classe.', $validator->getError());
    }

    public function testNullableFieldValid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotEqualsValidator(valueToCompare: 'test', nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testNullableFieldInvalid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotEqualsValidator(valueToCompare: 'test', nullable: true)]
            public ?string $field = 'test';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser diferente de test.', $validator->getError());
    }
}
