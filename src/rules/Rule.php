<?php

namespace Iljaaa\Machete\rules;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\ValidationResult;

/**
 * Extends for validate one rule
 * @version 0.0.1
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
     * @param bool $stopOnFirst stop on first found error
     * @return bool
     */
    public abstract function validate ($value): bool;

    /**
     * @return bool
     */
    public function isValid(): bool {
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
            case 'required' : return new RequiredValidation($ruleConfig);
            case 'number'   : return new RequiredValidation($ruleConfig);
        }

        return null;
    }

}
