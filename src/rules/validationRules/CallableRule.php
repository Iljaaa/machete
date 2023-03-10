<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\rules\RulesCollection;
use Iljaaa\Machete\Validation;

/**
 * Callable form rule
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.3
 * @package Iljaaa\Machete
 */
class CallableRule extends BasicRule
{
    /**
     * @var callable
     */
    private $callableObject;

    /**
     * Form field name for pass in callback
     * @var string
     */
    private string $wrongType = "Object is not callable";

    /**
     * Default error messages
     * @var array|int
     */
    private static array $defaultErrorDescriptions = [
        'wrongType' => ':attribute was checked by not callable object',
    ];

    /**
     * Form attribute name
     * When form validate method called
     * it call a callable object with parameters one of them it's an attribute name
     * @var string
     */
    private string $attributeName = '';

    /**
     * @param callable|null $callableObject
     */
    public function __construct (?callable $callableObject = null)
    {
        parent::__construct();

        $this->callableObject = $callableObject;
    }

    /**
     * @param callable|null $callableObject
     * @return CallableRule
     */
    public function setCallable (callable $callableObject): CallableRule
    {
        $this->callableObject = $callableObject;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getCallableObject (): ?callable
    {
        return $this->callableObject;
    }

    /**
     * @param string $attributeName
     * @return CallableRule
     */
    public function setAttributeName (string $attributeName): CallableRule
    {
        $this->attributeName = $attributeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAttributeName (): string
    {
        return $this->attributeName;
    }

    /**
     * @param string $wrongType
     * @return CallableRule
     */
    public function setWrongType (string $wrongType): CallableRule
    {
        $this->wrongType = $wrongType;
        return $this;
    }

    /**
     * @return string
     */
    public function getWrongType (): string
    {
        return $this->wrongType;
    }

    /**
     * @inheritDoc
     */
    public function validate ($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        if (empty($this->callableObject)){
            throw new ValidationException('Callable object not set');
        }

        if (!is_callable($this->callableObject)) {
            return $this->validationResult->addError($this->wrongType)->isValid();
        }

        // drop to default rue result
        $this->validationResult->clearErrorsAndSetValidTrue();

        $r = call_user_func($this->callableObject, $value, $this->attributeName, $this);

        if (!is_bool($r)) {
            throw new ValidationException('Validation function must return bool');
        }

        return $r;
    }

    /**
     * @param array $config
     * @return CallableRule
     * @throws RuleConfigurationException
     */
    public static function selfCreateFromValidatorConfig (array $config): CallableRule
    {
        assert(isset($config[1]), 'Callable object is empty, $config[1]');

        $attributes = RulesCollection::makeAttributesArrayFromRuleConfig($config);
        assert(!empty($attributes), 'Attribute name is empty, $config[0]');

        if (empty($attributes)) {
            throw new RuleConfigurationException('Attribute name is empty', $config);
        }

        if (empty($config[1])) {
            throw new RuleConfigurationException('Callable parameter empty', $config);
        }

        // check callable object
        $callableObject = $config[1];
        if (!is_callable($callableObject)){
            throw new RuleConfigurationException('Object is not callable', $config);
        }

        $attributeAsString = implode(', ', $attributes);

        $r = new CallableRule($callableObject);
        $r->setAttributeName($attributeAsString);

        $m = $config['wrongType'] ?? static::$defaultErrorDescriptions['wrongType'];
        $r->setWrongType(static::makeFormErrorString($m, [
            ':attribute' => $attributeAsString,
        ]));

        return $r;
    }

}
