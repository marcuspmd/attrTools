<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class BooleanAttrValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
    ) {
        if ($message === null) {
            $message = 'Campo {{field}} é obrigatório.';
        }
        parent::__construct($field, $message, $nullable);
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

        return is_bool($value);
    }
}
