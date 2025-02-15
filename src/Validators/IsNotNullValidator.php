<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class IsNotNullValidator extends BaseValidator implements Validator
{


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
