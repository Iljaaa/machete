<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\Validation;

/**
 * Integer validation rule
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class IntRule extends NumericValidationBasicRule
{
    /**
     * Min and max size of string
     * @var int|null
     */
    protected ?int $min = null, $max = null;

    /**
     * Wrong type error message
     * @var string
     */
    private string $wrongType = "It's not int";

    /**
     * @param array $config
     * @return IntRule
     */
    public static function selfCreateFromValidatorConfig (array $config): IntRule
    {
        $r = new static();

        if (isset($config['min'])) $r->setMin((int) $config['min']);
        if (!empty($config['max'])) $r->setMax((float) $config['max']);

        if (!empty($config['wrongType'])) $r->setWrongType($config['wrongType']);
        if (!empty($config['toSmall'])) $r->setToSmall($config['toSmall']);
        if (!empty($config['toBig'])) $r->setToBig($config['toBig']);

        return $r;
    }

    /**
     * @param float $min
     * @return IntRule
     */
    public function setMin (float $min): IntRule
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param float $max
     * @return IntRule
     */
    public function setMax (float $max): IntRule
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param string $wrongType
     * @return IntRule
     */
    public function setWrongType (string $wrongType): IntRule
    {
        $this->wrongType = $wrongType;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        if (!is_int($value)){
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
