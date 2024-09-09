<?php

namespace Marcuspmd\AttrTools\Validators;


use Attribute;
use Marcuspmd\AttrTools\Protocols\Validator;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class CnpjValidator extends BaseValidator implements Validator
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

        if (!$this->validadeCnpj($value)) {
            $this->errorCode = 1;

            return false;
        }

        return true;
    }

    private function validadeCnpj($cnpj): bool
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        $invalidCpfs = [
            '00000000000000',
            '11111111111111',
            '22222222222222',
            '33333333333333',
            '44444444444444',
            '55555555555555',
            '66666666666666',
            '77777777777777',
            '88888888888888',
            '99999999999999',
        ];

        if (in_array($cnpj, $invalidCpfs)) {
            return false;
        }

        $sum = 0;
        $length = strlen($cnpj) - 2;

        for ($i = 0, $j = 5; $i < $length; $i++, $j--) {
            if ($j < 2) {
                $j = 9;
            }

            $sum += $cnpj[$i] * $j;
        }

        $rest = $sum % 11;

        if ($cnpj[$length] != ($rest < 2 ? 0 : 11 - $rest)) {
            return false;
        }

        $sum = 0;
        $length++;

        for ($i = 0, $j = 6; $i < $length; $i++, $j--) {
            if ($j < 2) {
                $j = 9;
            }

            $sum += $cnpj[$i] * $j;
        }

        $rest = $sum % 11;

        return $cnpj[$length] == ($rest < 2 ? 0 : 11 - $rest);
    }

    protected function setMessage(): string
    {
        return match ($this->errorCode) {
            1 => 'CNPJ inválido.',
            default => 'Campo: {{field}} está inválido.',
        };
    }
}
