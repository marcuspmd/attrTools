<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\RegexValidator;
use PHPUnit\Framework\TestCase;

class RegexValidatorTest extends TestCase
{
    public function testValidStringMatchingPattern()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RegexValidator(pattern: '/^[a-zA-Z]+$/')]
            public string $field = 'testString';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidStringNotMatchingPattern()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RegexValidator(pattern: '/^[a-zA-Z]+$/')]
            public string $field = 'test123';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não corresponde ao padrão esperado.', $validator->getError());
    }

    public function testNullValueWhenNullable()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RegexValidator(pattern: '/^[a-zA-Z]+$/', nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testEmptyStringWhenEmptyToNull()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RegexValidator(pattern: '/^[a-zA-Z]+$/', emptyToNull: true)]
            public string $field = '';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullPattern()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RegexValidator()]
            public string $field = 'testString';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não é uma string válida.', $validator->getError());
    }

    public function testNonStringValueInvalid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RegexValidator(pattern: '/^[a-zA-Z]+$/')]
            public int $field = 12345;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não é uma string válida.', $validator->getError());
    }

    public function testEmptyStringInvalidWithoutEmptyToNull()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[RegexValidator(pattern: '/^[a-zA-Z]+$/')]
            public string $field = '';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field não corresponde ao padrão esperado.', $validator->getError());
    }
}
