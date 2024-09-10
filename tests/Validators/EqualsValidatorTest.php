<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\EqualsValidator;
use PHPUnit\Framework\TestCase;

class EqualsValidatorTest extends TestCase
{
    public function testValidEquality()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EqualsValidator(valueToCompare: 'test')]
            public string $field = 'test';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidEquality()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EqualsValidator(valueToCompare: 'expectedValue')]
            public string $field = 'actualValue';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não corresponde ao valor esperado.', $validator->getError());
    }

    public function testValidEqualityWithType()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EqualsValidator(valueToCompare: '123', type: 'string')]
            public string $field = '123';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidType()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EqualsValidator(valueToCompare: '123', type: 'string')]
            public int $field = 123;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser do tipo string.', $validator->getError());
    }

    public function testNullableFieldValid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EqualsValidator(valueToCompare: 'value', nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testFieldToCompare()
    {
        $validator = new AttrValidator();
        $class = new class {
            public string $fieldToCompare = 'match';

            #[EqualsValidator(fieldToCompare: 'fieldToCompare')]
            public string $field = 'match';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testFieldToCompareInvalid()
    {
        $validator = new AttrValidator();
        $class = new class {
            public string $fieldToCompare = 'expected';

            #[EqualsValidator(fieldToCompare: 'fieldToCompare')]
            public string $field = 'actual';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não corresponde ao valor esperado.', $validator->getError());
    }

    public function testFieldToCompareNotFound()
    {
        $validator = new AttrValidator();
        $class = new class {
            public string $fieldToCompare = 'expected';

            #[EqualsValidator(fieldToCompare: 'nonExistentField')]
            public string $field = 'actual';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: nonExistentField não encontrado na classe.', $validator->getError());
    }

}
