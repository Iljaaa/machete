<?php

namespace Iljaaa\Machete\rules;


/**
 * Rules collection
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
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
