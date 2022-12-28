<?php

namespace Iljaaa\Machete\rules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\CallableRule;
use Iljaaa\Machete\rules\validationRules\FloatRule;
use Iljaaa\Machete\rules\validationRules\IntRule;
use Iljaaa\Machete\rules\validationRules\InValidationRule;
use Iljaaa\Machete\rules\validationRules\RegexRule;
use Iljaaa\Machete\rules\validationRules\RequiredRule;
use Iljaaa\Machete\rules\validationRules\StringRule;

/**
 * Extends for validate one rule
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.2
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
     * Constructor create a result object
     */
    public function __construct ()
    {
        $this->validationResult = new RuleValidationResult();
    }

    /**
     * Run value validation
     * @param mixed $value
     * @return bool
     * @throws ValidationException
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
     * Create rule object from array getted from validation::roles
     *
     *
     * @param array $ruleConfig
     * @return Rule
     * @throws RuleConfigurationException
     */
    public static function makeRuleFromValidatorConfigArray (array $ruleConfig): Rule
    {
        // check field name

        // check validator exist
        if (empty($ruleConfig[1])) {
            throw new RuleConfigurationException('Validation rule not set');
        }

        $rule = $ruleConfig[1];

        // process string rule
        if (is_string($rule)) {
            $r = static::makeRuleFromString($rule, $ruleConfig);
            if (!$r) {
                throw new RuleConfigurationException(sprintf('Validation rule vas not created by string "%s"', $rule));
            }
            return $r;
        }

        // callable object
        elseif (is_callable($rule)) {
            return new CallableRule($rule, $ruleConfig);
        }

        throw new RuleConfigurationException("Unknown rule format");
    }

    /**
     * @param string $rule
     * @param array $ruleConfig
     * @return null|Rule
     * @throws RuleConfigurationException
     */
    private static function makeRuleFromString (string $rule, array $ruleConfig): ?Rule
    {
        switch ($rule) {
            case 'string'   : return new StringRule($ruleConfig);
            case 'required' : return RequiredRule::selfCreateFromValidatorConfig($ruleConfig);
            case 'int'      : return IntRule::selfCreateFromValidatorConfig($ruleConfig);
            case 'float'    : return FloatRule::selfCreateFromValidatorConfig($ruleConfig);
            // case 'number'   : return new RequiredValidationRule($ruleConfig);
            case 'in'       : return InValidationRule::selfCreateFromValidatorConfig($ruleConfig);
            case 'regex'    : return RegexRule::selfCreateFromValidatorConfig($ruleConfig);
        }

        return null;
    }

}
