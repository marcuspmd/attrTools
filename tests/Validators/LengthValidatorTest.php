<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\LengthValidator;
use PHPUnit\Framework\TestCase;

class LengthValidatorTest extends TestCase
{
    public function testValidLengthWithinRange()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LengthValidator(min: 3, max: 10)]
            public string $field = 'example';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidLengthBelowMinimum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LengthValidator(min: 5)]
            public string $field = 'test';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ter no mínimo 5 caracteres.', $validator->getError());
    }

    public function testInvalidLengthAboveMaximum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LengthValidator(max: 5)]
            public string $field = 'testing';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ter no máximo 5 caracteres.', $validator->getError());
    }

    public function testValidLengthAtExactMinimum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LengthValidator(min: 4)]
            public string $field = 'test';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidLengthAtExactMaximum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LengthValidator(max: 4)]
            public string $field = 'test';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testEmptyStringInvalid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LengthValidator()]
            public string $field = '';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não pode estar vazio.', $validator->getError());
    }

    public function testNullableFieldValid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LengthValidator(nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidTypeNotString()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[LengthValidator()]
            public int $field = 12345;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser uma string válida.', $validator->getError());
    }
}
