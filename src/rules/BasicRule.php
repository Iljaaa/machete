<?php

namespace Iljaaa\Machete\rules;

/**
 * Basic class for all provided rules
 * Implement basic methods
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.3
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
abstract class BasicRule implements Rule
{
    /**
     * Result of validation
     * @var RuleValidationResult
     */
    protected RuleValidationResult $validationResult;

    /**
     * Constructor create a result object
     */
    public function __construct ()
    {
        $this->validationResult = new RuleValidationResult();
    }

    /**
     * Add error and set valid result to false
     * @param string $error
     * @return $this
     */
    public function addError(string $error): BasicRule
    {
        $this->validationResult->addError($error);
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->validationResult->isValid();
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->validationResult->getErrors();
    }

    /**
     * @return string
     */
    public function getFirstError(): string
    {
        return $this->validationResult->getFirstError();
    }

}
