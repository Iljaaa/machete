<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\rules\Rule;

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
     * @return FloatValidationRule
     */
    public function setToSmall (string $message): NumericValidationRule
    {
        $this->toSmall = $message;
        return $this;
    }

    /**
     * @param string $message
     * @return FloatValidationRule
     */
    public function setToBig (string $message): NumericValidationRule
    {
        $this->toBig = $message;
        return $this;
    }
}
