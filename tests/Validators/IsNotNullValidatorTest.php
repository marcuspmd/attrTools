<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\IsNotNullValidator;
use PHPUnit\Framework\TestCase;

class IsNotNullValidatorTest extends TestCase
{
    public function testValidNonNullValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotNullValidator()]
            public string $field = 'value';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotNullValidator()]
            public ?string $field = null;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não pode ser nulo.', $validator->getError());
    }

    public function testValidNonNullInteger()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotNullValidator()]
            public int $field = 10;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullInteger()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotNullValidator()]
            public ?int $field = null;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não pode ser nulo.', $validator->getError());
    }

    public function testValidNonNullArray()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotNullValidator()]
            public array $field = [1, 2, 3];
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullArray()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IsNotNullValidator()]
            public ?array $field = null;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não pode ser nulo.', $validator->getError());
    }
}
