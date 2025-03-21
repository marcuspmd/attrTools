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
    public function isValid($value): bool
    {
        $this->type = IpType::tryFrom(mb_strtolower($this->type ?? IpType::BOTH->value));
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
