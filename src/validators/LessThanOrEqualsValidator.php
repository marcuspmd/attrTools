<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class LessThanOrEqualsValidator extends BaseValidator implements Validator
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

        if (!is_numeric($valueCompare) || !is_numeric($valueToCompare)) {
            $this->errorCode = 1;

            return false;
        }

        return $valueCompare <= $valueToCompare;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um valor numérico.',
            default => 'Campo: {{field}} deve ser menor ou igual a {{valueToCompare}}.',
        };
    }
}
