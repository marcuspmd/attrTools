<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\RequiredValidator;
use PHPUnit\Framework\TestCase;

class RequiredValidatorTest extends TestCase
{
    public function testValidNonEmptyString()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator()]
            public string $field = 'testString';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidEmptyString()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator()]
            public string $field = '';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo field é obrigatório.', $validator->getError());
    }

    public function testValidNonNullInteger()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator()]
            public int $field = 123;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator()]
            public ?string $field = null;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo field é obrigatório.', $validator->getError());
    }

    public function testValidEmptyStringWithEmptyToNull()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator(emptyToNull: true)]
            public string $field = '';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidEmptyStringWithoutEmptyToNull()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator(emptyToNull: false)]
            public string $field = '';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo field é obrigatório.', $validator->getError());
    }

    public function testValidNullableField()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator(nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullableFieldWithEmptyValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator(nullable: true)]
            public ?string $field = '';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo field é obrigatório.', $validator->getError());
    }

    public function testNotValidateUsingWhen()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RequiredValidator(when: 'alwaysFalse')]
            public ?string $field = null;

            public function alwaysFalse(): bool
            {
                return false;
            }

        };


        $this->assertTrue($validator->isValid($class));
    }
}
