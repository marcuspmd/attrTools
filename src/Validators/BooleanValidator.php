<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class BooleanValidator extends BaseValidator implements Validator
{
    public function isValid($value): bool
    {
        if (is_bool($value)) {
            return true;
        }

        $value = $this->getValue($value);

        if (is_array($value) || is_object($value)) {
            return false;
        }

        if ($this->nullable && $value === null) {
            return true;
        }

        return false;
    }
}
