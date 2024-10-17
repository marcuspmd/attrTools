<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class PercentualValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        public readonly ?int $min = null,
        public readonly ?int $max = null
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
        );
    }

    public function isValid($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (!is_numeric($value)) {
            $this->errorCode = 1;
            return false;
        }

        if ($value < 0) {
            $this->errorCode = 2;
            return false;
        }

        if ($value > 100) {
            $this->errorCode = 3;
            return false;
        }

        if ($this->min !== null && $value < $this->min) {
            $this->errorCode = 4;
            return false;
        }

        if ($this->max !== null && $value > $this->max) {
            $this->errorCode = 5;
            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um valor percentual.',
            2 => 'Campo: {{field}} deve ser um valor percentual positivo.',
            3 => 'Campo: {{field}} deve ser um valor percentual menor ou igual a 100.',
            4 => 'Campo: {{field}} deve ter no mínimo {{min}}.',
            5 => 'Campo: {{field}} deve ter no máximo {{max}}.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
