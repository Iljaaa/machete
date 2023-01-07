<?php

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\FloatRule;

/**
 * Test string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.3
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class FloatValidationRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Try to validate of wrong type data
     * @throws ValidationException
     */
    public function testType ()
    {
        $rule = (new FloatRule())->setMax(10);
        $result = $rule->validate(new stdClass());

        $this->assertIsBool($result, 'new stdClass() is valid string');
        $this->assertFalse($result);

        $rule = (new FloatRule())->setMax(10);
        $result = $rule->validate(['123']);

        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    /**
     * Check min max values
     * @throws ValidationException
     */
    public function testMinMax ()
    {
        //
        $result = (new FloatRule())->setMax(10)->validate(9);
        $this->assertTrue($result);

        $result = (new FloatRule())->setMax(10)->validate(11);
        $this->assertFalse($result);

        $result = (new FloatRule())->setMin(10)->validate(11);
        $this->assertTrue($result);

        $result = (new FloatRule())->setMin(10)->validate(9);
        $this->assertFalse($result);
    }

    /**
     *
     **/
    public function testOther ()
    {
        // $this->expectException(\Iljaaa\Machete\exceptions\ValidationException::class);
        $result = (new FloatRule())->validate(null);
        $this->assertFalse($result, 'new stdClass() is valid string');
    }
}
