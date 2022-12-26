<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\rules\Rule;

/**
 * Strings validation
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 */
class FloatValidationRule extends NumericValidationRule
{
    /**
     * Min and max size of string
     * @var float|null
     */
    protected ?float $min = null, $max = null;

    /**
     * Wrong type error message
     * @var string
     */
    private string $wrongType = "It's not int";

    /**
     * @param array $config
     * @return FloatValidationRule
     */
    public static function selfCreateFromValidatorConfig (array $config): FloatValidationRule
    {
        $r = new static();

        if (isset($config['min'])) $r->setMin((float) $config['min']);
        if (!empty($config['max'])) $r->setMax((float) $config['max']);

        if (!empty($config['wrongType'])) $r->setWrongType($config['wrongType']);
        if (!empty($config['toSmall'])) $r->setToSmall($config['toSmall']);
        if (!empty($config['toBig'])) $r->setToBig($config['toBig']);

        return $r;
    }

    /**
     * @param float $min
     * @return Rule
     */
    public function setMin (float $min): Rule
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param float $max
     * @return Rule
     */
    public function setMax (float $max): Rule
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param string $wrongType
     * @return Rule
     */
    public function setWrongType (string $wrongType): Rule
    {
        $this->wrongType = $wrongType;
        return $this;
    }

    /**
     * @param $value
     * @return bool
     */
    public function validate($value): bool
    {
        if(is_int($value)) $value = (float) $value;

        if (!is_float($value)){
            $this->validationResult->addError($this->wrongType);
            return false;
        }

        // drop to default true value
        $this->validationResult->setIsValid();

        // min max length
        if ($this->min != null || $this->max != null)
        {
            if ($this->min !== null && $value < $this->min) {
                $this->validationResult->addError($this->toSmall);
            }

            if ($this->max != null && $value > $this->max) {
                $this->validationResult->addError($this->toBig);
            }
        }

        return $this->validationResult->isValid();
    }
}
