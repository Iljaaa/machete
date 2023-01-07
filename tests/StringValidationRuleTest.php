<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\StringRule;

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
     * Try to validate of wrong type data
     * @throws ValidationException
     */
    public function testType ()
    {
        $rule = (new StringRule())->setMax(10);
        $result = $rule->validate(new stdClass());

        $this->assertIsBool($result, 'new stdClass() is valid string');
        $this->assertFalse($result);

        $rule = (new StringRule())->setMax(10);
        $result = $rule->validate(['123']);

        $this->assertIsBool($result);
        $this->assertFalse($result);
    }

    /**
     * Test min max string length validation
     * @throws ValidationException
     */
    public function testMinMax ()
    {
        $rule = (new StringRule())->setMax(9);
        $result = $rule->validate("1234567890");
        $this->assertIsBool($result, 'new stdClass() is valid string');
        $this->assertFalse($result);
        $this->assertTrue($rule->validate("123"));

        $rule = (new StringRule())->setMin(11);
        $result = $rule->validate("1234567890");
        $this->assertIsBool($result, 'new stdClass() is valid string');
        $this->assertFalse($result);
        $this->assertTrue($rule->validate("12345678901234567890"));
    }

    /**
     * @throws ValidationException
     */
    public function testDescription ()
    {
        // type
        $rule = new StringRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals("Value is not a string", $rule->getFirstError());
        $this->assertEquals(['Value is not a string'], $rule->getErrors(), 'Wrong errors array');

        // override
        $rule = (new StringRule())->setWrongType( 'wrong type error message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('wrong type error message', $rule->getFirstError());
        $this->assertEquals(['wrong type error message'], $rule->getErrors(), 'Wrong errors array');

        // default message
        $rule = (new StringRule())->setMax(10);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('it is very long string'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Value to long', $rule->getFirstError());
        $this->assertEquals(['Value to long'], $rule->getErrors(), 'Wrong errors array');

        // override message
        $rule = (new StringRule())->setMax(10)->setToLong('Test to big message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('it is very long string'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Test to big message', $rule->getFirstError());
        $this->assertEquals(['Test to big message'], $rule->getErrors(), 'Wrong errors array');

        // short

        // default
        $rule = (new StringRule())->setMin(10);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('test'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Value to short', $rule->getFirstError());
        $this->assertEquals(['Value to short'], $rule->getErrors(), 'Wrong errors array');

        // override
        $rule = (new StringRule())->setMin(10)->setToShort('Test short message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('test'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Test short message', $rule->getFirstError());
        $this->assertEquals(['Test short message'], $rule->getErrors(), 'Wrong errors array');
    }

    /**
     * @return void
     * @throws RuleConfigurationException
     * @throws ValidationException
     */
    public function testCreateFromFormConfig()
    {
        // disable assert
        assert_options(ASSERT_ACTIVE, 0);

        // not twos
        $this->expectException(RuleConfigurationException::class);
        StringRule::selfCreateFromValidatorConfig(['test', null]);
        StringRule::selfCreateFromValidatorConfig([['test'], [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        StringRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        StringRule::selfCreateFromValidatorConfig(['test', 'test2']);
    }

    /**
     * @return void
     * @throws RuleConfigurationException
     * @throws ValidationException
     */
    public function testExceptionsOnCreateFromFormConfig()
    {
        // disable assert
        assert_options(ASSERT_ACTIVE, 0);

        // not twos
        $this->expectException(RuleConfigurationException::class);
        StringRule::selfCreateFromValidatorConfig(['test', [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        StringRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        StringRule::selfCreateFromValidatorConfig(['test']);

        $this->expectException(RuleConfigurationException::class);
        StringRule::selfCreateFromValidatorConfig(['test', 'test222']);
    }

    /**
     * @return void
     * @throws RuleConfigurationException
     * @throws ValidationException
     */
    public function testAssertsOnCreateFromFormConfig()
    {
        // enable assert
        assert_options(ASSERT_ACTIVE, 1);

        // throws
        $this->expectError();
        // $this->expectNotToPerformAssertions();
        StringRule::selfCreateFromValidatorConfig([null, fn () => true]);
        StringRule::selfCreateFromValidatorConfig(['', fn () => true]);
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
        $rule = StringRule::selfCreateFromValidatorConfig(['testField', 'string']);
        $this->assertEquals("testField has wrong type", $rule->getWrongType());
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("testField has wrong type", $rule->getFirstError());
        $this->assertEquals(['testField has wrong type'], $rule->getErrors(), 'Wrong errors array');

        $rule = StringRule::selfCreateFromValidatorConfig(['testField', 'string', 'wrongType' => 'wrong type og :attribute']);
        $this->assertEquals("wrong type og testField", $rule->getWrongType());
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("wrong type og testField", $rule->getFirstError());
        $this->assertEquals(['wrong type og testField'], $rule->getErrors(), 'Wrong errors array');

        // short
        $rule = StringRule::selfCreateFromValidatorConfig(['testField', 'string', "min" => 5]);
        $this->assertEquals("testField to short, min length 5", $rule->getToShort());
        $result = $rule->validate('123');
        $this->assertFalse($result);
        $this->assertEquals("testField to short, min length 5", $rule->getFirstError());
        $this->assertEquals(['testField to short, min length 5'], $rule->getErrors());

        $rule = StringRule::selfCreateFromValidatorConfig(['testField', 'string', "min" => 5, 'toShort' => ':attribute min :min chars length']);
        $this->assertEquals("testField min 5 chars length", $rule->getToShort());
        $result = $rule->validate('123');
        $this->assertFalse($result);
        $this->assertEquals("testField min 5 chars length", $rule->getFirstError());
        $this->assertEquals(['testField min 5 chars length'], $rule->getErrors());

        // long
        $rule = StringRule::selfCreateFromValidatorConfig(['testField', 'string', 'max' => 2]);
        $this->assertEquals("testField to long, max length 2", $rule->getToLong());
        $result = $rule->validate("123");
        $this->assertFalse($result);
        $this->assertEquals("testField to long, max length 2", $rule->getFirstError());
        $this->assertEquals(['testField to long, max length 2'], $rule->getErrors());

        $rule = StringRule::selfCreateFromValidatorConfig(['testField', 'string', 'max' => 2, 'toLong' => ':attribute max :max chars length']);
        $this->assertEquals("testField max 2 chars length", $rule->getToLong());
        $result = $rule->validate("123");
        $this->assertFalse($result);
        $this->assertEquals("testField max 2 chars length", $rule->getFirstError());
        $this->assertEquals(['testField max 2 chars length'], $rule->getErrors());

    }


    /**
     *
     *
     * @throws ValidationException
     */
    public function testOther ()
    {
        $result = (new StringRule())->validate(new stdClass());
        $this->assertFalse($result, 'new stdClass() is valid string');
    }
}
