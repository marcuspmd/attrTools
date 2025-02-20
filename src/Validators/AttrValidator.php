<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use ReflectionClass;
use ReflectionProperty;
use Exception;

final class AttrValidator implements Validator
{
    public array $errors = [];

    public function isValid($target): bool
    {
        $className = get_class($target);
        $reflectionClass = new ReflectionClass($className);
        $properties = $reflectionClass->getProperties();

        $result = $this->validateProperties($properties, $className, $target);

        return $result;
    }

    public function getError(): string
    {
        return implode(', ', $this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateProperties(array $properties, string $className, object $target): bool
    {
        foreach ($properties as $property) {
            $propertyName = $property->getName();

            $reflectionProperty = new ReflectionProperty($className, $propertyName);
            $reflectionProperty->setAccessible(true);
            $attributes = $reflectionProperty->getAttributes();
            foreach ($attributes as $attribute) {
                try {
                    $validationAttribute = $attribute->newInstance();

                    if (!$validationAttribute instanceof BaseValidator) {
                        continue;
                    }

                    $arguments = $attribute->getArguments();

                    if (!array_key_exists('field', $arguments)) {
                        $validationAttribute->field = $propertyName;
                    }

                    if (property_exists($validationAttribute, 'context')) {
                        $validationAttribute->context = $target;
                    }

                    if ($validationAttribute->__call('isValid', [$reflectionProperty->getValue($target)]) == false) {
                        $this->errors[] = $validationAttribute->getError();
                    }

                } catch (Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        }
        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }
}
