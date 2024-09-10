<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\UrlValidator;
use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase
{
    public function testValidUrl()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UrlValidator()]
            public string $field = 'https://example.com';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidUrl()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UrlValidator()]
            public string $field = 'invalid-url';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser uma URL válida.', $validator->getError());
    }

    public function testValidNullableUrl()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UrlValidator(nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidNullableUrl()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UrlValidator(nullable: false)]
            public ?string $field = null;
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field está inválido.', $validator->getError());
    }

    public function testInvalidUrlWithSpecialCharacters()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UrlValidator()]
            public string $field = 'htt#p://ex#ample.com/test@@@<>characters';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser uma URL válida.', $validator->getError());
    }

    public function testValidUrlWithPort()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UrlValidator()]
            public string $field = 'http://example.com:8080';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidUrlWithPathAndQuery()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[UrlValidator()]
            public string $field = 'https://example.com/path?query=string';
        };

        $this->assertTrue($validator->isValid($class));
    }
}
