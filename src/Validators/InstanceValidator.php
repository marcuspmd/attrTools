<?php
namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

/**
 * Como usar:
 * #[InstanceValidator(instance: NomeDaClasse::class)]
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class InstanceValidator extends BaseValidator implements Validator
{

    public function isValid($value): bool
    {
        if (!class_exists($this->instance)) {
            throw new \InvalidArgumentException("A classe {$this->instance} não existe.");
        }

        $value = $this->getValue($value);

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
