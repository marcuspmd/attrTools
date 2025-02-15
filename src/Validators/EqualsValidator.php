<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Marcuspmd\AttrTools\Validators\BaseValidator;
use Attribute;
use ReflectionClass;
use ReflectionException;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
class EqualsValidator extends BaseValidator implements Validator
{

    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        $valueToCompare = $this->valueToCompare;

        if ($this->fieldToCompare !== null) {
            try {
                $valueToCompare = $this->getFieldValueFromClass();
            } catch (ReflectionException $e) {
                $this->errorCode = 3;
                return false;
            }
        }

        if ($this->type !== null && gettype($value) !== $this->type) {
            $this->errorCode = 2;

            return false;
        }

        if ($value != $valueToCompare) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} não corresponde ao valor esperado.',
            2 => 'Campo: {{field}} deve ser do tipo ' . $this->type . '.',
            3 => 'Campo: ' . $this->fieldToCompare . ' não encontrado na classe.',
            default => 'Campo: {{field}} está inválido.',
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
