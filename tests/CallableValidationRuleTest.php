<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
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
     * Test set callable object as instance
     * @throws ValidationException
     */
    public function testCallableObject ()
    {
        $c = new class {
            public function testValidationMethod($value, string $attribute, CallableRule $r): bool {
                return true;
            }
        };

        $result = (new CallableRule([$c, 'testValidationMethod']))->validate(123);
        $this->assertTrue($result, 'new stdClass() is valid string');


        $result = (new CallableRule(function ($value, string $attribute, CallableRule $r) {
            return true;
        }))->validate(123);
        $this->assertTrue($result, 'new stdClass() is valid string');

        // fn
        $result = (new CallableRule(fn ($value, string $attribute, CallableRule $r) => $r->addError('i sire it\s wrong')->isValid()))->validate(123);
        $this->assertFalse($result, 'new stdClass() is valid string');
    }

    /**
     * Set callable object as array
     * @return void
     * @throws ValidationException
     */
    public function testCallableArray()
    {
        $result = (new CallableRule([$this, 'successResulCallableFunction']))->validate(123);
        $this->assertTrue($result);

        $result = (new CallableRule([$this, 'errorResulCallableFunction']))->validate(123);
        $this->assertFalse($result);

        $result = (new CallableRule([static::class, 'successResulCallableStaticFunction']))->validate(123);
        $this->assertTrue($result);

        $result = (new CallableRule([static::class, 'errorResulCallableStaticFunction']))->validate(123);
        $this->assertFalse($result);
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function testSetAttributeName()
    {
        $rule = (new CallableRule(function ($value, string $attribute, CallableRule $r)  {
            $this->assertEquals('testAttribute', $attribute, 'wrong attribute');
            return true;
        }))->setAttributeName('testAttribute');

        $this->assertEquals('testAttribute', $rule->getAttributeName());

        $result = $rule->validate(123);
        $this->assertTrue($result);
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function testExceptions ()
    {
        $this->expectException(ValidationException::class);

        $rule = new CallableRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('sASAsAS'), 'wrong result');
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
        CallableRule::selfCreateFromValidatorConfig(['test', [static::class, 'successResulCallableStaticFunction']]);
        CallableRule::selfCreateFromValidatorConfig([['test'], [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        CallableRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        CallableRule::selfCreateFromValidatorConfig(['test', 'second']);
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
        CallableRule::selfCreateFromValidatorConfig(['test', [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        CallableRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        CallableRule::selfCreateFromValidatorConfig(['second']);

        $this->expectException(RuleConfigurationException::class);
        CallableRule::selfCreateFromValidatorConfig(['test', 'second']);
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
        CallableRule::selfCreateFromValidatorConfig([null, fn () => true]);
        CallableRule::selfCreateFromValidatorConfig(['', fn () => true]);
    }

    /**
     *
     *
     * @throws ValidationException
     */
    public function testErrorMessages ()
    {
        // default message
        $rule = (new CallableRule())->setCallable(fn() => true);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertTrue($rule->validate('test'), 'wrong result');
        $this->assertTrue($rule->isValid(), 'wrong result');
        $this->assertEquals('', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals([], $rule->getErrors());

        // override message
        // $rule = (new CallableRule(fn() => true));
        $rule = new CallableRule(function ($value, string $attribute, CallableRule $r) {
            $r->addError('test error');
            return false;
        });
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('test'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test error', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test error'], $rule->getErrors());

        $rule = new CallableRule();
        $this->assertEquals('Object is not callable', $rule->getWrongType());

        $rule = CallableRule::selfCreateFromValidatorConfig([['name', "value"], fn() => false, 'wrongType' => 'test message']);
        $this->assertEquals('test message', $rule->getWrongType());
        $this->assertEquals('name, value', $rule->getAttributeName());
        $this->assertFalse($rule->validate(null));

        $rule = CallableRule::selfCreateFromValidatorConfig([['name', 'value'], function ($value, string $attribute, CallableRule $r) {
            $r->addError("test error"); return false;} , 'wrongType' => 'test message']);
        $this->assertEquals('name, value', $rule->getAttributeName());
        $this->assertEquals('test message', $rule->getWrongType());
        $this->assertFalse($rule->validate(null));
        $this->assertEquals('test error', $rule->getFirstError());
    }

    /**
     * @param $value
     * @param string $attribute
     * @param CallableRule $r
     * @return bool
     */
    public function successResulCallableFunction($value, string $attribute, CallableRule $r): bool
    {
        return true;
    }

    /**
     * @param $value
     * @param string $attribute
     * @param CallableRule $r
     * @return bool
     */
    public static function successResulCallableStaticFunction($value, string $attribute, CallableRule $r): bool
    {
        return true;
    }

    /**
     * @param $value
     * @param string $attribute
     * @param CallableRule $r
     * @return bool
     */
    public function errorResulCallableFunction($value, string $attribute, CallableRule $r): bool
    {
        $r->addError('test error');
        return $r->isValid();
    }

    /**
     * @param $value
     * @param string $attribute
     * @param CallableRule $r
     * @return bool
     */
    public static function errorResulCallableStaticFunction($value, string $attribute, CallableRule $r): bool
    {
        $r->addError('test error');
        return false;
    }


}

