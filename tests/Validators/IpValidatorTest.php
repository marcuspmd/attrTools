<?php

declare(strict_types=1);

use Marcuspmd\AttrTools\Validators\AttrValidator;
use Marcuspmd\AttrTools\Validators\IpValidator;
use Marcuspmd\AttrTools\Validators\IpType;
use PHPUnit\Framework\TestCase;

class IpValidatorTest extends TestCase
{
    public function testValidIPv4()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IpValidator(type: 'ipv4')]
            public string $field = '192.168.0.1';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidIPv4()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IpValidator(type: 'ipv4')]
            public string $field = '2001:0db8:85a3:0000:0000:8a2e:0370:7334'; // IPv6 address
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um endereço de IP válido do tipo IPV4.', $validator->getError());
    }

    public function testValidIPv6()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IpValidator(type: 'ipv6')]
            public string $field = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';
        };

        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidIPv6()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IpValidator(type: 'ipv6')]
            public string $field = '192.168.0.1'; // IPv4 address
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um endereço de IP válido do tipo IPV6.', $validator->getError());
    }

    public function testValidBothIpTypes()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IpValidator(type: 'both')]
            public string $field = '192.168.0.1'; // Valid IPv4
        };

        $this->assertTrue($validator->isValid($class));

        $class->field = '2001:0db8:85a3:0000:0000:8a2e:0370:7334'; // Valid IPv6
        $this->assertTrue($validator->isValid($class));
    }

    public function testInvalidIp()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IpValidator(type: 'both')]
            public string $field = 'invalid_ip';
        };

        $this->assertFalse($validator->isValid($class));
        $this->assertEquals('Campo: field deve ser um endereço de IP válido.', $validator->getError());
    }

    public function testNullableField()
    {
        $validator = new AttrValidator();
        $class = new class {
            #[IpValidator(type: 'both', nullable: true)]
            public ?string $field = null;
        };

        $this->assertTrue($validator->isValid($class));
    }
}
