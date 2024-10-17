<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;
use Closure;
use DateTime;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class DateTimeValidator extends BaseValidator implements Validator
{
    private ?DateTime $resolvedMin = null;
    private ?DateTime $resolvedMax = null;

    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        private $min = null,
        private $max = null,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
        );

        $this->resolvedMin = $this->resolveDateTime($this->min);
        $this->resolvedMax = $this->resolveDateTime($this->max);
    }

    /**
     * @param DateTime|null $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (!($value instanceof DateTime)) {
            $this->errorCode = 1;
            return false;
        }

        if ($this->resolvedMin !== null && $value < $this->resolvedMin) {
            $this->errorCode = 2;
            return false;
        }

        if ($this->resolvedMax !== null && $value > $this->resolvedMax) {
            $this->errorCode = 3;
            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} não é uma data válida.',
            2 => 'Campo: {{field}} deve ser maior ou igual a ' . $this->resolvedMin->format('Y-m-d') . '.',
            3 => 'Campo: {{field}} deve ser menor ou igual a ' . $this->resolvedMax->format('Y-m-d') . '.',
            default => 'Campo: {{field}} está inválido.',
        };
    }

    private function resolveDateTime(DateTime|callable|Closure|null $dateTime): ?DateTime
    {
        if ($dateTime instanceof DateTime) {
            $dateTime->setTime(0, 0, 0);
            return $dateTime;
        }

        if (is_callable($dateTime)) {
            $resolved = $dateTime();
            if ($resolved instanceof DateTime) {
                $resolved->setTime(0, 0, 0);
                return $resolved;
            }
        }

        return null;
    }
}
