<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\rules\RulesCollection;
use Iljaaa\Machete\Validation;

/**
 * Strings validation
 *
 * I think, maybe should try to convert received value to string
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.1.2
 * @package Iljaaa\Machete
 */
class StringRule extends BasicRule
{
    /**
     * Min and max size of string
     * @var int|null
     */
    private ?int $min = null, $max = null;

    /**
     * Basic error messages
     * @var string
     */
    private string $wrongType = "It's not a string";
    private string $toShort = 'To short';
    private string $toLong = 'To long';

    /**
     * @return int|null
     */
    public function getMin (): ?int
    {
        return $this->min;
    }

    /**
     * @param int $min
     * @return StringRule
     */
    public function setMin (int $min): StringRule
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMax (): ?int
    {
        return $this->max;
    }

    /**
     * @param int|null $max
     */
    public function setMax (int $max): StringRule
    {
        $this->max = $max;
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
     * @param string $wrongType
     * @return StringRule
     */
    public function setWrongType (string $wrongType): StringRule
    {
        $this->wrongType = $wrongType;
        return $this;
    }

    /**
     * @return string
     */
    public function getToShort (): string
    {
        return $this->toShort;
    }

    /**
     * @param string $toShort
     * @return StringRule
     */
    public function setToShort (string $toShort): StringRule
    {
        $this->toShort = $toShort;
        return $this;
    }

    /**
     * @return string
     */
    public function getToLong (): string
    {
        return $this->toLong;
    }

    /**
     * @param string $toLong
     * @return StringRule
     */
    public function setToLong (string $toLong): StringRule
    {
        $this->toLong = $toLong;
        return $this;
    }

    /**
     * @param array $config
     * @return StringRule
     * @throws RuleConfigurationException throws when config success all asserts but config still wrong
     * @throws ValidationException
     */
    public static function selfCreateFromValidatorConfig(array $config): StringRule
    {
        $attributes = RulesCollection::makeAttributesArrayFromRuleConfig($config);
        assert($attributes, 'Attribute name is empty, $config[0]');

        if (empty($attributes)) {
            throw new RuleConfigurationException('Attribute name is empty', null, $config);
        }

        $r = new StringRule();

        if (!empty($config['min'])) {
            $r->setMin((int) $config['min']);
        }

        if (!empty($config['max'])) {
            $r->setMax((int) $config['max']);
        }

        if (!empty($config['wrongType'])) {
            $r->setWrongType(static::makeFormErrorString($config['wrongType'], [
                ':attribute' => implode(', ', $attributes)
            ]));
        }

        if (!empty($config['toShort'])) {
            $r->setToShort(static::makeFormErrorString($config['toShort'], [
                ':attribute' => implode(', ', $attributes),
                ':min'       => $r->getMin(),
            ]));
            // $this->toShort = $config['toShort'];
        }

        if (!empty($config['toLong'])) {
            $r->setToLong(static::makeFormErrorString($config['toLong'], [
                ':attribute' => implode(', ', $attributes),
                ':max'       => $r->getMax(),
            ]));
        }

        return $r;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        if (!is_string($value)) {
            $this->validationResult->addError($this->wrongType);
            return false;
        }

        // set return value as true and clear errors
        // its not good practice
        // but if we set RuleValidationResult.isValid default true
        // we was wrong return on not validate value
        $this->validationResult->setIsValid();

        // min max length
        if ($this->min != null || $this->max != null)
        {
            $len = mb_strlen($value);

            if ($this->min !== null && $len < $this->min) {
                $this->validationResult->addError($this->toShort);
            }

            if ($this->max != null && $len > $this->max) {
                $this->validationResult->addError($this->toLong);
            }
        }

        return $this->validationResult->isValid();
    }
}
