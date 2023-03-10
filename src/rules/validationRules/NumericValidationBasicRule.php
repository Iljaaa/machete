<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\rules\RulesCollection;

/**
 * Base class for int & float rules
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.1.2
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
abstract class NumericValidationBasicRule extends BasicRule
{
    /**
     * Min and max size of string
     * @var float|null
     */
    private ?float $min = null, $max = null;

    /**
     * Error messages
     * @var string
     */
    private string $wrongType = 'Value has wrong type';
    private string $toSmall = 'Value to small';
    private string $toBig = 'Value to big';

    /**
     * Default error messages
     * @var array|int
     */
    private static array $defaultErrorDescriptions = [
        'wrongType' => ':attribute has wrong type',
        'toSmall' => ':attribute to small, min length :min',
        'toBig' => ':attribute to big, max length :max',
    ];

    /**
     * @param float $min
     * @return IntRule
     */
    public function setMin (float $min): NumericValidationBasicRule
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMin (): ?float
    {
        return $this->min;
    }

    /**
     * @param float $max
     * @return IntRule
     */
    public function setMax (float $max): NumericValidationBasicRule
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getMax (): ?float
    {
        return $this->max;
    }

    /**
     * @param string $message
     * @return NumericValidationBasicRule
     */
    public function setToSmall (string $message): NumericValidationBasicRule
    {
        $this->toSmall = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getToSmall (): string
    {
        return $this->toSmall;
    }

    /**
     * @param string $message
     * @return FloatRule
     */
    public function setToBig (string $message): NumericValidationBasicRule
    {
        $this->toBig = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getToBig (): string
    {
        return $this->toBig;
    }


    /**
     * @param string $wrongType
     * @return IntRule
     */
    public function setWrongType (string $wrongType): NumericValidationBasicRule
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
     * @param NumericValidationBasicRule $rule
     * @param array $config
     * @return void
     * @throws RuleConfigurationException
     */
    public static function updateNumericRule  (NumericValidationBasicRule $rule, array $config): void
    {
        $attributes = RulesCollection::makeAttributesArrayFromRuleConfig($config);
        assert(!empty($attributes), 'Attribute name is empty, $config[0]');

        if (empty($attributes)) {
            throw new RuleConfigurationException('Attribute name is empty', $config);
        }

        if (isset($config['min'])) {
            $rule->setMin((float) $config['min']);
        }
        if (isset($config['max'])) {
            $rule->setMax((float) $config['max']);
        }

        $m = $config['wrongType'] ?? static::$defaultErrorDescriptions['wrongType'];
        $rule->setWrongType(static::makeFormErrorString($m, [
            ':attribute' => implode(', ', $attributes),
        ]));


        $m = $config['toSmall'] ?? static::$defaultErrorDescriptions['toSmall'];
        $rule->setToSmall(static::makeFormErrorString($m, [
            ':attribute' => implode(', ', $attributes),
            ':min'       => $rule->getMin(),
        ]));

        $m = $config['toBig'] ?? static::$defaultErrorDescriptions['toBig'];
        $rule->setToBig(static::makeFormErrorString($m, [
            ':attribute' => implode(', ', $attributes),
            ':max'       => $rule->getMax(),
        ]));
    }

    /**
     * Validate min max values
     * @param $value
     * @return void
     */
    protected function validateMinMax($value)
    {
        // validate min|max
        $min = $this->getMin();
        if ($min !== null && $min > $value) {
            $this->validationResult->addError($this->getToSmall());
        }

        $max = $this->getMax();
        if ($max !== null && $max < $value) {
            $this->validationResult->addError($this->getToBig());
        }
    }
}
