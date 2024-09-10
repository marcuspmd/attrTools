<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\InstanceValidator;
use PHPUnit\Framework\TestCase;

class ExampleClass {}

class AnotherClass {}

class InstanceValidatorTest extends TestCase
{
    public function testValidInstanceOfClass()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InstanceValidator(instance: ExampleClass::class)]
            public ExampleClass $field;

            public function __construct() {
                $this->field = new ExampleClass();
            }
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidInstanceOfClass()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InstanceValidator(instance: ExampleClass::class)]
            public AnotherClass $field;

            public function __construct() {
                $this->field = new AnotherClass();
            }
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser uma instância de ExampleClass.', $validator->getError());
    }

    public function testNullableValid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InstanceValidator(instance: ExampleClass::class, nullable: true)]
            public ?ExampleClass $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testNullableInvalid()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[InstanceValidator(instance: ExampleClass::class, nullable: true)]
            public ?AnotherClass $field;

            public function __construct() {
                $this->field = new AnotherClass();
            }
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser uma instância de ExampleClass.', $validator->getError());
    }
}
