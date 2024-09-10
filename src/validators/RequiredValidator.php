<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class RequiredValidator extends BaseValidator implements Validator
{
    public function __construct(
        bool $nullable = false,
        bool $emptyToNull = false,
        ?string $field = null,
        ?string $message = null,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
            emptyToNull: $emptyToNull
        );
    }

    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        return !empty($value);
    }

    protected function setMessage(): string
    {
        return 'Campo {{field}} é obrigatório.';
    }
}
