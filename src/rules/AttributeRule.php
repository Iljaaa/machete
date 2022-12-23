<?php

namespace Iljaaa\Machete\rules;


/**
 * Merge validation attribute and role
 */
class AttributeRule
{

    /**
     * Class attribute ar field value
     * @var string
     */
    private string $attribute;

    /**
     * Validation rule
     * @var Rule
     */
    private Rule $rule;

    /**
     * @param string $attribute
     * @param Rule $rule
     */
    public function __construct (string $attribute, Rule $rule)
    {
        $this->attribute = $attribute;
        $this->rule = $rule;
    }

    /**
     * @return Rule
     */
    public function getRule (): Rule
    {
        return $this->rule;
    }

    /**
     * @return string
     */
    public function getAttribute (): string
    {
        return $this->attribute;
    }


}
