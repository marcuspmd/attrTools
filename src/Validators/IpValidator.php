<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

enum IpType: string
{
    case IPV4 = 'ipv4';
    case IPV6 = 'ipv6';
    case BOTH = 'both';
}

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class IpValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        ?bool $emptyToNull = false,
        public readonly ?IpType $type = IpType::BOTH,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
            emptyToNull: $emptyToNull
        );
    }

    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        if ($this->type === IpType::IPV4 && !filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $this->errorCode = 1;

            return false;
        }

        if ($this->type === IpType::IPV6 && !filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $this->errorCode = 2;

            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            $this->errorCode = 3;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um endereço de IP válido do tipo IPV4.',
            2 => 'Campo: {{field}} deve ser um endereço de IP válido do tipo IPV6.',
            3 => 'Campo: {{field}} deve ser um endereço de IP válido.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
