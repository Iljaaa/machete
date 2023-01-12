<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\Validation;

/**
 * Integer validation rule
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 * @see https://www.php.net/manual/en/filter.filters.validate.php
 */
class IntRule extends NumericValidationBasicRule
{

    public function __construct ()
    {
        parent::__construct();

        $this->setWrongType('It\'s not int');
    }

    /**
     * @param array $config
     * @return IntRule
     * @throws ValidationException
     */
    public static function selfCreateFromValidatorConfig (array $config): IntRule
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
        // if (!is_int($value)){
        if (!filter_var($value, FILTER_VALIDATE_INT)){
            $this->validationResult->addError($this->getWrongType());
            return false;
        }

        // drop default result to true, and clean errors
        $this->validationResult->clearErrorsAndSetValidTrue();

        // min max
        $this->validateMinMax($value);

        return $this->validationResult->isValid();
    }
}
