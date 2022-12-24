<?php

namespace Iljaaa\Machete\rules;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\CallableRule;
use Iljaaa\Machete\rules\validationRules\RequiredValidationRule;
use Iljaaa\Machete\rules\validationRules\StringValidationRule;

/**
 * Extends for validate one rule
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
abstract class Rule
{
    /**
     * Result of validation
     * @var RuleValidationResult
     */
    protected RuleValidationResult $validationResult;

    /**
     * @param array $config
     */
    public function __construct (array $config = [])
    {
        $this->validationResult = new RuleValidationResult();
    }

    /**
     * Run value validation
     * @param mixed $value
     * @return bool
     */
    public abstract function validate ($value): bool;

    /**
     * Add error and set valid result to false
     * @param string $error
     * @return $this
     */
    public function addError(string $error): Rule
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

    /**
     * Create rule object from array data
     * @param array $ruleConfig
     * @return Rule
     * @throws ValidationException
     */
    public static function makeRuleFromArray (array $ruleConfig): Rule
    {
        // check field name

        // check validator exist
        if (empty($ruleConfig[1])) {
            throw new ValidationException('Validation rule not set');
        }

        $rule = $ruleConfig[1];

        // process string rule
        if (is_string($rule)) {
            $r = static::makeRuleFromString($rule, $ruleConfig);
            if (!$r) {
                throw new ValidationException(sprintf('Validation rule vas not created by string "%s', $rule));
            }
            return $r;
        }

        // callable object
        elseif (is_callable($rule)) {
            return new CallableRule($rule, $ruleConfig);
        }

        throw new ValidationException("Unknown rule format");
    }

    /**
     * @param string $rule
     * @return null|Rule
     */
    private static function makeRuleFromString (string $rule, array $ruleConfig): ?Rule
    {
        switch ($rule) {
            case 'string'   : return new StringValidationRule($ruleConfig);
            case 'required' : return new RequiredValidationRule($ruleConfig);
            case 'number'   : return new RequiredValidationRule($ruleConfig);
        }

        return null;
    }

}
