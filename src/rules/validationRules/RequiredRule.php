<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\rules\RulesCollection;
use Iljaaa\Machete\Validation;

/**
 * Required validation
 * in base used
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.1.3
 * @package Iljaaa\Machete
 */
class RequiredRule extends BasicRule
{
    /**
     * Basic error messages
     * @var string
     */
    private string $message = "Value required";

    /**
     * Default error messages
     * @var array|int
     */
    private static array $defaultErrorDescriptions = [
        'message' => ':attribute is required',
    ];

    /**
     * @param string $message
     * @return RequiredRule
     */
    public function setMessage (string $message): RequiredRule
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage (): string
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        // drop default result to true, and clean errors
        $this->validationResult->setIsValid();

        // min max length
        if (empty($value))
        {
            $this->validationResult->addError($this->message);
        }

        return $this->validationResult->isValid();
    }

    /**
     * @param array $config
     * @return RequiredRule
     * @throws RuleConfigurationException
     */
    public static function selfCreateFromValidatorConfig(array $config): RequiredRule
    {
        $attributes = RulesCollection::makeAttributesArrayFromRuleConfig($config);
        assert(!empty($attributes), 'Attribute name is empty, $config[0]');

        if (empty($attributes)) {
            throw new RuleConfigurationException('Attribute name is empty', $config);
        }

        $r = new RequiredRule();

        $m = $config['message'] ?? static::$defaultErrorDescriptions['message'];
        $r->setMessage(static::makeFormErrorString($m, [':attribute' => implode(', ', $attributes)]));

        return $r;
    }

}
