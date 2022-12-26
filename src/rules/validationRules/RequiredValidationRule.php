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
     * @return RequiredValidationRule
     */
    public static function selfCreateFromValidatorConfig(array $config): RequiredValidationRule
    {
        $r = new RequiredValidationRule();

        if (!empty($config['message'])) $r->setMessage($config['message']);

        return $r;
    }

    /**
     * @param string $message
     * @return RequiredValidationRule
     */
    public function setMessage (string $message): RequiredValidationRule
    {
        $this->message = $message;
        return $this;
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
