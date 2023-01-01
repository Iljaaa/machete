<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\BasicRule;
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
    private string $wrongType = "Current object is not callable object";

    /**
     * Form attribute name
     * when we call a validate method
     * it call a callable object with parameters one of them its a attribute name
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
     * @param string $attributeName
     * @return CallableRule
     */
    public function setAttributeName (string $attributeName): CallableRule
    {
        $this->attributeName = $attributeName;
        return $this;
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
     * @param callable|null $callableObject
     * @return CallableRule
     */
    public function setCallable (callable $callableObject): CallableRule
    {
        $this->callableObject = $callableObject;
        return $this;
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
        $this->validationResult->setIsValid();

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
        // $a =
        // assert(empty($config[0]), 'Attribute name is empty. '.print_r($config[0], true));
        assert($config[0], 'Attribute name is empty, $config[0]');

        /*if (empty($config[0])) {
            throw new RuleConfigurationException('Attribute parameter empty', null, $config);
        }*/

        if (empty($config[1])) {
            throw new RuleConfigurationException('Callable parameter empty', null, $config);
        }

        // check callable object
        $callableObject = $config[1];
        if (!is_callable($callableObject)){
            throw new RuleConfigurationException('Object is not callable', null, $config);
        }

        return new CallableRule($callableObject);
        // if (!empty($config['message'])) $r->setMessage($config['message']);
        // if (!empty($config['message'])) $r->setMessage($config['message']);
    }

}
