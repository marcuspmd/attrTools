<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class CountValidator extends BaseValidator implements Validator
{
    /**
     * @param string|array $value
     * @return bool
     */
    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        if ($value === null || is_string($value)) {
            $this->errorCode = 1;

            return false;
        }

        $length = count($value);

        if ($this->min !== null && $length < $this->min) {
            $this->errorCode = 2;

            return false;
        }

        if ($this->max !== null && $length > $this->max) {
            $this->errorCode = 3;

            return false;
        }

        if ($length === 0) {
            $this->errorCode = 4;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um array ou coleção.',
            2 => 'Campo: {{field}} deve ter no mínimo {{min}} objeto(s).',
            3 => 'Campo: {{field}} deve ter no máximo {{max}} objeto(s).',
            4 => 'Campo: {{field}} não pode estar vazio.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
