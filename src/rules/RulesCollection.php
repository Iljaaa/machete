<?php

namespace Iljaaa\Machete\rules;


use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\CallableRule;
use Iljaaa\Machete\rules\validationRules\FloatRule;
use Iljaaa\Machete\rules\validationRules\InRule;
use Iljaaa\Machete\rules\validationRules\IntRule;
use Iljaaa\Machete\rules\validationRules\RegexRule;
use Iljaaa\Machete\rules\validationRules\RequiredRule;
use Iljaaa\Machete\rules\validationRules\StringRule;

/**
 * Rules collection
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.3.5
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class RulesCollection implements \Iterator
{
    /**
     * Inner rules array
     * @var AttributeRule[]
     */
    private array $rules = [];

    /**
     * Make rules collection for validate
     * @param array $rules
     * @return RulesCollection
     * @throws ValidationException
     */
    public static function makeRulesCollection (array $rules): RulesCollection
    {
        $collection = new RulesCollection();

        // fill rules collection
        foreach ($rules as $ruleConfig)
        {
            // make attributes array
            $attributes = static::makeAttributesArrayFromRuleConfig($ruleConfig);

            //
            if (empty($attributes)) {
                throw new ValidationException("Rule data have not attribute description");
            }

            foreach ($attributes as $attr)
            {
                // make role validator
                $roleValidator = static::makeRuleFromValidatorConfigArray($ruleConfig);

                // if is callable we need additional add field name
                // for the pas it in callback function
                if ($roleValidator instanceof CallableRule) {
                    $roleValidator->setAttributeName($attr);
                }

                //
                $collection->add(new AttributeRule($attr, $roleValidator));
            }
        }

        return $collection;
    }

    /**
     * Make fields array from difrend types
     * @param array $ruleConfig
     * @return array
     * @throws RuleConfigurationException
     */
    public static function makeAttributesArrayFromRuleConfig (array $ruleConfig): array
    {
        if (empty($ruleConfig[0])) {
            return [];
        }

        $field = $ruleConfig[0];

        if (is_string($field)) return [$field];
        if (is_array($field)) return $field;

        throw new RuleConfigurationException("Unknown field description");
    }

    /**
     * Create rule object from array getted from validation::roles
     *
     * @param array $ruleConfig
     * @return Rule
     * @throws RuleConfigurationException
     */
    public static function makeRuleFromValidatorConfigArray (array $ruleConfig): Rule
    {
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
            return CallableRule::selfCreateFromValidatorConfig($ruleConfig);
        }

        throw new RuleConfigurationException("Unknown rule format");
    }

    /**
     * @param string $rule
     * @param array $ruleConfig
     * @return Rule
     * @throws RuleConfigurationException
     */
    private static function makeRuleFromString (string $rule, array $ruleConfig): ?Rule
    {
        switch ($rule) {
            case 'string'   : return StringRule::selfCreateFromValidatorConfig($ruleConfig);
            case 'required' : return RequiredRule::selfCreateFromValidatorConfig($ruleConfig);
            case 'int'      : return IntRule::selfCreateFromValidatorConfig($ruleConfig);
            case 'float'    : return FloatRule::selfCreateFromValidatorConfig($ruleConfig);
            // case 'number'   : return new RequiredValidationRule($ruleConfig);
            case 'in'       : return InRule::selfCreateFromValidatorConfig($ruleConfig);
            case 'regex'    : return RegexRule::selfCreateFromValidatorConfig($ruleConfig);
            case 'rule'     : return UserRuleWrapper::selfCreateFromValidatorConfig($ruleConfig);
        }

        return null;
    }

    /**
     * Add rule to collection
     * @param AttributeRule $r
     * @return RulesCollection
     */
    public function add(AttributeRule $r): RulesCollection
    {
        $this->rules[] = $r;
        return $this;
    }

    /**
     * List of all attributes, collected by rules
     * @return array
     */
    public function getAttributesList(): array
    {
        if (empty($this->rules)) return [];

        $attributes = [];
        foreach ($this->rules as $r) {
            $roleAttribute = $r->getAttribute();
            if (!in_array($roleAttribute, $attributes)) {
                $attributes[] = $roleAttribute;
            }
        }

        return $attributes;
    }

    /**
     * Collect and return all errors from rules
     * @return array
     */
    public function getErrors(): array
    {
        $result = [];

        foreach ($this->rules as $r)
        {
            $ruleErrors = $r->getRule()->getErrors();

            if (empty($ruleErrors)) {
                continue;
            }

            $attr = $r->getAttribute();
            if (!isset($result[$attr])) $result[$attr] = [];

            $result[$attr] = array_merge($result[$attr], $ruleErrors);
        }

        return $result;
    }

    /**
     * Collect and return all errors from rules for one attribute
     * @param string $attribute
     * @return array
     */
    public function getErrorsForAttribute(string $attribute): array
    {
        $result = [];

        foreach ($this->rules as $r)
        {
            $errors = $r->getRule()->getErrors();

            if (empty($errors)) {
                continue;
            }

            $ruleAttr = $r->getAttribute();
            if ($ruleAttr != $attribute){
                continue;
            }

            $result = array_merge($result, $errors);
        }

        return $result;
    }

    /**
     * Find and return first error in rules
     * @return string
     */
    public function getFirstError(): string
    {
        foreach ($this->rules as $r)
        {
            $e = $r->getRule()->getFirstError();
            if (!empty($e)) return $e;
        }

        return '';
    }

    /**
     * Find and return first error in rules
     * @param string $attribute
     * @return string
     */
    public function getFirstErrorForAttribute(string $attribute): string
    {
        foreach ($this->rules as $r)
        {
            $ruleAttr = $r->getAttribute();
            if ($ruleAttr != $attribute){
                continue;
            }

            $e = $r->getRule()->getFirstError();
            if (!empty($e)) return $e;
        }

        return '';
    }

    /**
     * Check all rules and math the valid attribute
     * @param string $attribute
     * @return bool
     */
    public function isAttributeValid (string $attribute): bool
    {
        if (empty($this->rules)) {
            return false;
        }

        foreach ($this->rules as $r)
        {
            $ruleAttr = $r->getAttribute();
            if ($ruleAttr != $attribute){
                continue;
            }

            if ($r->getRule()->isValid() == false){
                return false;
            }
        }

        return true;
    }

    /**
     * @return AttributeRule|null
     */
    function rewind(): ?AttributeRule
    {
        return reset($this->rules);
    }

    /**
     * @return AttributeRule
     */
    function current(): AttributeRule
    {
        return current($this->rules);
    }

    /**
     * @return int|string|null
     */
    function key()
    {
        return key($this->rules);
    }

    /**
     * @return false|AttributeRule
     */
    function next()
    {
        return next($this->rules);
    }

    /**
     * @return bool
     */
    function valid(): bool
    {
        return key($this->rules) !== null;
    }
}
