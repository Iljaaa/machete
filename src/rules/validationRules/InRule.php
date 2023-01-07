<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\rules\RulesCollection;
use Iljaaa\Machete\Validation;
use Traversable;

/**
 * Strings validation
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 */
class InRule extends BasicRule
{
    /**
     * Basic error messages
     * @var string
     */
    private string $message = 'Not in array';

    /**
     * Haystack to check
     * is  array in description, but am not sure
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
        parent::__construct();

        $this->haystack = $haystack;
    }
    /**
     * Message setter
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): InRule
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param bool $strict
     * @return InRule
     */
    public function setStrict (bool $strict): InRule
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     * @param array|Traversable $haystack
     * @return InRule
     */
    public function setHaystack ($haystack): InRule
    {
        $this->haystack = $haystack;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        assert($this->haystack, 'Haystack name is empty, use ->setHaystack()');

        if ($this->haystack === null){
            throw new ValidationException('Haystack for validate not set');
        }

        return $this->inArray($value, $this->haystack, $this->strict);
    }

    /**
     * @param array $config
     * @return InRule
     * @throws RuleConfigurationException
     */
    public static function selfCreateFromValidatorConfig(array $config): InRule
    {
        $attributes = RulesCollection::makeAttributesArrayFromRuleConfig($config);
        assert($attributes, 'Attribute name is empty, $config[0]');

        $haystack = $config['haystack'] ?? $config[2] ?? null;
        assert($haystack != null, 'Haystack not found in config, $config[1]');

        if (empty($haystack)) {
            throw new RuleConfigurationException('Haystack is empty');
        }

        $r = new InRule($haystack);

        if (!empty($config['message'])) $r->setMessage($config['message']);
        if (!empty($config['strict'])) $r->setStrict((bool) $config['strict']);

        return $r;
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
        // drop default result to true, and clean errors
        $this->validationResult->setIsValid();

        if (!in_array($needle, $haystack, $strict)) {
            $this->validationResult->addError($this->message);
            return false;
        }

        return true;
    }
}
