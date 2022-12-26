<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\rules\Rule;

class IntValidationRule extends NumericValidationRule
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
     * @return IntValidationRule
     */
    public static function selfCreateFromValidatorConfig (array $config): IntValidationRule
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
     * @return IntValidationRule
     */
    public function setMin (float $min): IntValidationRule
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param float $max
     * @return IntValidationRule
     */
    public function setMax (float $max): IntValidationRule
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param string $wrongType
     * @return IntValidationRule
     */
    public function setWrongType (string $wrongType): IntValidationRule
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
