<?php

namespace Iljaaa\Machete\rules;

/**
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
 */
class CallableRule extends Rule
{
    /**
     * @var callable
     */
    private $callableObject;

    /**
     * Form field name for pass in callback
     * @var string
     */
    private string $formFieldName = '';

    /**
     * @param callable $callableObject
     * @param array $config
     */
    public function __construct (callable $callableObject, array $config)
    {
        parent::__construct($config);

        $this->callableObject = $callableObject;
    }

    /**
     * @param string $formFieldName
     */
    public function setFormFieldName (string $formFieldName): void
    {
        $this->formFieldName = $formFieldName;
    }

    /**
     * @param $value
     * @return bool
     */
    public function validate ($value): bool
    {
        if (is_callable($value)) {
            return $this->validationResult->addError('Its not callable')->isValid();
        }

        return call_user_func($this->callableObject, $value, $this->formFieldName, $this);
    }

}
