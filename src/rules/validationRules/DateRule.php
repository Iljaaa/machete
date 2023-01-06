<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\rules\RulesCollection;
use Iljaaa\Machete\Validation;

/**
 * Date rule its for check string
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/datetime-local#:~:text=MM%2DDD%2DYYYY,%3Amm%20(12%20hour%20clock)
 */
class DateRule extends DatesValidationBasicRule
{
    /**
     * Format
     */
    const FORMAT  = 'Y-m-d';

    /**
     * @return string
     */
    protected function getBaseFormat (): string
    {
        return static::FORMAT;
    }

}
