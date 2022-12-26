<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\rules\validationRules\RegexValidationRule;
use Iljaaa\Machete\rules\validationRules\StringValidationRule;

/**
 * Regex string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class RegexValidationRuleTest extends \PHPUnit\Framework\TestCase
{

    /**
     *
     **/
    public function testOther ()
    {
        // $this->expectException(\Iljaaa\Machete\exceptions\ValidationException::class);
        $result = (new RegexValidationRule('this is not patters'))->validate(new stdClass());
        $this->assertFalse($result, 'its object is valid');

        $c = new class {
            public function __toString () {
                return "strObject";
            }
        };

        $result = (new RegexValidationRule())->setRegex('/^str/')->validate($c);
        $this->assertTrue($result);
    }

    /**
     *
     **/
    public function testDescription ()
    {
        // type
        $rule = new RegexValidationRule('/^[a-z]+$/');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('123sdsadas'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals("Not match", $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Not match'], $rule->getErrors(), 'Wrong errors array');

        // override
        $rule = (new RegexValidationRule('/^[a-z]+$/'))->setMessage('test message for regex test');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('123123'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test message for regex test', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test message for regex test'], $rule->getErrors(), 'Wrong errors array');

        // set message as param on validator config string
        $rule = RegexValidationRule::selfCreateFromValidatorConfig(['field', 'regex', '/^ilj(a)+$/', 'message' => 'test message for regex validator']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('123123'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test message for regex validator', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test message for regex validator'], $rule->getErrors(), 'Wrong errors array');

    }

    /**
     * Test configuretion on create from validator config array
     * @throws \Iljaaa\Machete\exceptions\ValidationException
     */
    public function testExceptionsOnCreateFromValidationArray()
    {
        $result = RegexValidationRule::selfCreateFromValidatorConfig(['field', 'regex', '/^ilj(a)+$/'])->validate('iljaaaaaaaaaaa');
        $this->assertTrue($result);

        $this->expectException(RuleConfigurationException::class);

        RegexValidationRule::selfCreateFromValidatorConfig(['field', 'regex']);
        RegexValidationRule::selfCreateFromValidatorConfig(['field', 'regex', null]);
    }
}
