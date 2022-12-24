<?php

use Iljaaa\Machete\rules\validationRules\StringValidationRule;

/**
 * Test string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class StringValidationRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     **/
    public function testOther ()
    {
        // $this->expectException(\Iljaaa\Machete\exceptions\ValidationException::class);
        $result = (new StringValidationRule([]))->validate(new stdClass());
        $this->assertFalse($result, 'new stdClass() is valid string');
    }

    /**
     *
     **/
    public function testDescription ()
    {
        // type
        $rule = new StringValidationRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals("It's not a string", $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['It\'s not a string'], $rule->getErrors(), 'Wrong errors array');

        // override
        $rule = new StringValidationRule(['wrongType' => 'wrong type error message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('wrong type error message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['wrong type error message'], $rule->getErrors(), 'Wrong errors array');

        // default message
        $rule = new StringValidationRule(['max' => 10]);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('asdasdaskjdbasjhdvkasjdv'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('To long', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['To long'], $rule->getErrors(), 'Wrong errors array');

        // override message
        $rule = new StringValidationRule(['max' => 10, 'toLong' => 'Test to big message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('asdasdaskjdbasjhdvkasjdv'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Test to big message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Test to big message'], $rule->getErrors(), 'Wrong errors array');

        // short

        // default
        $rule = new StringValidationRule(['min' => 10]);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('test'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('To short', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['To short'], $rule->getErrors(), 'Wrong errors array');

        // override
        $rule = new StringValidationRule(['min' => 10, 'toShort' => 'Test short message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('test'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Test short message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Test short message'], $rule->getErrors(), 'Wrong errors array');
    }


    /**
     *
     **/
    public function testMinMax ()
    {
        $result = (new StringValidationRule(['max' => 10]))->validate(new stdClass());

        $this->assertIsBool($result, 'new stdClass() is valid string');
    }

}
