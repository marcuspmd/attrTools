<?php

namespace App\Helpers\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class GreaterThanOrEqualsValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        private readonly ?string $valueToCompare = null,
        private readonly ?string $fieldToCompare = null
    ) {
        parent::__construct($field, $message, $nullable);
    }

    /**
     * @param array|string|int|float $value
     * @return bool
     */
    public function validate($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        $valueToCompare = $this->valueToCompare;

        if ($this->fieldToCompare !== null) {
            $valueToCompare = $this->getValue($value);
        }

        return $value >= $valueToCompare;
    }

    protected function setMessage(): string
    {
        return 'Campo: {{field}} deve ser maior ou igual a {{valueToCompare}}.';
    }
}
