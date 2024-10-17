<?php

namespace Marcuspmd\AttrTools\Validators;

abstract class BaseValidator
{
    public $context;

    public function __construct(
        public ?string $field = null,
        public ?string $message = null,
        public ?bool $nullable = false,
        public ?bool $emptyToNull = false,
        public ?int $errorCode = null,
    ) {
    }

    public function getValue($value)
    {
        if ($this->emptyToNull && empty($value)) {
            $this->nullable = true;
            return null;
        }

        if (is_string($value)) {
            return trim($value);
        }

        if (!is_array($value) && !is_object($value)) {
            return $value;
        }

        if (is_object($value)) {
            $value = json_decode(json_encode($value), true);
        }

        if (strstr($this->field, '.') === false) {
            return $value;
        }

        $multArray = explode('.', $this->field);

        if (count($multArray) == 1) {
            return $value[$this->field];
        }

        foreach ($multArray as $key) {
            if (is_object($value)) {
                $value = (array) $value;
            }
            if (is_array($value)) {
                $value = $value[$key];
            }
        }

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }

    public function getError(): string
    {
        if ($this->message === null) {
            $this->message = $this->setMessage();
        }

        if ($this->message !== null) {
            return $this->parseMessage($this->message);
        }

        return 'Field " ' . $this->field . ' " inválido.';
    }

    public function getErrors(): array
    {
        return [$this->getError()];
    }

    protected function setMessage(): ?string
    {
        return null;
    }

    private function parseMessage($message): string
    {
        $pattern = '/{{(.*?)}}/';
        if (preg_match_all($pattern, $message, $matches) === false) {
            return $message;
        }

        foreach ($matches[1] as $match) {
            $auxMatch = '{{' . $match . '}}';
            $auxValue = $this->{$match} ?? '';
            $message = str_replace($auxMatch, $auxValue, $message);
        }

        return $message;
    }
}
