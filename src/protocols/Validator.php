<?php

namespace Marcuspmd\AttrTools\Protocols;

interface Validator
{
    public function isValid($value): bool;

    public function getError(): string;

    public function getErrors(): array;
}
