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
    /**
     * @return bool
     */
    public function isValid($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        if (!is_callable($this->callback)) {
            $this->errorCode = 3;

            return false;
        }

        // Context é o valor da classe, incluindo todas propriedades e métodos
        $context = $this->context ?? $value;
        $result = call_user_func($this->callback, $context);

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
