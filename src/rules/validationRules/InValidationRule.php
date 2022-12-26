<?php

namespace Iljaaa\Machete\rules\validationRules;

use http\Message;
use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\Rule;

/**
 * Strings validation
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 */
class InValidationRule extends Rule
{
    /**
     * Basic error messages
     * @var string
     */
    private string $message = 'Not in array';

    /**
     * Haystack to check
     * it's array in description, but am not shure
     * @var array|null
     */
    private ?array $haystack = null;

    /**
     * @var bool
     */
    private bool $strict = false;

    /**
     * @param array|null $haystack
     */
    public function __construct (?array $haystack = null)
    {
        parent::__construct([]);

        $this->haystack = $haystack;
    }

    /**
     * @param array $config
     * @return InValidationRule
     * @throws RuleConfigurationException
     */
    public static function selfCreateFromValidatorConfig(array $config): InValidationRule
    {
        $haystack = $config['haystack'] ?? $config[2] ?? null;

        if (empty($haystack)) {
            throw new RuleConfigurationException('Haystack is empty');
        }

        $r = new InValidationRule($haystack);

        if (!empty($config['message'])) $r->setMessage($config['message']);
        if (!empty($config['strict'])) $r->setStrict((bool) $config['strict']);

        return $r;
    }

    /**
     * Message setter
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): InValidationRule
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param bool $strict
     * @return InValidationRule
     */
    public function setStrict (bool $strict): InValidationRule
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     * @param array $haystack
     * @return InValidationRule
     */
    public function setHaystack (array $haystack): InValidationRule
    {
        $this->haystack = $haystack;
        return $this;
    }

    /**
     * @param mixed $value
     * @return bool
     * @throws ValidationException
     */
    public function validate($value): bool
    {
        if ($this->haystack === null){
            throw new ValidationException('Haystack for validate not set');
        }

        return $this->inArray($value, $this->haystack, $this->strict);
    }

    /**
     * @see https://www.php.net/manual/en/function.in-array.php
     * @param mixed $needle
     * @param mixed $haystack
     * @param bool $strict
     * @return bool
     */
    public function inArray($needle, $haystack, bool $strict = false): bool
    {
        // set result as true
        $this->validationResult->setIsValid();

        if (!in_array($needle, $haystack, $strict)) {
            $this->validationResult->addError($this->message);
            return false;
        }

        return true;
    }
}
