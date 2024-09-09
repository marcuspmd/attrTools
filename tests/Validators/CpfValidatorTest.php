<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\CpfValidator;
use PHPUnit\Framework\TestCase;

class CpfClass
{
    #[CpfValidator]
    public ?string $cpfField = '12345678909';
}

class CpfNullableClass
{
    #[CpfValidator(nullable: true)]
    public ?string $cpfField = null;
}

class CpfValidatorTest extends TestCase
{
    public function testAttrClassAllowNullable()
    {
        $validator = new AttrValidator();
        $class = new CpfNullableClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullable()
    {
        $validator = new AttrValidator();
        $class = new CpfClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullableError()
    {
        $validator = new AttrValidator();
        $class = new CpfClass();
        $class->cpfField = null;

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: cpfField é um CPF inválido.', $validator->getError());
    }

    public function testAttrClassInvalidCpfWithInvalidDigits()
    {
        $validator = new AttrValidator();
        $class = new CpfClass();
        $class->cpfField = '12345678900'; // CPF com dígitos verificadores inválidos

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: cpfField é um CPF inválido.', $validator->getError());
    }

    public function testAttrClassInvalidCpfWithRepeatedNumbers()
    {
        $validator = new AttrValidator();
        $class = new CpfClass();
        $class->cpfField = '11111111111'; // CPF inválido com números repetidos

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: cpfField é um CPF inválido.', $validator->getError());
    }

    public function testAttrClassInvalidCpfWithLessThan11Digits()
    {
        $validator = new AttrValidator();
        $class = new CpfClass();
        $class->cpfField = '123456789'; // CPF com menos de 11 dígitos

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: cpfField é um CPF inválido.', $validator->getError());
    }

    public function testAttrClassInvalidCpfWithMoreThan11Digits()
    {
        $validator = new AttrValidator();
        $class = new CpfClass();
        $class->cpfField = '123456789012'; // CPF com mais de 11 dígitos

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: cpfField é um CPF inválido.', $validator->getError());
    }

    public function testValidCpf()
    {
        $validator = new CpfValidator();
        $this->assertTrue($validator->isValid('12345678909')); // CPF válido (exemplo)
    }

    public function testInvalidCpf()
    {
        $validator = new CpfValidator();
        $this->assertFalse($validator->isValid('12345678900')); // CPF inválido
    }

    public function testNullableCpf()
    {
        $validator = new CpfValidator(null, null, true);
        $this->assertTrue($validator->isValid(null));
    }

    public function testInvalidCpfWithNonNumericCharacters()
    {
        $validator = new CpfValidator();
        $validator->field = 'cpfField';
        $this->assertFalse($validator->isValid('123.456.789-19'));
        $this->assertEquals('Campo: cpfField é um CPF inválido.', $validator->getError());
    }
}
