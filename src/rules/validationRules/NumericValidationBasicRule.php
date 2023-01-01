<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\rules\BasicRule;

/**
 * Base class for int & float rules
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
abstract class NumericValidationBasicRule extends BasicRule
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
    public function setToSmall (string $message): NumericValidationBasicRule
    {
        $this->toSmall = $message;
        return $this;
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
}
