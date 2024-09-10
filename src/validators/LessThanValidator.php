<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;
use ReflectionClass;
use ReflectionException;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class LessThanValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        private $valueToCompare = null,
        private readonly ?string $fieldToCompare = null
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

        if ($this->fieldToCompare !== null) {
            try {
                $this->valueToCompare = $this->getFieldValueFromClass();
            } catch (ReflectionException $e) {
                $this->errorCode = 1;
                return false;
            }
        }

        if (is_numeric($this->valueToCompare)) {
            $this->valueToCompare = (float) $this->valueToCompare;
            $value = (float) $value;
        }

        if (is_array($this->valueToCompare)) {
            $this->valueToCompare = count($this->valueToCompare);
            $value = count($value);
        }

        if (is_string($this->valueToCompare)) {
            $this->valueToCompare = strlen($this->valueToCompare);
            $value = strlen($value);
        }

        return $value < $this->valueToCompare;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: '.$this->fieldToCompare.' não encontrado na classe.',
            default => 'Campo: {{field}} deve ser menor que '.$this->valueToCompare.'.',
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
