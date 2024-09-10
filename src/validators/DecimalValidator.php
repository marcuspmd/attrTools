<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class DecimalValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        public ?float $min = null,
        public ?float $max = null,
        public ?int $scale = null,
        public ?int $precision = null,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
        );
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

        if (!is_numeric($value)) {
            $this->errorCode = 1;

            return false;
        }

        if ($this->precision !== null && !$this->validatePrecision($value)) {
            $this->errorCode = 2;

            return false;
        }

        if ($this->scale !== null && !$this->validateScale($value)) {
            $this->errorCode = 3;

            return false;
        }

        if ($this->min !== null && $value < $this->min) {
            $this->errorCode = 4;

            return false;
        }

        if ($this->max !== null && $value > $this->max) {
            $this->errorCode = 5;

            return false;
        }

        return true;
    }

    /**
     * Valida o número de dígitos significativos (precision).
     *
     * @param float $value
     * @return bool
     */
    private function validatePrecision(float $value): bool
    {
        $digits = strlen(str_replace('.', '', (string) $value));

        return $digits <= $this->precision;
    }

    /**
     * Valida o número de casas decimais (scale).
     *
     * @param float $value
     * @return bool
     */
    private function validateScale(float $value): bool
    {
        $decimalPart = explode('.', (string) $value)[1] ?? '';

        return strlen($decimalPart) <= $this->scale;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um número.',
            2 => 'Campo: {{field}} excede a precisão permitida.',
            3 => 'Campo: {{field}} excede o número de casas decimais permitidas.',
            4 => 'Campo: {{field}} deve ser maior ou igual a {{min}}.',
            5 => 'Campo: {{field}} deve ser menor ou igual a {{max}}.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
