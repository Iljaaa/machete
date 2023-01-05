<?php

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\FloatRule;

/**
 * Test string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.2
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
    public function testDefaultErrorMessages ()
    {
        // type
        $rule = new FloatRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('sadfsdfdsf'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals("It's not float", $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['It\'s not float'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new FloatRule())->setWrongType('wrong type message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('wrong type message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['wrong type message'], $rule->getErrors(), 'Wrong errors array');

        // min
        $rule = (new FloatRule())->setMin(10);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(5), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('To small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['To small'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new FloatRule())->setMin(10)->setToSmall("test to small");
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(5), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test to small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test to small'], $rule->getErrors(), 'Wrong errors array');

        // max
        $rule = (new FloatRule())->setMax(5);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(15), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('To big', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['To big'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new FloatRule())->setMax(5)->setToBig("test to small");
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(15), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test to small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test to small'], $rule->getErrors(), 'Wrong errors array');

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
