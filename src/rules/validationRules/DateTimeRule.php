<?php

namespace Iljaaa\Machete\rules\validationRules;

/**
 * DateTime rule it's for check date time string
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/datetime-local#:~:text=MM%2DDD%2DYYYY,%3Amm%20(12%20hour%20clock)
 */
class DateTimeRule extends DatesValidationBasicRule
{

    /**
     * Format
     */
    const FORMAT  = 'Y-m-d H:i'; // :s';

    /**
     * @return string
     */
    protected function getBaseFormat (): string
    {
        return static::FORMAT;
    }

}
