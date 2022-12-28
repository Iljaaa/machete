<?php

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\Rule;
use Iljaaa\Machete\rules\validationRules\CallableRule;

/**
 * Test callable validator
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class CallableValidationRuleTest extends \PHPUnit\Framework\TestCase
{


    /**
     *
     **/
    public function testDifferentCallableObject ()
    {
        $c = new class {
            public function testValidationMethod($value, string $attribute, Rule $r): bool {
                return true;
            }
        };

        $result = (new CallableRule([$c, 'testValidationMethod']))->validate(123);
        $this->assertTrue($result, 'new stdClass() is valid string');


        // todo: static call
        // todo: this call

        $result = (new CallableRule(function ($value, string $attribute, Rule $r) {
            return true;
        }))->validate(123);
        $this->assertTrue($result, 'new stdClass() is valid string');

        $result = (new CallableRule(fn ($value, string $attribute, Rule $r) => $r->addError('i sire it\s wrong')->isValid()))->validate(123);
        $this->assertFalse($result, 'new stdClass() is valid string');
    }

    public function testSetAttributeName()
    {
        $result = (new CallableRule(function ($value, string $attribute, Rule $r)  {
            $this->assertEquals('testAttribute', $attribute, 'wrong attribute');
            return true;
        }))->setAttributeName("testAttribute")->validate(123);
        $this->assertTrue($result);
    }

    public function testExceptions ()
    {
        $this->expectException(ValidationException::class);

        $rule = new CallableRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('sASAsAS'), 'wrong result');
    }

    /**
     *
     **/
    public function testDescription ()
    {
        // type
        // hm we have not erro we can set in
//        $rule = new CallableRule('sdfsdfsdfsd');
//        $this->assertFalse($rule->isValid(), 'wrong result');
//        $this->assertFalse($rule->validate('sASAsAS'), 'wrong result');
//        $this->assertFalse($rule->isValid(), 'wrong result');
//        $this->assertEquals("It's not a string", $rule->getFirstError(), 'Wrong first error');
//        $this->assertEquals(['It\'s not a string'], $rule->getErrors(), 'Wrong errors array');

        // override
//        $rule = new CallableRule(['wrongType' => 'wrong type error message']);
//        $this->assertFalse($rule->isValid(), 'wrong result');
//        $this->assertFalse($rule->validate([]), 'wrong result');
//        $this->assertFalse($rule->isValid(), 'wrong result');
//        $this->assertEquals('wrong type error message', $rule->getFirstError(), 'Wrong first error');
//        $this->assertEquals(['wrong type error message'], $rule->getErrors(), 'Wrong errors array');

        // default message
        $rule = (new CallableRule())->setCallableObject(fn() => true);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertTrue($rule->validate('asdasdaskjdbasjhdvkasjdv'), 'wrong result');
        $this->assertTrue($rule->isValid(), 'wrong result');
        $this->assertEquals('', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals([], $rule->getErrors(), 'Wrong errors array');

        // override message
        // $rule = (new CallableRule(fn() => true));
        $rule = new CallableRule(function ($value, string $attribute, Rule $r) {
            $r->addError('test error');
            return false;
        });
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('asdasdaskjdbasjhdvkasjdv'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test error', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test error'], $rule->getErrors(), 'Wrong errors array');

    }


}

