<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\IntRule;

/**
 * Test string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.2
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class IntValidationRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Try to validate of wrong type data
     * @throws ValidationException
     */
    public function testType ()
    {
        $rule = (new IntRule())->setMax(10);
        $result = $rule->validate(new stdClass());

        $this->assertIsBool($result, 'new stdClass() is valid string');
        $this->assertFalse($result);

        $rule = (new IntRule())->setMax(10);
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
        $result = (new IntRule())->setMax(10)->validate(9);
        $this->assertTrue($result);

        $result = (new IntRule())->setMax(10)->validate(11);
        $this->assertFalse($result);

        $result = (new IntRule())->setMin(10)->validate(11);
        $this->assertTrue($result);

        $result = (new IntRule())->setMin(10)->validate(9);
        $this->assertFalse($result);
    }

    /**
     *
     *
     * @throws ValidationException
     */
    public function testDefaultErrorMessages ()
    {
        // type defuult
        $rule = new IntRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('sadfsdfdsf'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals("It's not int", $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['It\'s not int'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new IntRule())->setWrongType('wrong type message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('wrong type message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['wrong type message'], $rule->getErrors(), 'Wrong errors array');

        // min default
        $rule = (new IntRule())->setMin(10);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(5), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('To small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['To small'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new IntRule())->setMin(10)->setToSmall("test to small");
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(5), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test to small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test to small'], $rule->getErrors(), 'Wrong errors array');

        // max
        $rule = (new IntRule())->setMax(5);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(15), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('To big', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['To big'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new IntRule())->setMax(5)->setToBig("test to small");
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(15), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test to small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test to small'], $rule->getErrors(), 'Wrong errors array');

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
        IntRule::selfCreateFromValidatorConfig(['test', null]);
        IntRule::selfCreateFromValidatorConfig([['test'], [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        IntRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        IntRule::selfCreateFromValidatorConfig(['test', 'test2']);
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
        IntRule::selfCreateFromValidatorConfig(['test', [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        IntRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        IntRule::selfCreateFromValidatorConfig(['test']);

        $this->expectException(RuleConfigurationException::class);
        IntRule::selfCreateFromValidatorConfig(['test', 'test222']);
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
        IntRule::selfCreateFromValidatorConfig([null, fn () => true]);
        IntRule::selfCreateFromValidatorConfig(['', fn () => true]);
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
        $rule = IntRule::selfCreateFromValidatorConfig(['testField', 'string', 'wrongType' => 'wrong type og :attribute']);
        $result = $rule->validate(null);
        $this->assertFalse($result);

        $this->assertEquals("wrong type og testField", $rule->getFirstError());
        $this->assertEquals(['wrong type og testField'], $rule->getErrors(), 'Wrong errors array');

        // small
        $rule = IntRule::selfCreateFromValidatorConfig(['testField', 'string', "min" => 5, 'toSmall' => ':attribute min :min size']);
        $this->assertFalse($rule->validate(2));
        $this->assertEquals("testField min 5 size", $rule->getFirstError());
        $this->assertEquals(['testField min 5 size'], $rule->getErrors());

        // long
        $rule = IntRule::selfCreateFromValidatorConfig(['testField', 'string', 'max' => 2, 'toBig' => ':attribute max :max chars length']);
        $result = $rule->validate(123);
        $this->assertFalse($result);

        $this->assertEquals("testField max 2 chars length", $rule->getFirstError());
        $this->assertEquals(['testField max 2 chars length'], $rule->getErrors());

    }



    /**
     *
     **/
    public function testOther ()
    {
        // $this->expectException(\Iljaaa\Machete\exceptions\ValidationException::class);
        $result = (new IntRule())->validate(null);
        $this->assertFalse($result, 'new stdClass() is valid string');
    }
}
