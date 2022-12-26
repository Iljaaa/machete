<?php

namespace Iljaaa\Machete\rules;


use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\CallableRule;

/**
 * Rules collection
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.2.3
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

            foreach ($attributes as $attr)
            {
                // make role validator
                $roleValidator = Rule::makeRuleFromValidatorConfigArray($ruleConfig);

                // if is callable we need additional add field name
                // for the pas it in callback function
                if ($roleValidator instanceof CallableRule) {
                    $roleValidator->setFormFieldName($attr);
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
     * @throws ValidationException
     */
    private static function makeAttributesArrayFromRuleConfig (array $ruleConfig): array
    {
        if (empty($ruleConfig[0])) {
            throw new ValidationException("Rule data have not attribute description");
        }

        $field = $ruleConfig[0];

        if (is_string($field)) return [$field];
        if (is_array($field)) return $field;

        throw new ValidationException("Unknown field description");
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
