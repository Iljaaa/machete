<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\RequiredRule;

/**
 * Test string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.1.1
 * @see https://github.com/Iljaaa/machete
 */
class RequiredValidationRuleTest extends \PHPUnit\Framework\TestCase
{


    /**
     *
     *
     * @throws ValidationException
     */
    public function testDefaultFalseValues ()
    {
        $v = (new RequiredRule());
        $this->assertFalse($v->isValid(), 'its not false');
        $result = $v->validate(new stdClass());
        $this->assertTrue($result, 'object is valid');
    }

    /**
     * test different
     *
     * @throws ValidationException
     */
    public function testValues ()
    {
        $result = (new RequiredRule())->validate(new stdClass());
        $this->assertTrue($result, 'object is valid');

        $result = (new RequiredRule())->validate(new class {});
        $this->assertTrue($result, 'object is valid');

        $result = (new RequiredRule())->validate([]);
        $this->assertFalse($result, 'array is not empty');

        $result = (new RequiredRule())->validate([1]);
        $this->assertTrue($result, 'array is empty');

        $result = (new RequiredRule())->validate(null);
        $this->assertFalse($result, 'null is required');

        $result = (new RequiredRule())->validate(0);
        $this->assertFalse($result, 'object is not valid');

        $result = (new RequiredRule())->validate(1);
        $this->assertTrue($result, 'object is not valid');

        $result = (new RequiredRule())->validate('');
        $this->assertFalse($result, 'object is not valid');

        $result = (new RequiredRule())->validate(' ');
        $this->assertTrue($result, 'whitespace is not true');

        $result = (new RequiredRule())->validate('some string');
        $this->assertTrue($result, 'object is not valid');
    }

    /**
     *
     *
     * @throws RuleConfigurationException
     * @throws ValidationException
     */
    public function testDescription ()
    {
        $rule = new RequiredRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Value required', $rule->getFirstError());
        $this->assertEquals(['Value required'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new RequiredRule())->setMessage('required test message');
        $this->assertEquals('required test message', $rule->getMessage());
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required test message', $rule->getFirstError());
        $this->assertEquals(['required test message'], $rule->getErrors(), 'Wrong errors array');

        $rule = RequiredRule::selfCreateFromValidatorConfig([['attribute'], 'message' => 'required test message2']);
        $this->assertEquals('required test message2', $rule->getMessage());
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required test message2', $rule->getFirstError());
        $this->assertEquals(['required test message2'], $rule->getErrors(), 'Wrong errors array');

        // override attribute
        $rule = RequiredRule::selfCreateFromValidatorConfig(['testAttr', 'message' => ':attribute required']);
        $this->assertEquals('testAttr required', $rule->getMessage());
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('testAttr required', $rule->getFirstError());
        $this->assertEquals(['testAttr required'], $rule->getErrors(), 'Wrong errors array');

        $rule = RequiredRule::selfCreateFromValidatorConfig(['name', 'message' => 'required test message2'])
            ->setMessage('required second test message');

        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required second test message', $rule->getFirstError());
        $this->assertEquals(['required second test message'], $rule->getErrors(), 'Wrong errors array');
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
        $rule = RequiredRule::selfCreateFromValidatorConfig(['test', null]);
        $this->assertTrue($rule->validate('not empty value'));

        $rule = RequiredRule::selfCreateFromValidatorConfig([['test'], [static::class, 'successResulCallableStaticFunction']]);
        $this->assertTrue($rule->validate('not empty value'));
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
        RequiredRule::selfCreateFromValidatorConfig([null, 'string', 'format' => 'd.m.Y', 'wrongFormat' => 'wrong format on: :attribute ans format: :format']);
        RequiredRule::selfCreateFromValidatorConfig([]);

        RequiredRule::selfCreateFromValidatorConfig(['', 'date', 'format' => 'd.m.Y']);
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

        //
        $this->expectError();

        // wrong attribute
        RequiredRule::selfCreateFromValidatorConfig([null, 'date']);
        RequiredRule::selfCreateFromValidatorConfig(['', 'date']);

        // wrong validator name
        RequiredRule::selfCreateFromValidatorConfig(['name', 'wrongName']);
        RequiredRule::selfCreateFromValidatorConfig(['name', []]);
        RequiredRule::selfCreateFromValidatorConfig(['name', true]);
        RequiredRule::selfCreateFromValidatorConfig(['name', fn () => true]);
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

        $rule = RequiredRule::selfCreateFromValidatorConfig(['testField', 'date']);
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("testField is required", $rule->getFirstError());
        $this->assertEquals(['testField is required'], $rule->getErrors(), 'Wrong errors array');

        $rule = RequiredRule::selfCreateFromValidatorConfig(['testField', 'date', 'wrongType' => 'required og :attribute']);
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("testField is required", $rule->getFirstError());
        $this->assertEquals(['testField is required'], $rule->getErrors(), 'Wrong errors array');
    }

}
