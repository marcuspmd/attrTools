<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class UrlValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false
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

        if ($value === null) {
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errorCode = 1;
            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser uma URL válida.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
