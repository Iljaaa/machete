<?php

namespace Iljaaa\Machete\rules\validationRules;

use Iljaaa\Machete\rules\BasicRule;
use Iljaaa\Machete\Validation;

class UserValidationRoleWrapper extends BasicRule
{
    /**
     * @inheritDoc
     */
    public function validate ($value, ?string $attribute = null, ?Validation $validation = null): bool
    {
        return true;
    }

}
