<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class InArrayValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        private readonly array $allowedValues = []
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

        if (!in_array($value, $this->allowedValues, true)) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um dos seguintes valores: ' . implode(', ', $this->allowedValues) . '.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
