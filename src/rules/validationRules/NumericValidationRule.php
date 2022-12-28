<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\rules\Rule;

/**
 * Base class for int & float rules
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
abstract class NumericValidationRule extends Rule
{
    /**
     * Basic error messages
     * @var string
     */
    protected string $toSmall = 'To small';
    protected string $toBig = 'To big';



    /**
     * @param string $message
     * @return FloatRule
     */
    public function setToSmall (string $message): NumericValidationRule
    {
        $this->toSmall = $message;
        return $this;
    }

    /**
     * @param string $message
     * @return FloatRule
     */
    public function setToBig (string $message): NumericValidationRule
    {
        $this->toBig = $message;
        return $this;
    }
}
