<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class IpAttrValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false
    ) {
        parent::__construct($field, $message, $nullable);
    }

    public function validate($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (!filter_var($value, FILTER_VALIDATE_IP)) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um endereço IP válido.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
