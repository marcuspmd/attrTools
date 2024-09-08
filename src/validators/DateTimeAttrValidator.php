<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;
use Closure;
use DateTime;

/**
 * Como usar:
 * Temos 3 formas de usar o DateTimeAttrValidator:
 *  #[DateTimeAttrValidator(min: new DateTime('2023-01-01'), max: new DateTime('2023-12-31'))]
 *  #[DateTimeAttrValidator(min: fn() => new DateTime('now'), max: fn() => (new DateTime())->modify('+1 year'), nullable: true)]
 *  #[DateTimeAttrValidator(min: null, max: new DateTime('2023-12-31'), nullable: true)]
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class DatetimeAttrValidator extends BaseValidator implements Validator
{
    private ?DateTime $resolvedMin = null;
    private ?DateTime $resolvedMax = null;

    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        private readonly DateTime | Closure | null $min = null,
        private readonly DateTime | Closure | null $max = null,
    ) {
        if ($message === null) {
            $message = 'Campo {{field}} não é uma data válida.';
        }
        parent::__construct($field, $message, $nullable);

        $this->resolvedMin = $this->resolveDateTime($this->min);
        $this->resolvedMax = $this->resolveDateTime($this->max);
    }

    /**
     * @param DateTime|null $value
     * @return bool
     */
    public function validate($value): bool
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
            1 => 'Campo: {{field}} deve ser uma data válida.',
            2 => 'Campo: {{field}} deve ser maior ou igual a '.$this->min->format('Y-m-d').'.',
            3 => 'Campo: {{field}} deve ser menor ou igual a '.$this->max->format('Y-m-d').'.',
            default => 'Campo: {{field}} está inválido.',
        };
    }

    private function resolveDateTime(DateTime|Closure|null $dateTime): ?DateTime
    {
        if ($dateTime instanceof DateTime) {
            $dateTime->setTime(0, 0, 0);

            return $dateTime;
        }

        if (is_callable($dateTime)) {
            return $dateTime();
        }

        return null;
    }
}
