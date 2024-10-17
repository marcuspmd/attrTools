<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;
use ReflectionClass;
use ReflectionException;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class IsNotEqualsValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        ?bool $emptyToNull = false,
        private $valueToCompare = null,
        private readonly ?string $fieldToCompare = null
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
            emptyToNull: $emptyToNull
        );
    }

    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        if ($this->fieldToCompare !== null) {
            try {
                $this->valueToCompare = $this->getFieldValueFromClass();
            } catch (ReflectionException $e) {
                $this->errorCode = 1;
                return false;
            }
        }

        return $value != $this->valueToCompare;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: ' . $this->fieldToCompare . ' não encontrado na classe.',
            default => 'Campo: {{field}} deve ser diferente de ' . $this->valueToCompare . '.',
        };
    }

    private function getFieldValueFromClass(): mixed
    {
        $object = $this->context;

        $reflection = new ReflectionClass($object);

        $property = $reflection->getProperty($this->fieldToCompare);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
