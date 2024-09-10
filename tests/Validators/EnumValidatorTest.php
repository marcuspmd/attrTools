<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\EnumValidator;
use PHPUnit\Framework\TestCase;

// Enum válido para os testes
enum AccountingTypeEnum: string
{
    case TYPE_A = 'A';
    case TYPE_B = 'B';
    case TYPE_C = 'C';
}

class EnumValidatorTest extends TestCase
{
    public function testNullableEnum()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EnumValidator(enum: AccountingTypeEnum::class, nullable: true)]
            public ?string $enumField = null;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testValidEnumValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EnumValidator(enum: AccountingTypeEnum::class)]
            public string $enumField = AccountingTypeEnum::TYPE_A->value;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidEnumValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EnumValidator(enum: AccountingTypeEnum::class)]
            public string $enumField = 'INVALID';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: enumField deve ser um valor válido do enum AccountingTypeEnum.', $validator->getError());
    }

    public function testValidEnumWithDifferentValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EnumValidator(enum: AccountingTypeEnum::class)]
            public string $enumField = AccountingTypeEnum::TYPE_B->value;
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testEmptyEnumValue()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EnumValidator(enum: AccountingTypeEnum::class)]
            public string $enumField = '';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: enumField deve ser um valor válido do enum AccountingTypeEnum.', $validator->getError());
    }

    public function testEnumClassDoesNotExist()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[EnumValidator(enum: 'InvalidEnum', nullable: false)]
            public string $enumField = 'A';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('A classe InvalidEnum não é um enum válido.', $validator->getError());
    }
}
