<?php

namespace Iljaaa\Machete\rules;

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\Validation;

/**
 * Wrapper for user role
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class UserRuleWrapper extends BasicRule
{
    /**
     * User rule
     * @var UserRule|null
     */
    private ?UserRule $userRule;

    /**
     *
     */
    public function __construct (?UserRule $userRule = null)
    {
        parent::__construct();

        $this->userRule = $userRule;
    }

    /**
     * @inheritDoc
     */
    public function validate ($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        assert($this->userRule !== null, 'you forgot $this->userRole');

        if (!$this->userRule){
            throw new ValidationException('User role not sent');
        }

        //  call user validator
        $result = $this->userRule->validate($value, $attribute, $this, $validation);
        $this->validationResult->setIsValid($result);

        return $result;
    }

    /**
     * @param array $config
     * @return UserRuleWrapper
     * @throws RuleConfigurationException
     */
    public static function selfCreateFromValidatorConfig (array $config): UserRuleWrapper
    {
        assert(!empty($config[0]), 'Attribute name empty');

        $r = $config['rule'] ?? $config[2] ?? null;

        /**
         * Instance of user role
         */
        $userRoleInstance = null;

        if ($r instanceof UserRule) {
            $userRoleInstance = $r;
        }
        elseif (is_string($r) && class_exists($r)) {
            $userRoleInstance = new $r($config);
        }

        if (!$userRoleInstance) {
            throw new RuleConfigurationException('User rule instance vas not crated', null, $config);
        }


        return new UserRuleWrapper($userRoleInstance);
    }

}
