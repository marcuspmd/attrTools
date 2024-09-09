<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use PHPUnit\Framework\TestCase;
use Marcuspmd\AttrTools\Validators\CnpjValidator;
use PHPUnit\Framework\Attributes\CoversClass;

class CnpjNullableClass
{
    #[CnpjValidator(nullable: true)]
    public ?string $field = null;
}

class CnpjClass
{
    #[CnpjValidator]
    public ?string $field = '39549883000129';
}

#[CoversClass(CnpjValidator::class)]
class CnpjValidatorTest extends TestCase
{

    public function testAttrClassAllowNullable()
    {
        $validator = new AttrValidator();
        $class = new CnpjNullableClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassDisallowNullable()
    {
        $validator = new AttrValidator();
        $class = new CnpjClass();

        $this->assertTrue($validator->isValid($class));
    }

    public function testAttrClassInvalidRepeatableNumberCnpj()
    {
        $validator = new AttrValidator();
        $class = new CnpjClass();
        $class->field = '11.111.111/1111-11';

        $this->assertFalse($validator->isValid($class));
    }

    public function testAttrClassInvalidCnpjMoreThan14Digits()
    {
        $validator = new AttrValidator();
        $class = new CnpjClass();
        $class->field = '11.111.111/11111-11';

        $this->assertFalse($validator->isValid($class));
    }

    public function testAttrClassInvalidCnpjLessThan14Digits()
    {
        $validator = new AttrValidator();
        $class = new CnpjClass();
        $class->field = '11.111.111/111-11';

        $this->assertFalse($validator->isValid($class));
    }

    public function testAttrClassInvalidCnpjErrorRest()
    {
        $validator = new AttrValidator();
        $class = new CnpjClass();
        $class->field = '39549883000107';

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('CNPJ inválido.', $validator->getError());
    }

    public function testValidCnpj()
    {
        $validator = new CnpjValidator();
        $this->assertTrue($validator->isValid('39549883000129'));
    }

    public function testInvalidCnpj()
    {
        $validator = new CnpjValidator();
        $this->assertFalse($validator->isValid('12.345.678/0001-91'));
    }

    public function testNullableCnpj()
    {
        $validator = new CnpjValidator(null, null, true);
        $this->assertTrue($validator->isValid(null));
    }
}