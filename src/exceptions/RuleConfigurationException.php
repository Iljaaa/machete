<?php

namespace Iljaaa\Machete\exceptions;

/**
 * If something wrong with config
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class RuleConfigurationException extends ValidationException
{
    /**
     * Rule form config
     * @var array|null
     */
    private ?array $roleConfig;

    /**
     * @param string $message
     * @param array|null $roleConfig config of role
     */
    public function __construct (string $message, ?array $roleConfig = null)
    {
        parent::__construct($message);
        $this->roleConfig = $roleConfig;
    }

    /**
     * @return array|null
     */
    public function getRoleConfig (): ?array
    {
        return $this->roleConfig;
    }


}
