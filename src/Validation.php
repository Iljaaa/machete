<?php

namespace Iljaaa\Machete;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\AttributeRule;
use Iljaaa\Machete\rules\Rule;
use Iljaaa\Machete\rules\RulesCollection;

/**
 * Its validation
 *
 * file for extend
 * class MySuperValidator extents
 *
 * @version 0.0.3
 */
abstract class Validation
{
    /**
     * Shadow data, used if field not found in class
     * @var array
     */
    protected array $data = [];

    /**
     * @var array
     */
    private $errors = [];

    /**
     * Is data valid
     * @var bool
     */
    private bool $isValid = false;

    /**
     * Rules array
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Is data valid
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * Load validation data
     * @param array $data
     * @return void
     */
    public function load (array $data)
    {
        foreach ($data as $key => $value) {
            $this->setValue($key, $value);
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->setValue($name, $value);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private function setValue (string $key, $value)
    {
        // check is key form attribute
        if (property_exists($this, $key)) {
            $this->{$key} = $value;
            return;
        }

        // put it in data
        $this->data[$key] = $value;
    }

    /**
     * @param $name
     * @return void
     */
    public function __get($name)
    {
        return $this->getValue($name);
    }

    /**
     * Getting value wrap
     * @param $name
     * @return mixed
     */
    private function getValue ($name)
    {
        return $this->{$name};
    }

    /**
     * Validate data
     *
     * fields array for validate
     * like ['phone', 'name', 'email']
     * @param string[] $fieldsForValidate
     *
     * @return bool
     * @throws ValidationException
     */
    public function validate(array $fieldsForValidate = []) : bool
    {
        $this->isValid = true;

        // wrap rules in objects
        $rulesCollection = static::makeRulesCollection($this->rules());

        /** @var AttributeRule[] $rule */
        foreach ($rulesCollection as $aRule)
        {
            // check is field is need validate
            $value = $this->getValue($aRule->getAttribute());

            // validation one field
            $result = $aRule->getRule()->validate($value);

            if (!$result) {
                $this->isValid = false;
            }

        }

        return $this->isValid;
    }

    /**
     * Make rules collection for validate
     * @param array $rules
     * @return RulesCollection
     * @throws ValidationException
     */
    private static function makeRulesCollection (array $rules): RulesCollection
    {
        $collection = new RulesCollection();

        // fill rules collection
        foreach ($rules as $ruleConfig)
        {
            // make attributes array
            $attributes = static::makeFieldsArrayFromRuleConfig($ruleConfig);

            // make role validator
            $roleValidator = Rule::makeRuleFromArray($ruleConfig);

            foreach ($attributes as $a) {
                $collection->add(new AttributeRule($a, $roleValidator));
            }


        }

        return $collection;
    }

    /**
     * Make fields array from difrend types
     * @param array $ruleConfig
     * @return void
     * @throws ValidationException
     */
    private static function makeFieldsArrayFromRuleConfig (array $ruleConfig): array
    {
        if (empty($ruleConfig[0])) {
            throw new ValidationException("Rule data have not attribute description");
        }

        $field = $ruleConfig[0];

        if (is_string($field)) return [$field];
        if (is_array($field)) return $field;

        throw new ValidationException("Unknown field description");
    }

    /**
     * @param string $field filed name for validate
     * @param \Rule $rule
     * @return ValidationResult
     */
    private function validateField(string $field,  $rule)
    {
        $validatator = $rule[1];

        $value = $this->getValue($field);

        // check is validator is method of class
        if (method_exists($this, $validatator)) {
            $result = call_user_func([$this, $validatator], $value, $field, $rule);
            return (new ValidationResult())->setResult($result);
        }

        // parse short name validators
        $class = $this->getAssociateClass($validatator);

        // manual load class
        if (!class_exists($validatator, false)) {
            $a = 3;
        }

        /** @var Validation $v */
        $v = new $class;
        return $v->validate($field, $value, $rule);
    }

    /**
     * @param string $field
     * @param string $error
     * @return void
     */
    public function addError ($field, $error)
    {
        if (!isset($this->errors[$field])) $this->errors[$field] = [];
        $this->errors[$field][] = $error;
    }

    /**
     * @return array
     */
    public function getErrors (){
        return $this->errors;
    }

    /**
     * @return void|null
     */
    public function getFirstError ()
    {
        if (empty($this->errors)) return null;
        $errors = $this->errors[array_key_first($this->errors)];
        if (empty($errors)) return null;
        return $errors[array_key_first($errors)];
    }

    /**
     * @param string $field
     * @return array
     */
    public function getErrorsForField ($field) {
        return (!empty($this->errors[$field])) ? $this->errors[$field] : [];
    }

    /**
     * @param $field
     * @return void
     */
    public function getFirstErrorForField ($field){
        if (empty($this->errors[$field])) return null;
        return $this->errors[$field][array_key_first($this->errors[$field])];
    }

}

class ValidationResult
{
    /**
     * @var bool
     */
    public $result = false;
    /**
     * @var string
     */
    public $error = '';

    /**
     * @param bool
     * @return ValidationResult
     */
    public function setResult ($result) {
        $this->result = $result;
        return $this;
    }

    /**
     * @param string
     * @return ValidationResult
     */
    public function setError ($error) {
        $this->error = $error;
        return $this;
    }
}
