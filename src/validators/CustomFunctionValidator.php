<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

/**
 * Como usar:
 * Temos 3 formas de usar o CustomFunctionValidator:
 *  #[CustomFunctionValidator(callback: [$this, 'validateCustom'], nullable: true)]
 *  #[CustomFunctionValidator(callback: [self::class, 'validateStatic'], nullable: true)]
 *  #[CustomFunctionValidator(
 *       callback: function($value) {
 *          return strlen($value) > 5; // Exemplo: valida se a string tem mais de 5 caracteres
 *      },
 *      nullable: true
 *  )]
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class CustomFunctionValidator extends BaseValidator implements Validator
{
    private $callback;

    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
        ?callable $callback = null
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
        );
        $this->callback = $callback;
    }

    /**
     * @return bool
     */
    public function isValid($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (!is_callable($this->callback)) {
            $this->errorCode = 3;

            return false;
        }

        $result = call_user_func($this->callback, $value);

        if ($result === false) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} falhou na validação personalizada.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
