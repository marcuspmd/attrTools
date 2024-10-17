<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;
use ReflectionEnum;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class EnumValidator extends BaseValidator implements Validator
{
    public function __construct(
        private readonly string $enum,
        ?string $field = null,
        ?string $message = null,
        bool $nullable = false,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
        );
    }

    public function isValid($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (!enum_exists($this->enum)) {
            $this->errorCode = 1;
            return false;
        }

        $reflectionEnum = new ReflectionEnum($this->enum);
        if (!$reflectionEnum->isEnum()) {
            $this->errorCode = 1;
            return false;
        }

        $cases = $this->enum::cases();
        foreach ($cases as $case) {
            if ($case->value === $value) {
                return true;
            }
        }

        $this->errorCode = 2;
        return false;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'A classe ' . $this->enum . ' não é um enum válido.',
            2 => 'Campo: {{field}} deve ser um valor válido do enum ' . $this->enum . '.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
