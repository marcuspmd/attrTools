<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class PercentualValidator extends BaseValidator implements Validator
{

    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        if (!is_numeric($value)) {
            $this->errorCode = 1;
            return false;
        }

        if ($this->min !== null && $value < $this->min) {
            $this->errorCode = 2;
            return false;
        }

        if ($this->max !== null && $value > $this->max) {
            $this->errorCode = 3;
            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um valor percentual.',
            2 => 'Campo: {{field}} deve ter no mínimo {{min}}.',
            3 => 'Campo: {{field}} deve ter no máximo {{max}}.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
