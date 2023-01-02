<?php

namespace Iljaaa\Machete\rules;

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\Validation;

/**
 * Form rule interface
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.2
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
interface Rule
{
    /**
     * Run value validation\
     * @param mixed $value Value for validate
     * @param string|null $attribute If rule used in form for validate attribute here send attribute name
     * @param Validation|null $validation Validator form class instance
     * @return bool
     * @throws ValidationException
     */
    public function validate ($value, ?string $attribute = null, ?Validation $validation = null): bool;

    /**
     * Method called when Validator create collection of instances of validator rules
     *
     * Method mast:
     * - create your rule class instance
     * - parametrize it
     * - and return
     *
     * @param array $config Config array from roles method description
     * @return Rule
     */
    // public static function selfCreateFromValidatorConfig (array $config): Rule;

}
