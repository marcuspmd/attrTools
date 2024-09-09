<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;
use DateTime;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class DateRangeValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        private readonly ?DateTime $minDate = null,
        private readonly ?DateTime $maxDate = null
    ) {
        parent::__construct($field, $message, $nullable);
    }

    public function isValid($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (!($value instanceof DateTime)) {
            $this->errorCode = 1;

            return false;
        }

        if ($this->minDate !== null && $value < $this->minDate) {
            $this->errorCode = 2;

            return false;
        }

        if ($this->maxDate !== null && $value > $this->maxDate) {
            $this->errorCode = 3;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser uma data válida.',
            2 => 'Campo: {{field}} deve ser maior ou igual a {{minDate}}.',
            3 => 'Campo: {{field}} deve ser menor ou igual a {{maxDate}}.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}