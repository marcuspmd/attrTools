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
    public function __construct(
        private string $instance,
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        ?bool $emptyToNull = false,
    ) {
        if (!class_exists($this->instance)) {
            throw new \InvalidArgumentException("A classe {$this->instance} não existe.");
        }

        parent::__construct(
            field: $field,
            message: $message ?? "Campo: {{field}} deve ser uma instância de {$this->instance}.",
            nullable: $nullable,
        );
    }

    public function isValid($value): bool
    {
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
