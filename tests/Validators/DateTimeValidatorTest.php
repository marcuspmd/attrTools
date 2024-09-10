<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\DateTimeValidator;
use PHPUnit\Framework\TestCase;
use DateTime;

class DateTimeValidatorTest extends TestCase
{
    public function testAttrClassAllowNullable()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DateTimeValidator(
                min: [self::class, 'getMinDate'],
                max: null,
                nullable: true
            )]
            public ?DateTime $dateField = null;

            public static function getMinDate(): DateTime
            {
                return new DateTime('2022-01-01');
            }
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassValidFixedDateWithinRange()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DateTimeValidator(
                min: new DateTime('2023-01-01'),
                max: new DateTime('2023-12-31')
            )]
            public ?DateTime $dateField = null;
        };
        $class->dateField = new DateTime('2023-06-01');

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassInvalidDateBeforeMinDate()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DateTimeValidator(
                min: new DateTime('2023-01-01'),
                max: new DateTime('2023-12-31')
            )]
            public ?DateTime $dateField = null;
        };
        $class->dateField = new DateTime('2022-12-31');

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: dateField deve ser maior ou igual a 2023-01-01.', $validator->getError());
    }

    public function testAttrClassInvalidDateAfterMaxDate()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[DateTimeValidator(
                min: new DateTime('2023-01-01'),
                max: new DateTime('2023-12-31')
            )]
            public ?DateTime $dateField = null;
        };
        $class->dateField = new DateTime('2024-01-01');

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: dateField deve ser menor ou igual a 2023-12-31.', $validator->getError());
    }

    public function testDynamicRangeWithValidDate()
    {
        $validator = new DateTimeValidator(
            field: 'dateField',
            min: fn() => new DateTime('now'),
            max: fn() => (new DateTime())->modify('+1 year')
        );

        // Definindo uma data dentro do intervalo dinâmico
        $this->assertTrue($validator->isValid(new DateTime('now')));
    }

    public function testDynamicRangeWithInvalidDate()
    {
        $validator = new DateTimeValidator(
            field: 'dateField',
            min: fn() => new DateTime('now'),
            max: fn() => (new DateTime())->modify('+1 year')
        );

        // Definindo uma data fora do intervalo dinâmico (anterior à data atual)
        $this->assertFalse($validator->isValid(new DateTime('2022-12-31')));
        $this->assertEquals('Campo: dateField deve ser maior ou igual a ' . (new DateTime('now'))->format('Y-m-d') . '.', $validator->getError());
    }

    public function testInvalidDateType()
    {
        $validator = new DateTimeValidator(field: 'dateField', min: new DateTime('2023-01-01'), max: new DateTime('2023-12-31'));

        // Passando um valor que não é uma instância de DateTime
        $this->assertFalse($validator->isValid('not a date'));
        $this->assertEquals('Campo: dateField não é uma data válida.', $validator->getError());
    }
}
