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
 * @version 1.0.2
 * @package Iljaaa\Machete
 */
class RequiredRule extends BasicRule
{
    /**
     * Basic error messages
     * @var string
     */
    private string $message = "It's required";
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
        assert($attributes, 'Attribute name is empty, $config[0]');

        $r = new RequiredRule();

        if (!empty($config['message'])) $r->setMessage($config['message']);

        return $r;
    }

}
