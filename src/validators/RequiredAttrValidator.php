<?php

namespace Marcuspmd\AttrTools\Validators;


use Marcuspmd\AttrTools\Protocols\Validator;
use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_PROPERTY)]
final class RequiredAttrValidator extends BaseValidator implements Validator
{
    public function __construct(
        bool $nullable = false,
        bool $emptyToNull = false,
        ?string $field = null,
        ?string $message = null,
    ) {
        parent::__construct(
            field: $field,
            message: $message,
            nullable: $nullable,
            emptyToNull: $emptyToNull
        );
    }

    /**
     * @param string|array|null $value
     * @return bool
     */
    public function validate($value): bool
    {
        $value = $this->getValue($value);

        if ($this->nullable && $value === null) {
            return true;
        }

        return !empty($value);
    }

    protected function setMessage(): string
    {
        return 'Campo {{field}} é obrigatório.';
    }
}
