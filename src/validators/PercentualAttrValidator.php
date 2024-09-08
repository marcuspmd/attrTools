<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class PercentualAttrValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        public readonly ?int $min = null,
        public readonly ?int $max = null
    ) {
        parent::__construct($field, $message, $nullable);
    }

    /**
     * @param string|float|int|null $value
     * @return bool
     */
    public function validate($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (is_string($value)) {
            $value = (float) $value;
        }

        if ($value === null) {
            $this->message = 'Campo: {{field}} deve ser um valor percentual.';

            return false;
        }

        if ($value < 0) {
            $this->message = 'Campo: {{field}} deve ser um valor percentual positivo.';

            return false;
        }

        if ($value > 100) {
            $this->message = 'Campo: {{field}} deve ser um valor percentual menor ou igual a 100.';

            return false;
        }

        if ($this->min !== null && $value < $this->min) {
            $this->message = 'Campo: {{field}} deve ter no mínimo {{min}}.';

            return false;
        }

        if ($this->max !== null && $value > $this->max) {
            $this->message = 'Campo: {{field}} deve ter no máximo {{max}}.';

            return false;
        }

        return true;
    }
}
