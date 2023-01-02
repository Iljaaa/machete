<?php

namespace Iljaaa\Machete\rules;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\Validation;

interface UserRule
{

    /**
     * Run value validation\
     * @param mixed $value Value for validate
     * @param string|null $attribute If rule used in form for validate attribute here send attribute name
     * @param UserRuleWrapper|null $userRuleWrapper
     * @param Validation|null $validation Validator form class instance
     * @return bool
     * @throws ValidationException
     */
    public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool;
    // public function validate ($value, ?string $attribute = null, ?Validation $validation = null): bool;


}
