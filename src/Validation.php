<?php

namespace Iljaaa\Machete;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\AttributeRule;
use Iljaaa\Machete\rules\RulesCollection;

/**
 * Its Validation class
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.1.5
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
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

    /**
     * Is vas validate() method calls
     * @var bool
     */
    private bool $isVasValidate = false;

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
     * Is vas validate() method calls
     * @return bool
     */
    public function isVasValidated(): bool
    {
        return $this->isVasValidate;
    }

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
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setValue (string $key, $value)
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
     * @return array
     */
    public function getData (): array
    {
        return $this->data;
    }

    /**
     * Getting value wrap
     * @param string $name
     * @return mixed
     */
    public function getValue (string $name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        return $this->data[$name] ?? null;
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
     * @param $name
     * @return void
     */
    public function __get($name)
    {
        return $this->getValue($name);
    }

    /**
     * Validate data
     *
     * fields array for validate
     * like ['phone', 'name', 'email']
     * @param null|string[] $attributesForValidate
     *
     * @return bool
     * @throws ValidationException
     */
    public function validate(?array $attributesForValidate = null) : bool
    {
        // drop result to true
        $this->result->clearBeforeValidate();

        // wrap rules in objects
        $rulesCollection = RulesCollection::makeRulesCollection($this->rules());

        // check all filds in $attributesForValidate has roles
        if ($attributesForValidate !== null)
        {
            // collect attributes by rules
            // for check existing role for all attributes in $attributesForValidate
            $attributesByRoles = $rulesCollection->getAttributesList();

            // filter attributes without role
            $attributesWithoutRules = array_filter($attributesForValidate, fn ($a) => !in_array($a, $attributesByRoles));
            if (!empty($attributesWithoutRules)) {
                throw new ValidationException(sprintf('Attribute (%s) with out rules', implode(', ', $attributesWithoutRules)));
            }
        }

        /** @var AttributeRule[] $rule */
        foreach ($rulesCollection as $aRule)
        {
            $attribute = $aRule->getAttribute();
            if ($attributesForValidate !== null) {
                if (!in_array($attribute, $attributesForValidate)) {
                    continue;
                }
            }

            // check is field is need validate
            $value = $this->getValue($attribute);

            $rule = $aRule->getRule();

            // run validaton
            if (!$rule->validate($value, $attribute, $this)) {
                $this->result->setIsValid(false);
            }

        }

        // sava validated rules in result
        $this->result->setRulesCollection($rulesCollection);

        // mark as finish
        $this->isVasValidate = true;

        return $this->result->isValid();
    }

    /**
     * Return array of error messages grouped by field
     * @return array
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


}
