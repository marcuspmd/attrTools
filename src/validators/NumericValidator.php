<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class NumericValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        public ?float $min = null,
        public ?float $max = null,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
        );
    }

    /**
     * @param string|array $value
     * @return bool
     */
    public function isValid($value): bool
    {
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
            1 => 'Campo: {{field}} deve ser um valor numérico.',
            2 => 'Campo: {{field}} deve ser maior ou igual a {{min}}.',
            3 => 'Campo: {{field}} deve ser menor ou igual a {{max}}.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
