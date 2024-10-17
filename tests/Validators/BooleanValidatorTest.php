<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use PHPUnit\Framework\TestCase;
use Marcuspmd\AttrTools\Validators\BooleanValidator;

class BooleanNullableClass
{
    #[BooleanValidator(nullable: true)]
    public ?bool $testField = null;
}

class BooleanClass
{
    #[BooleanValidator]
    public ?bool $testField = true;
}

class BooleanValidatorTest extends TestCase
{
    public function testAttrClassAllowNullable()
    {
        $validator = new AttrValidator();
        $class = new BooleanNullableClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullable()
    {
        $validator = new AttrValidator();
        $class = new BooleanClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullableError()
    {
        $validator = new AttrValidator();
        $class = new BooleanClass();
        $class->testField = null;

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo testField é obrigatório.', $validator->getError());
    }


    public function testValidateWithBooleanTrue()
    {
        $validator = new BooleanValidator();
        $this->assertTrue($validator->isValid(true));
    }

    public function testValidateWithBooleanFalse()
    {
        $validator = new BooleanValidator();
        $this->assertTrue($validator->isValid(false));
    }

    public function testValidateWithNonBoolean()
    {
        $validator = new BooleanValidator();
        $this->assertFalse($validator->isValid('string'));
        $this->assertFalse($validator->isValid(123));
    }

    public function testValidateWithNullWhenNullableTrue()
    {
        $validator = new BooleanValidator(null, null, true);
        $this->assertTrue($validator->isValid(null));
    }

    public function testValidateWithNullWhenNullableFalse()
    {
        $validator = new BooleanValidator();
        $this->assertFalse($validator->isValid(null));
    }
}