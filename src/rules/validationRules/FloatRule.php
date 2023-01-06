<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\Validation;

/**
 * Float validation rule
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 2.0.1
 * @package Iljaaa\Machete
 * @see https://www.php.net/manual/en/filter.filters.validate.php
 */
class FloatRule extends NumericValidationBasicRule
{

    public function __construct ()
    {
        parent::__construct();

        $this->setWrongType('It\'s not float');
    }

    /**
     * @param array $config
     * @return FloatRule
     * @throws ValidationException
     */
    public static function selfCreateFromValidatorConfig (array $config): FloatRule
    {
        $r = new static();

        static::updateNumericRule($r, $config);

        return $r;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        if(is_int($value)) $value = (float) $value;

        // if (!is_float($value)){
        if (!filter_var($value, FILTER_VALIDATE_FLOAT)){
            $this->validationResult->addError($this->getWrongType());
            return false;
        }

        // drop to default true value
        $this->validationResult->setIsValid();

        // validate min|max
        $min = $this->getMin();
        if ($min !== null && $min > $value) {
            $this->validationResult->addError($this->getToSmall());
        }

        $max = $this->getMax();
        if ($max !== null && $max < $value) {
            $this->validationResult->addError($this->getToBig());
        }

        return $this->validationResult->isValid();
    }
}
