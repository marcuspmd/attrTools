<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class IsNotNullValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null
    ) {
        parent::__construct(
            field: $field,
            message: $message,
        );
    }

    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        return isset($value) && !is_null($value);
    }

    protected function setMessage(): string
    {
        return 'Campo: {{field}} não pode ser nulo.';
    }
}
