<?php

namespace Iljaaa\Machete\rules;


/**
 * Rules collection
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.1.2
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
