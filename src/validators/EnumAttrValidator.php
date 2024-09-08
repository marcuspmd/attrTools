<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;
use ReflectionClass;

/**
 * Como usar:
 * #[EnumAttrValidator(enum: AccountingTypeEnum::class, nullable: false)]
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class EnumAttrValidator extends BaseValidator implements Validator
{
    public function __construct(
        private readonly string $enum,
        ?string $field = null,
        ?string $message = null,
        bool $nullable = false,
    ) {
        parent::__construct($field, $message, $nullable);
    }

    public function validate($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        $constants = (new ReflectionClass($this->enum))->getConstants();

        if (!in_array($value, $constants, true)) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} deve ser um valor válido do enum '.$this->enum.'.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
