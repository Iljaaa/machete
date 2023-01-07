<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\RegexRule;

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
     *
     * @throws ValidationException
     */
    public function testOther ()
    {
        // $this->expectException(\Iljaaa\Machete\exceptions\ValidationException::class);
        $result = (new RegexRule('this is not patters'))->validate(new stdClass());
        $this->assertFalse($result, 'its object is valid');

        $c = new class {
            public function __toString () {
                return "strObject";
            }
        };

        $result = (new RegexRule())->setRegex('/^str/')->validate($c);
        $this->assertTrue($result);
    }

    /**
     *
     *
     * @throws ValidationException
     */
    public function testDescription ()
    {
        // type
        $rule = new RegexRule('/^[a-z]+$/');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('123 test test'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals("Value is not valid", $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Value is not valid'], $rule->getErrors());

        // override
        $rule = (new RegexRule('/^[a-z]+$/'))->setMessage('test message for regex test');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('123123'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test message for regex test', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test message for regex test'], $rule->getErrors());

        // set message as param on validator config string
        $rule = RegexRule::selfCreateFromValidatorConfig(['field', 'regex', '/^ilj(a)+$/', 'message' => 'test message for regex validator']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('123123'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test message for regex validator', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test message for regex validator'], $rule->getErrors());

    }

    /**
     * @return void
     * @throws RuleConfigurationException
     */
    public function testCreateFromFormConfig()
    {
        // disable assert
        assert_options(ASSERT_ACTIVE, 0);

        // not twos
        $this->expectException(RuleConfigurationException::class);
        RegexRule::selfCreateFromValidatorConfig(['test', null]);
        RegexRule::selfCreateFromValidatorConfig([['test'], [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        RegexRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        RegexRule::selfCreateFromValidatorConfig(['test', 'test2']);
    }

    /**
     * @return void
     * @throws RuleConfigurationException
     */
    public function testExceptionsOnCreateFromFormConfig()
    {
        // disable assert
        assert_options(ASSERT_ACTIVE, 0);

        // not twos
        $this->expectException(RuleConfigurationException::class);
        RegexRule::selfCreateFromValidatorConfig(['test', [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        RegexRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        RegexRule::selfCreateFromValidatorConfig(['test']);

        $this->expectException(RuleConfigurationException::class);
        RegexRule::selfCreateFromValidatorConfig(['test', 'test222']);
    }

    /**
     * @return void
     * @throws RuleConfigurationException
     */
    public function testAssertsOnCreateFromFormConfig()
    {
        // enable assert
        assert_options(ASSERT_ACTIVE, 1);

        // throws
        $this->expectError();
        // $this->expectNotToPerformAssertions();
        RegexRule::selfCreateFromValidatorConfig([null, fn () => true]);
        RegexRule::selfCreateFromValidatorConfig(['', fn () => true]);
    }

    /**
     * @return void
     * @throws RuleConfigurationException
     * @throws ValidationException
     */
    public function testReplacedParamsInErrorMessages()
    {
        // disable assert
        assert_options(ASSERT_ACTIVE, 0);

        // type
        $rule = RegexRule::selfCreateFromValidatorConfig(['testField', 'regex', '/^sss$/']);
        $this->assertEquals("testField is not valid", $rule->getMessage());
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("testField is not valid", $rule->getFirstError());
        $this->assertEquals(['testField is not valid'], $rule->getErrors());

        $rule = RegexRule::selfCreateFromValidatorConfig(['testField', 'regex', '/^sss$/', 'message' => 'wrong type og :attribute']);
        $this->assertEquals("wrong type og testField", $rule->getMessage());
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("wrong type og testField", $rule->getFirstError());
        $this->assertEquals(['wrong type og testField'], $rule->getErrors());

    }

    /**
     * Test configuration on create from validator config array
     * @throws ValidationException
     */
    public function testExceptionsOnCreateFromValidationArray()
    {
        $result = RegexRule::selfCreateFromValidatorConfig(['field', 'regex', '/^ilj(a)+$/'])->validate('iljaaaaaaaaaaa');
        $this->assertTrue($result);

        $this->expectException(RuleConfigurationException::class);

        RegexRule::selfCreateFromValidatorConfig(['field', 'regex']);
        RegexRule::selfCreateFromValidatorConfig(['field', 'regex', null]);
    }
}
