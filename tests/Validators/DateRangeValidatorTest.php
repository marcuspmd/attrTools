<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\DateRangeValidator;
use PHPUnit\Framework\TestCase;

class DateRangeClass
{
    #[DateRangeValidator(minDate: new DateTime('2022-01-01'), maxDate: new DateTime('2023-01-01'))]
    public ?DateTime $dateField;
}

class DateRangeNullableClass
{
    #[DateRangeValidator(nullable: true, minDate: new DateTime('2022-01-01'), maxDate: new DateTime('2023-01-01'))]
    public ?DateTime $dateField = null;
}

class DateRangeValidatorTest extends TestCase
{
    public function testAttrClassAllowNullable()
    {
        $validator = new AttrValidator();
        $class = new DateRangeNullableClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassValidDateWithinRange()
    {
        $validator = new AttrValidator();
        $class = new DateRangeClass();
        $class->dateField = new DateTime('2022-06-01');

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassInvalidDateBeforeMinDate()
    {
        $validator = new AttrValidator();
        $class = new DateRangeClass();
        $class->dateField = new DateTime('2021-12-31');

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: dateField deve ser maior ou igual a 2022-01-01.', $validator->getError());
    }

    public function testAttrClassInvalidDateAfterMaxDate()
    {
        $validator = new AttrValidator();
        $class = new DateRangeClass();
        $class->dateField = new DateTime('2023-02-01');

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: dateField deve ser menor ou igual a 2023-01-01.', $validator->getError());
    }

    public function testInvalidDateType()
    {
        $validator = new DateRangeValidator(field: 'dateField', minDate: new DateTime('2022-01-01'), maxDate: new DateTime('2023-01-01'));

        // Passando um valor que não é uma instância de DateTime
        $this->assertFalse($validator->isValid('not a date'));
        $this->assertEquals('Campo: dateField deve ser uma data válida.', $validator->getError());
    }

    public function testValidDateWithinRange()
    {
        $validator = new DateRangeValidator(field: 'dateField', minDate: new DateTime('2022-01-01'), maxDate: new DateTime('2023-01-01'));
        $this->assertTrue($validator->isValid(new DateTime('2022-06-15')));
    }

    public function testDateBeforeMinDate()
    {
        $validator = new DateRangeValidator(field: 'dateField', minDate: new DateTime('2022-01-01'));
        $this->assertFalse($validator->isValid(new DateTime('2021-12-31')));
        $this->assertEquals('Campo: dateField deve ser maior ou igual a 2022-01-01.', $validator->getError());
    }

    public function testDateAfterMaxDate()
    {
        $validator = new DateRangeValidator(field: 'dateField', maxDate: new DateTime('2023-01-01'));
        $this->assertFalse($validator->isValid(new DateTime('2023-02-01')));
        $this->assertEquals('Campo: dateField deve ser menor ou igual a 2023-01-01.', $validator->getError());
    }
}
