<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\rules\RulesCollection;
use Iljaaa\Machete\Validation;

/**
 * Strings validation
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.2
 * @package Iljaaa\Machete
 */
class RegexRule extends BasicRule
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
    private string $message = "Value is not valid";

    /**
     * Default error messages
     * @var array|int
     */
    private static array $defaultErrorDescriptions = [
        'message' => ':attribute is not valid',
    ];

    /**
     * @param string|null $regex
     */
    public function __construct (?string $regex = null)
    {
        parent::__construct();

        $this->regex = $regex;
    }

    /**
     * @param string $regex
     * @return RegexRule
     */
    public function setRegex (string $regex): RegexRule
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegex (): ?string
    {
        return $this->regex;
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
     * @return string
     */
    public function getMessage (): string
    {
        return $this->message;
    }

    /**
     * @param array $config
     * @return RegexRule
     * @throws RuleConfigurationException
     */
    public static function selfCreateFromValidatorConfig (array $config): RegexRule
    {
        $attributes = RulesCollection::makeAttributesArrayFromRuleConfig($config);
        assert(!empty($attributes), 'Attribute name is empty, $config[0]');

        $regex = $config['regex'] ?? $config[2] ?? null;
        assert($regex != null, 'Regex not found in config, $config[0]');

        if (empty($regex)) {
            throw new RuleConfigurationException('Regex pattern not set', $config);
        }

        $r = new RegexRule($regex);

        $m = $config['message'] ?? static::$defaultErrorDescriptions['message'];
        $r->setMessage(static::makeFormErrorString($m, [
            ':attribute' => implode(', ', $attributes),
        ]));

        return $r;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        if (empty($this->regex)) {
            throw new ValidationException('Regex pattern is empty');
        }

        // $matches = null;
        // preg_match($pattern, $subject, $matches);
        // $result = !empty($matches);

        // drop default result to true, and clean errors
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
