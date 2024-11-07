<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class RegexValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        ?bool $emptyToNull = false,
        private readonly ?string $pattern = null
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            emptyToNull: $emptyToNull,
            nullable: $nullable
        );
    }

    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        if (!is_string($value) || $this->pattern === null) {
            $this->errorCode = 1;

            return false;
        }

        if (!preg_match($this->pattern, $value)) {
            $this->errorCode = 2;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} não é uma string válida.',
            2 => 'Campo: {{field}} não corresponde ao padrão esperado.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
