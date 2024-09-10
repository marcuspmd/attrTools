<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\EmailValidator;
use PHPUnit\Framework\TestCase;

class EmailValidatorTest extends TestCase
{
    public function testNullableEmail()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EmailValidator(nullable: true)]
            public ?string $emailField = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidEmail()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EmailValidator()]
            public string $emailField = 'test@example.com';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidEmail()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EmailValidator()]
            public string $emailField = 'invalid-email';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: emailField deve ser um e-mail válido.', $validator->getError());
    }

    public function testEmptyEmail()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EmailValidator()]
            public string $emailField = '';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: emailField deve ser um e-mail válido.', $validator->getError());
    }
}
