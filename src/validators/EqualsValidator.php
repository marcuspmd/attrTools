<?php

namespace App\Helpers\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class EqualsValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        private readonly ?string $valueToCompare = null,
        private readonly ?string $fieldToCompare = null,
        private readonly ?string $type = null,
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

        if ($this->type !== null && gettype($value) !== $this->type) {
            $this->errorCode = 2;

            return false;
        }

        if ($value != $valueToCompare) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} não corresponde ao valor esperado.',
            2 => 'Campo: {{field}} deve ser do tipo {{type}}.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
