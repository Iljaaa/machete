<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\Validation;

/**
 * Strings validation
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.2
 * @package Iljaaa\Machete
 */
class StringRule extends BasicRule
{
    /**
     * Min and max size of string
     * @var int|null
     */
    private ?int $min = null, $max = null;

    /**
     * Basic error messages
     * @var string
     */
    private string $wrongType = "It's not a string";
    private string $toShort = 'To short';
    private string $toLong = 'To long';

    /**
     * fixme: refactor it
     * @param array $config
     */
    public function __construct (array $config = [])
    {
        parent::__construct($config);

        if (!empty($config['min'])) $this->min = (int) $config['min'];
        if (!empty($config['max'])) $this->max = (int) $config['max'];

        if (!empty($config['wrongType'])) $this->wrongType = $config['wrongType'];
        if (!empty($config['toShort'])) $this->toShort = $config['toShort'];
        if (!empty($config['toLong'])) $this->toLong = $config['toLong'];
    }

    /**
     * @inheritDoc
     */
    public function validate($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        if (!is_string($value)) {
            $this->validationResult->addError($this->wrongType);
            return false;
        }

        // fixme: not good practice
        // but if we set RuleValidationResult.isValid default true
        // we was wrong return on not valided value
        $this->validationResult->setIsValid();

        // min max length
        if ($this->min != null || $this->max != null)
        {
            $len = mb_strlen($value);

            if ($this->min !== null && $len < $this->min) {
                $this->validationResult->addError($this->toShort);
                $result = false;
            }

            if ($this->max != null && $len > $this->max) {
                $this->validationResult->addError($this->toLong);
            }
        }

        return $this->validationResult->isValid();
    }
}
