<?php

namespace App\Helpers\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class IsNotNullValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null
    ) {
        if ($message === null) {
            $message = 'Campo: {{field}} não pode ser nulo.';
        }
        parent::__construct($field, $message);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool
    {
        $value = $this->getValue($value);

        return isset($value) && !is_null($value);
    }

    protected function setMessage(): string
    {
        return 'Campo: {{field}} não pode ser nulo.';
    }
}
