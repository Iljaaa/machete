<?php

namespace Iljaaa\Machete;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\AttributeRule;
use Iljaaa\Machete\rules\Rule;
use Iljaaa\Machete\rules\RulesCollection;
use Iljaaa\Machete\rules\validationRules\CallableRule;

/**
 * Its validation
 *
 * file for extend
 * class MySuperValidator extents
 *
 * @version 1.1.3
 */
abstract class Validation
{
    /**
     * Shadow data, used if field not found in class
     * @var array
     */
    protected array $data = [];

    /**
     * @var ValidationResult
     */
    private ValidationResult $result;

    public function __construct ()
    {
        $this->result = new ValidationResult();
    }

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
        return $this->result->isValid();
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
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        return $this->data[$name] ?? null;
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
        // drop result to true
        $this->result->clearBeforeValidate();

        // wrap rules in objects
        $rulesCollection = static::makeRulesCollection($this->rules());

        /** @var AttributeRule[] $rule */
        foreach ($rulesCollection as $aRule)
        {
            // check is field is need validate
            $value = $this->getValue($aRule->getAttribute());

            // validation one field
            if ($aRule->getRule()->validate($value) == false) {
                $this->result->setIsValid(false);
            }
        }

        // sava validated rules in result
        $this->result->setRulesCollection($rulesCollection);

        return $this->result->isValid();
    }

    /**
     * Return array of error messages grouped by field
     * @return void
     */
    public function getErrors(): array
    {
        return $this->result->getErrors();
    }

    /**
     * Get first error
     * @return string
     */
    public function getFirstError(): string
    {
        return $this->result->getFirstError();
    }

    /**
     * Get errors array fro attribute
     * @param string $attribute
     * @return array
     */
    public function getErrorsForAttribute(string $attribute): array
    {
        return $this->result->getErrorsForAttribute($attribute);
    }

    /**
     * @param string $attribute
     * @return string
     */
    public function getFirstErrorForAttribute (string $attribute): string
    {
        return $this->result->getFirstErrorForAttribute($attribute);
    }

    /**
     * @param string $attribute
     * @return bool
     */
    public function isAttributeValid (string $attribute): bool
    {
        return $this->result->isAttributeValid($attribute);
    }

    /**
     * Make rules collection for validate
     * todo: move to collection
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
            $attributes = static::makeAttributesArrayFromRuleConfig($ruleConfig);

            foreach ($attributes as $attr)
            {
                // make role validator
                $roleValidator = Rule::makeRuleFromArray($ruleConfig);

                // if is callable we need additional add field name
                // for the pas it in callback function
                if ($roleValidator instanceof CallableRule) {
                    $roleValidator->setFormFieldName($attr);
                }

                //
                $collection->add(new AttributeRule($attr, $roleValidator));
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
    private static function makeAttributesArrayFromRuleConfig (array $ruleConfig): array
    {
        if (empty($ruleConfig[0])) {
            throw new ValidationException("Rule data have not attribute description");
        }

        $field = $ruleConfig[0];

        if (is_string($field)) return [$field];
        if (is_array($field)) return $field;

        throw new ValidationException("Unknown field description");
    }

}
