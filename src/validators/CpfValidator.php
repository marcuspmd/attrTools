<?php

namespace Marcuspmd\AttrTools\Validators;

use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class CpfValidator extends BaseValidator implements Validator
{
    public function __construct(
        ?string $field = null,
        ?string $message = null,
        ?bool $nullable = false,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
        );
    }

    /**
     * @param string $value
     * @return bool
     */
    public function isValid($value): bool
    {
        if ($this->nullable && $value === null) {
            return true;
        }

        if (!$this->validateCpf($value)) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    private function validateCpf($cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        $invalidCpfs = [
            '00000000000',
            '11111111111',
            '22222222222',
            '33333333333',
            '44444444444',
            '55555555555',
            '66666666666',
            '77777777777',
            '88888888888',
            '99999999999',
        ];

        if (in_array($cpf, $invalidCpfs)) {
            return false;
        }

        for ($i = 9; $i < 11; $i++) {
            $sum = 0;
            for ($j = 0; $j < $i; $j++) {
                $sum += $cpf[$j] * (($i + 1) - $j);
            }
            $rest = $sum % 11;
            $digit = ($rest < 2) ? 0 : 11 - $rest;
            if ($digit != $cpf[$i]) {
                return false;
            }
        }

        return true;
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'Campo: {{field}} é um CPF inválido.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
