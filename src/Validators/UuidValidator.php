<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class UuidValidator extends BaseValidator implements Validator
{


    public function isValid($value): bool
    {
        $value = $this->getValue($value);
        if ($this->nullable && $value === null) {
            return true;
        }

        if ($value === null) {
            return false;
        }

        if (!is_string($value) || !preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $value)) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um UUID válido.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
