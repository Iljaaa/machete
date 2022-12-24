<?php

namespace Iljaaa\Machete\rules;

/**
 * One rule validation result
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class RuleValidationResult
{
    /**
     * Is field valid
     * yop is default true
     * @var bool
     */
    private bool $isValid = false;

    /**
     * String error description
     * @var string[]
     */
    private array $errors = [];

    /**
     * @param string|null $error
     * @return RuleValidationResult
     */
    public function addError (?string $error): RuleValidationResult
    {
        $this->isValid = false;
        $this->errors[] = $error;
        return $this;
    }

    /**
     * @return void
     */
    public function setIsValid() {
        $this->isValid = true;
    }

    /**
     * @return bool
     */
    public function isValid(): bool {
        return $this->isValid;
    }

    /**
     * @return array
     */
    public function getErrors (): array
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getFirstError () : string
    {
        if (empty($this->errors)) return '';
        return $this->errors[array_key_first($this->errors)];
    }
}
