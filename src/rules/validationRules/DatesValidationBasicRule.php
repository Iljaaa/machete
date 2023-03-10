<?php

namespace Iljaaa\Machete\rules\validationRules;

use DateTime;
use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\rules\RulesCollection;
use Iljaaa\Machete\Validation;

/**
 * Basic class for dates rules
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
abstract class DatesValidationBasicRule extends BasicRule
{
    /**
     * Format for validate
     * @var string
     */
    private string $format;

    /**
     * Minimal date
     * @var DateTime|null
     */
    private ?DateTime $min = null;

    /**
     * Maximum date
     * @var DateTime|null
     */
    private ?DateTime $max = null;

    /**
     * Error messages
     * @var string
     */
    private string $wrongType = 'Value has wrong type';
    private string $wrongFormat = 'Value has wrong format';
    private string $beforeMin = 'Value is before minimal value';
    private string $afterMax = 'Value is after maximal value';

    /**
     * Default error messages
     * @var array|int
     */
    private static array $defaultErrorDescriptions = [
        'wrongType' => ':attribute has wrong type',
        'wrongFormat' => ':attribute has wrong :format',
        'beforeMin' => ':attribute is before :min value',
        'afterMax' => ':attribute is after :max value',
    ];

    /**
     */
    public function __construct ()
    {
        parent::__construct();

        // set format for validate
        $this->format = $this->getBaseFormat();
    }

    /**
     * Base date time format for rule
     * @return string
     */
    protected abstract function getBaseFormat (): string;

    /**
     * @param string $format
     * @return DatesValidationBasicRule
     */
    public function setFormat (string $format): DatesValidationBasicRule
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat (): string
    {
        return $this->format;
    }

    /**
     * @return DateTime|null
     */
    public function getMin (): ?DateTime
    {
        return $this->min;
    }

    /**
     * @param DateTime $min
     * @return DatesValidationBasicRule
     */
    public function setMin (DateTime $min): DatesValidationBasicRule
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param string $min
     * @param string|null $format
     * @return DatesValidationBasicRule
     */
    public function setMinAsString (string $min, ?string $format = null): DatesValidationBasicRule
    {
        $this->min = DateTime::createFromFormat($format ?? $this->getFormat(), $min);
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMax (): ?DateTime
    {
        return $this->max;
    }

    /**
     * @param DateTime $max
     * @return DatesValidationBasicRule
     */
    public function setMax (DateTime $max): DatesValidationBasicRule
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param string $max
     * @param string|null $format
     * @return DatesValidationBasicRule
     */
    public function setMaxAsString (string $max, ?string $format = null): DatesValidationBasicRule
    {
        $this->max = DateTime::createFromFormat($format ?? $this->getFormat(), $max);
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
     * @return DatesValidationBasicRule
     */
    public function setWrongType (string $wrongType): DatesValidationBasicRule
    {
        $this->wrongType = $wrongType;
        return $this;
    }

    /**
     * @return string
     */
    public function getWrongFormat (): string
    {
        return $this->wrongFormat;
    }

    /**
     * @param string $wrongFormat
     * @return DatesValidationBasicRule
     */
    public function setWrongFormat (string $wrongFormat): DatesValidationBasicRule
    {
        $this->wrongFormat = $wrongFormat;
        return $this;
    }

    /**
     * @return string
     */
    public function getBeforeMin (): string
    {
        return $this->beforeMin;
    }

    /**
     * @param string $beforeMin
     * @return DatesValidationBasicRule
     */
    public function setBeforeMin (string $beforeMin): DatesValidationBasicRule
    {
        $this->beforeMin = $beforeMin;
        return $this;
    }

    /**
     * @return string
     */
    public function getAfterMax (): string
    {
        return $this->afterMax;
    }

    /**
     * @param string $afterMax
     * @return DatesValidationBasicRule
     */
    public function setAfterMax (string $afterMax): DatesValidationBasicRule
    {
        $this->afterMax = $afterMax;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        // assert(is_string($value), "Value not be converted to string may be its wrong format: ".get_class($value));
//        if ($value === null){
//            $this->validationResult->addError($this->wrongType);
//            return false;
//        }

        $format = $this->getFormat();

        // if ($value instanceof \DateTime) {
        if (is_object($value) && get_class($value) == "DateTime") {
            $value = $value->format($format);
        }

        // drop default result to true, and clean errors
        $this->validationResult->clearErrorsAndSetValidTrue();

        if (!is_string($value)) {
            // $this->validationResult->addError($this->wrongType." :::: ".((is_object($value))? get_class($value) : gettype($value)));
            $this->validationResult->addError($this->wrongType);
            return false;
        }

        // type to create to date by format
        $date = DateTime::createFromFormat($format, $value);
        if (!$date) {
            $this->validationResult->addError($this->wrongFormat);
            return false;
        }

        // check min max
        if ($this->min && $this->min > $date) {
            $this->validationResult->addError($this->beforeMin);
        }

        if ($this->max && $this->max < $date) {
            $this->validationResult->addError($this->afterMax);
        }

        return $this->validationResult->isValid();
    }


    /**
     * @param array $config
     * @return DatesValidationBasicRule
     * @throws RuleConfigurationException throws when config success all asserts but config still wrong
     */
    public static function selfCreateFromValidatorConfig(array $config): DatesValidationBasicRule
    {
        // assert($config[1], 'DateTimeRule required $config[1] = date || datetime');
        assert(isset($config[1]), 'DateTimeRule required $config[1] = date || datetime');
        assert(is_string($config[1]), 'DateTimeRule required $config[1] = date || datetime');

        $attributes = RulesCollection::makeAttributesArrayFromRuleConfig($config);
        assert(!empty($attributes), 'Attribute name is empty, $config[0]');

        if (empty($attributes)) {
            throw new RuleConfigurationException('Attribute name is empty', $config);
        }

        // check format
        $r = static::roleFactory((string) $config[1]);

        // set format
        if (isset($config['format'])) {
            $r->setFormat($config['format']);
        }

        if (!empty($config['min'])) {
            if (is_string($config['min'])) $r->setMinAsString($config['min']);
            elseif ($config['min'] instanceof \DateTime) $r->setMin($config['min']);
            else throw new RuleConfigurationException('Unexpected type of min', $config);
        }

        if (!empty($config['max'])) {
            if (is_string($config['max'])) $r->setMaxAsString($config['max']);
            elseif ($config['max'] instanceof \DateTime) $r->setMax($config['max']);
            else throw new RuleConfigurationException('Unexpected type of max', $config);
        }

        // set messages

        $m = $config['wrongType'] ?? static::$defaultErrorDescriptions['wrongType'];
        $r->setWrongType(static::makeFormErrorString($m, [
            ':attribute' => implode(', ', $attributes)
        ]));

        $m = $config['wrongFormat'] ?? static::$defaultErrorDescriptions['wrongFormat'];
        $r->setWrongFormat(static::makeFormErrorString($m, [
            ':attribute' => implode(', ', $attributes),
            ':format' => $r->getFormat()
        ]));

        $m = $config['beforeMin'] ?? static::$defaultErrorDescriptions['beforeMin'];
        $min = $r->getMin();
        $r->setBeforeMin(static::makeFormErrorString($m, [
            ':attribute' => implode(', ', $attributes),
            ':min' => $min ? $min->format($r->getFormat()) : ''
        ]));

        $m = $config['afterMax'] ?? static::$defaultErrorDescriptions['afterMax'];
        $max = $r->getMax();
        $r->setAfterMax(static::makeFormErrorString($m, [
            ':attribute' => implode(', ', $attributes),
            ':max' => $max ? $max->format($r->getFormat()) : ''
        ]));

        return $r;
    }

    /**
     * Make rule instance by code
     * @param string $ruleName
     * @return DatesValidationBasicRule
     * @throws RuleConfigurationException
     */
    private static function roleFactory(string $ruleName): DatesValidationBasicRule
    {
        // check format
        switch ($ruleName)
        {
            case 'date': return new DateRule();
            case 'datetime': return new DateTimeRule();
            default: throw new RuleConfigurationException(sprintf('Wrong validator type: %s', $ruleName));
        }
    }

}
