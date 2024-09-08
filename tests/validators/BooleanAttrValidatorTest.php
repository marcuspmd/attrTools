<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use PHPUnit\Framework\TestCase;
use Marcuspmd\AttrTools\Validators\BooleanAttrValidator;
use PHPUnit\Framework\Attributes\CoversClass;

class TestNullableClass
{
    #[BooleanAttrValidator(nullable: true)]
    public ?bool $testField = null;
}

class TestClass
{
    #[BooleanAttrValidator]
    public ?bool $testField = true;
}

#[CoversClass(Marcuspmd\AttrTools\Validators\BooleanAttrValidator::class)]
class BooleanAttrValidatorTest extends TestCase
{
    public function testAttrClassAllowNullable()
    {
        $validator = new AttrValidator();
        $class = new TestNullableClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullable()
    {
        $validator = new AttrValidator();
        $class = new TestClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullableError()
    {
        $validator = new AttrValidator();
        $class = new TestClass();
        $class->testField = null;

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo testField é obrigatório.', $validator->getError());
    }


    public function testValidateWithBooleanTrue()
    {
        $validator = new BooleanAttrValidator();
        $this->assertTrue($validator->isValid(true));
    }

    public function testValidateWithBooleanFalse()
    {
        $validator = new BooleanAttrValidator();
        $this->assertTrue($validator->isValid(false));
    }

    public function testValidateWithNonBoolean()
    {
        $validator = new BooleanAttrValidator();
        $this->assertFalse($validator->isValid('string'));
        $this->assertFalse($validator->isValid(123));
        $this->assertFalse($validator->isValid([]));
    }

    public function testValidateWithNullWhenNullableTrue()
    {
        $validator = new BooleanAttrValidator(null, null, true);
        $this->assertTrue($validator->isValid(null));
    }

    public function testValidateWithNullWhenNullableFalse()
    {
        $validator = new BooleanAttrValidator();
        $this->assertFalse($validator->isValid(null));
    }
}