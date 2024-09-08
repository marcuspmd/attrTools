<?php

namespace App\Helpers\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class GreaterThanValidator extends BaseValidator implements Validator
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

        $valueCompare = $this->getValue($value);

        $valueToCompare = $this->valueToCompare;
        if ($this->fieldToCompare !== null) {
            $this->field = $this->fieldToCompare;
            $valueToCompare = $this->getValue($value);
        }

        return $valueCompare > $valueToCompare;
    }

    protected function setMessage(): string
    {
        return 'Campo: {{field}} deve ser maior que {{valueToCompare}}.';
    }
}
