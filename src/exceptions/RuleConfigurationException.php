<?php

namespace Iljaaa\Machete\exceptions;

/**
 * If something wrong with config
 */
class RuleConfigurationException extends ValidationException
{
    private array $roleConfig;
}
