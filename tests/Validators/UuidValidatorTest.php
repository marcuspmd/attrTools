<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\UuidValidator;
use PHPUnit\Framework\TestCase;

class UuidValidatorTest extends TestCase
{
    public function testValidUuid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UuidValidator()]
            public string $field = '123e4567-e89b-12d3-a456-426614174000';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidUuidFormat()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UuidValidator()]
            public string $field = 'invalid-uuid-format';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um UUID válido.', $validator->getError());
    }

    public function testValidNullableUuid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UuidValidator(nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullableUuid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UuidValidator(nullable: false)]
            public ?string $field = null;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field está inválido.', $validator->getError());
    }

    public function testInvalidUuidWithIncorrectLength()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UuidValidator()]
            public string $field = '123e4567-e89b-12d3-a456';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um UUID válido.', $validator->getError());
    }

    public function testInvalidUuidWithSpecialCharacters()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UuidValidator()]
            public string $field = '123e4567-e89b-12d3-a456-4266141740!@#';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um UUID válido.', $validator->getError());
    }
}
