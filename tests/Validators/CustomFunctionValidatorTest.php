<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\CustomFunctionValidator;
use PHPUnit\Framework\TestCase;

class CustomFunctionClass
{
    #[CustomFunctionValidator(callback: [self::class, 'validateStatic'])]
    public ?string $customField = 'example';

    public static function validateStatic($value): bool
    {
        return strlen($value) > 5;
    }

    public function validateInstance($value): bool
    {
        return $value === 'valid';
    }
}

class CustomFunctionNullableClass
{
    #[CustomFunctionValidator(callback: [self::class, 'validateStatic'], nullable: true)]
    public ?string $customField = null;

    public static function validateStatic($value): bool
    {
        return strlen($value) > 5;
    }
}

class CustomFunctionValidatorTest extends TestCase
{
    public function testAttrClassAllowNullable()
    {
        $validator = new AttrValidator();
        $class = new CustomFunctionNullableClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullable()
    {
        $validator = new AttrValidator();
        $class = new CustomFunctionClass();
        $class->customField = 'example';

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullableError()
    {
        $validator = new AttrValidator();
        $class = new CustomFunctionClass();
        $class->customField = 'fail';

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: customField falhou na validação personalizada.', $validator->getError());
    }

    public function testInvalidCallableCallbackIsNUll()
    {
        $validator = new CustomFunctionValidator(field: 'customField');
        $this->assertFalse($validator->isValid('fail'));
    }

    public function testAnonymousFunctionSuccess()
    {
        $validator = new CustomFunctionValidator(
            field: 'customField',
            callback: function ($value) {
                return strlen($value) > 3;
            }
        );
        $this->assertTrue($validator->isValid('valid'));
    }

    public function testAnonymousFunctionFailure()
    {
        $validator = new CustomFunctionValidator(
            field: 'customField',
            callback: function ($value) {
                return strlen($value) > 10;
            }
        );
        $this->assertFalse($validator->isValid('short'));
        $this->assertEquals('Campo: customField falhou na validação personalizada.', $validator->getError());
    }

    public function testInstanceMethodCallback()
    {
        $instance = new CustomFunctionClass();
        $validator = new CustomFunctionValidator(field: 'customField', callback: [$instance, 'validateInstance']);
        $this->assertTrue($validator->isValid('valid'));
    }

    public function testInstanceMethodCallbackFailure()
    {
        $instance = new CustomFunctionClass();
        $validator = new CustomFunctionValidator(field: 'customField', callback: [$instance, 'validateInstance']);
        $this->assertFalse($validator->isValid('invalid'));
        $this->assertEquals('Campo: customField falhou na validação personalizada.', $validator->getError());
    }
}
