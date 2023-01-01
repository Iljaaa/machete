<?php

namespace Iljaaa\Machete\exceptions;

use Iljaaa\Machete\rules\Rule;

/**
 * If something wrong with config
 */
class RuleConfigurationException extends ValidationException
{
    /**
     * Ru;e instance
     */
    private ?Rule $rule;

    /**
     * Rule form config
     * @var array|null
     */
    private ?array $roleConfig;

    /**
     * @param string $message
     * @param Rule|null $rule handled role
     * @param array|null $roleConfig config of role
     */
    public function __construct (string $message, ?Rule $rule = null, ?array $roleConfig = null)
    {
        parent::__construct($message);
        $this->rule = $rule;
        $this->roleConfig = $roleConfig;
    }

    /**
     * @return Rule|null
     */
    public function getRule (): ?Rule
    {
        return $this->rule;
    }

    /**
     * @return array|null
     */
    public function getRoleConfig (): ?array
    {
        return $this->roleConfig;
    }


}
