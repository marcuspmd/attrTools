<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class EmailValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        ?bool $emptyToNull = false,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
            emptyToNull: $emptyToNull
        );
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um e-mail válido.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
