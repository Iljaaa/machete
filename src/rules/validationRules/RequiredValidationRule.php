<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\rules\Rule;

/**
 * Required validation
 * in base used
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
 */
class RequiredValidationRule extends Rule
{
    /**
     * Basic error messages
     * @var string
     */
    private string $message = "It's required";

    /**
     * @param array $config
     */
    public function __construct (array $config = [])
    {
        parent::__construct($config);

        if (!empty($config['message'])) $this->message = $config['message'];
    }


    /**
     * @param $value
     * @return bool
     */
    public function validate($value): bool
    {
        // fixme: not good practice
        // but if we set RuleValidationResult.isValid default true
        // we was wrong return on not valided value
        $this->validationResult->setIsValid();

        // min max length
        if (empty($value))
        {
            $this->validationResult->addError($this->message);
        }

        return $this->validationResult->isValid();
    }
}
