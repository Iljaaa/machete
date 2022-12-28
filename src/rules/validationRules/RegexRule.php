<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\Rule;

/**
 * Strings validation
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 */
class RegexRule extends Rule
{
    /**
     * Regex for math
     * @var string|null
     */
    private ?string $regex;

    /**
     * Error messages
     * @var string
     */
    private string $message = "Not match";

    /**
     * @param string|null $regex
     */
    public function __construct (?string $regex = null)
    {
        parent::__construct();

        $this->regex = $regex;
    }

    /**
     * @param string|null $regex
     * @return RegexRule
     */
    public function setRegex (?string $regex): RegexRule
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * @param string $message
     * @return RegexRule
     */
    public function setMessage (string $message): RegexRule
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param array $config
     * @return RegexRule
     * @throws RuleConfigurationException
     */
    public static function selfCreateFromValidatorConfig (array $config): RegexRule
    {
        $regex = $config['regex'] ?? $config[2] ?? null;
        if (empty($regex)) {
            throw new RuleConfigurationException('Regex pattern not set', null, $config);
        }

        $r = new RegexRule($regex);
        if (!empty($config['message'])) $r->setMessage($config['message']);
        return $r;
    }

    /**
     * @param $value
     * @return bool
     * @throws ValidationException
     */
    public function validate($value): bool
    {
        if (empty($this->regex)) {
            throw new ValidationException('Regex pattern is empty');
        }

        // $matches = null;
        // preg_match($pattern, $subject, $matches);
        // $result = !empty($matches);

        // drop result to bool
        $this->validationResult->setIsValid();

        if ($this->isMatch($this->regex, $value) == false) {
            $this->validationResult->addError($this->message);
            return false;
        }

        return true;
    }

    /**
     * @param string $regex
     * @param mixed $value
     * @return bool
     */
    public function isMatch(string $regex, $value): bool
    {
        // $options = ["regexp" => $regex];
        return filter_var($value, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => $regex]]);
    }
}
