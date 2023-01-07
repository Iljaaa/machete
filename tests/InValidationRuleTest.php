<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\InRule;

/**
 * Test in component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @see https://github.com/Iljaaa/machete
 */
class InValidationRuleTest extends \PHPUnit\Framework\TestCase
{

    /**
     *
     *
     * @throws ValidationException
     */
    public function testDefaultFalseValues ()
    {
        $v = (new InRule(['aaa']));
        $this->assertFalse($v->isValid(), 'its not false');
        $result = $v->validate('aaa');
        $this->assertTrue($result, 'object is valid');
    }

    /**
     * @return void
     */
    public function testDirectMethod ()
    {
        $result = (new InRule())->inArray(2, [1, 2]);
        $this->assertTrue($result);

        $result = (new InRule())->inArray(2, [1, 2], true);
        $this->assertTrue($result);

        $result = (new InRule())->inArray(3, [1, 2], true);
        $this->assertFalse($result);


    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function testExceptionsOnValidate ()
    {
        $this->expectException(ValidationException::class);
        (new InRule())->validate([]);

        $this->expectException(ValidationException::class);
        (new InRule())->validate([1 => ['aaa']]);

        $this->expectException(ValidationException::class);
        (new InRule())->validate([0 => ['aaa'], 2 => true]);
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
        InRule::selfCreateFromValidatorConfig(['test', null]);
        InRule::selfCreateFromValidatorConfig([['test'], [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        InRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        InRule::selfCreateFromValidatorConfig(['test', 'test2']);
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

        $this->expectException(RuleConfigurationException::class);
        InRule::selfCreateFromValidatorConfig(['field', 'in']);

        // not twos
        $this->expectException(RuleConfigurationException::class);
        InRule::selfCreateFromValidatorConfig(['test', [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        InRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        InRule::selfCreateFromValidatorConfig(['test']);

        $this->expectException(RuleConfigurationException::class);
        InRule::selfCreateFromValidatorConfig(['test', 'test222']);
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
        InRule::selfCreateFromValidatorConfig([null, fn () => true]);
        InRule::selfCreateFromValidatorConfig(['', fn () => true]);
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
        $rule = InRule::selfCreateFromValidatorConfig(['testField', 'string', [1, 2]]);
        $this->assertEquals("testField not in array", $rule->getMessage());
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("testField not in array", $rule->getFirstError());
        $this->assertEquals(['testField not in array'], $rule->getErrors());

        $rule = InRule::selfCreateFromValidatorConfig(['testField', 'string', [1, 2], 'message' => 'it not in :attribute']);
        $this->assertEquals("it not in testField", $rule->getMessage());
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("it not in testField", $rule->getFirstError());
        $this->assertEquals(['it not in testField'], $rule->getErrors());

    }

    /**
     *
     *
     * @throws ValidationException
     */
    public function testValues ()
    {
        $result = (new InRule([null]))->validate(null);
        $this->assertTrue($result, 'array is not empty');

        $result = (new InRule([1, 2]))->validate(2);
        $this->assertTrue($result, 'array is not empty');

        $result = (new InRule([[1]]))->validate([1]);
        $this->assertTrue($result, 'array is not empty');

        $result = (new InRule([new stdClass()]))->validate(new stdClass());
        $this->assertTrue($result, 'object is valid');
        $result = (new InRule())->inArray(new stdClass(), [new stdClass()]);
        $this->assertTrue($result, 'object is valid');

        $result = (new InRule([new class {}]))->validate(new class {});
        $this->assertFalse($result, 'object is valid');
        $result = (new InRule())->inArray(new class {}, [new class {}]);
        $this->assertFalse($result, 'object is valid');

        $c = new class {};
        $result = (new InRule([$c]))->validate($c);
        $this->assertTrue($result, 'object is valid');

        $result = (new InRule())->setHaystack([$c])->validate($c);
        $this->assertTrue($result, 'object is valid');

        $result = (new InRule())->inArray($c, [$c]);
        $this->assertTrue($result, 'object is valid');
    }

    /**
     *
     *
     * @throws ValidationException
     */
    public function testDescription ()
    {
        $rule = new InRule(['hi']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Value not in array', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Value not in array'], $rule->getErrors());

        $rule = (new InRule([]))->setMessage('required test message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required test message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['required test message'], $rule->getErrors());
    }

}
