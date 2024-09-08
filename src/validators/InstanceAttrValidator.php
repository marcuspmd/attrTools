<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

/**
 * Como usar:
 * #[InstanceAttrValidator(instance: instance::class)]
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class InstanceAttrValidator extends BaseValidator implements Validator
{
    public function __construct(
        private string $instance,
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false
    ) {
        parent::__construct($field, $message, $nullable);
    }

    public function validate($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (!($value instanceof $this->instance)) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => "Campo: {{field}} deve ser uma instância de {$this->instance}.",
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
