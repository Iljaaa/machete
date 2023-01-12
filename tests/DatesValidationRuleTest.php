<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\DateRule;
use Iljaaa\Machete\rules\validationRules\DatesValidationBasicRule;
use Iljaaa\Machete\rules\validationRules\DateTimeRule;

/**
 * Test stringDate rule
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.2
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class StringDateValidationRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Try to validate of wrong type data
     * @throws ValidationException
     */
    public function testDate ()
    {
        $rule = new DateRule();
        $result = $rule->validate('2022-12-11');
        $this->assertIsBool($result);
        $this->assertTrue($result);

        $rule = new DateRule();
        $result = $rule->validate('2-3-2');
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * Try to validate of wrong type data
     * @throws ValidationException
     */
    public function testDateTime ()
    {
        $rule = new DateTimeRule();
        $result = $rule->validate('2022-12-31 12:55');
        $this->assertIsBool($result);
        $this->assertTrue($result);

        $rule = new DateTimeRule();
        $result = $rule->validate('2022-12-31 12:55');
        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    /**
     * Try to validate of wrong type data
     * @throws ValidationException
     */
    public function testSelfFormats ()
    {
        $rule = (new DateTimeRule())->setFormat('d-m-Y');
        $this->assertEquals('d-m-Y', $rule->getFormat());
        $result = $rule->validate('15-12-2012');
        $this->assertTrue($result, $rule->getFirstError());

        $rule = (new DateTimeRule())->setFormat('H-i d.m-Y');
        $this->assertEquals('H-i d.m-Y', $rule->getFormat());
        $result = $rule->validate('10-32 01.05-1873');
        $this->assertTrue($result, $rule->getFirstError());

        // wrongType
        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'date', 'format' => '123123']);
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("testField has wrong type", $rule->getFirstError());
        $this->assertEquals(['testField has wrong type'], $rule->getErrors(), 'Wrong errors array');

        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'date', 'format' => 'random format', 'wrongFormat' => 'wrong type og :attribute']);
        $result = $rule->validate(new DateTime());
        $this->assertFalse($result);
        $this->assertEquals("wrong type og testField", $rule->getFirstError());
        $this->assertEquals(['wrong type og testField'], $rule->getErrors(), 'Wrong errors array');
    }

    /**
     * Test min max string length validation
     * @throws ValidationException
     */
    public function testMinMax ()
    {
        $tomorrow = (new DateTime())->modify("+1 days")->setTime(0, 0);

        $yesterday = (new DateTime())->modify("-1 days")->setTime(0, 0);

        // correct
        $rule = (new DateTimeRule())->setMin($yesterday);
        $this->assertEquals($yesterday, $rule->getMin());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertTrue($result, $rule->getFirstError());

        $rule = (new DateTimeRule())->setMax($tomorrow);
        $this->assertEquals($tomorrow, $rule->getMax());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, 'new stdClass() is valid string');
        $this->assertTrue($result, $rule->getFirstError());

        $f = 'Y-m-d H:i';

        $rule = (new DateTimeRule())->setMinAsString($yesterday->format($f), $f);
        $this->assertEquals($yesterday, $rule->getMin());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertTrue($result, $rule->getFirstError());

        $rule = (new DateTimeRule())->setMax($tomorrow)->setMaxAsString($tomorrow->format($f), $f);
        $this->assertEquals($tomorrow, $rule->getMax());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result);
        $this->assertTrue($result, $rule->getFirstError());

        $f = 'Y-m-d H:i:s';
        $yesterday->setTime(0, 0, 10);

        $rule = (new DateTimeRule())->setMinAsString($yesterday->format($f), $f);
        $this->assertEquals($yesterday, $rule->getMin());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertTrue($result, $rule->getFirstError());

        $rule = (new DateTimeRule())->setMax($tomorrow)->setMaxAsString($tomorrow->format($f), $f);
        $this->assertEquals($tomorrow, $rule->getMax());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result);
        $this->assertTrue($result, $rule->getFirstError());


        // incorrect
        $rule = (new DateTimeRule())->setMin($tomorrow);
        $this->assertEquals($tomorrow, $rule->getMin());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertFalse($result, $rule->getFirstError());

        $rule = (new DateTimeRule())->setMax($yesterday);
        $this->assertEquals($yesterday, $rule->getMax());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertFalse($result, $rule->getFirstError());
    }

    /**
     * Test min max string length validation
     * @throws ValidationException
     */
    public function testMinMaxOnStaticCreate ()
    {
        $tomorrow = (new DateTime())->modify("+1 days")->setTime(0, 0);

        $yesterday = (new DateTime())->modify("-1 days")->setTime(0, 0);

        // correct
        $rule = DateTimeRule::selfCreateFromValidatorConfig(['attribute', 'datetime', 'min' => $yesterday]);
        $this->assertEquals($yesterday, $rule->getMin());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertTrue($result, $rule->getFirstError());

        $rule = DateTimeRule::selfCreateFromValidatorConfig(['attribute', 'datetime', 'max' => $tomorrow]);
        $this->assertEquals($tomorrow, $rule->getMax());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertTrue($result, $rule->getFirstError());

        $rule = DateTimeRule::selfCreateFromValidatorConfig(['attribute', 'datetime', 'min' => $yesterday->format(DateTimeRule::FORMAT)]);
        $this->assertEquals($yesterday, $rule->getMin());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertTrue($result, $rule->getFirstError());

        $rule = DateTimeRule::selfCreateFromValidatorConfig(['attribute', 'datetime', 'max' => $tomorrow->format(DateTimeRule::FORMAT)]);
        $this->assertEquals($tomorrow, $rule->getMax());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertTrue($result, $rule->getFirstError());

        // incorrect
        $rule = DateTimeRule::selfCreateFromValidatorConfig(['attribute', 'datetime', 'min' => $tomorrow->format(DateTimeRule::FORMAT)]);
        $this->assertEquals($tomorrow, $rule->getMin());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertFalse($result, $rule->getFirstError());

        $rule = DateTimeRule::selfCreateFromValidatorConfig(['attribute', 'datetime', 'max' => $yesterday->format(DateTimeRule::FORMAT)]);
        $this->assertEquals($yesterday, $rule->getMax());
        $result = $rule->validate(new DateTime());
        $this->assertIsBool($result, $rule->getFirstError());
        $this->assertFalse($result, $rule->getFirstError());
        $this->assertEquals('attribute is after '.$yesterday->format($rule->getFormat()).' value', $rule->getFirstError());

    }

    /**
     * @throws ValidationException
     */
    public function testDescription ()
    {
        // type
        $rule = new DateTimeRule();
        $this->assertFalse($rule->isValid());
        $this->assertFalse($rule->validate([]));
        $this->assertFalse($rule->isValid());
        $this->assertEquals("Value has wrong type", $rule->getFirstError());
        $this->assertEquals(['Value has wrong type'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new DateTimeRule())->setWrongType( 'wrong type error message');
        $this->assertEquals('wrong type error message', $rule->getWrongType());
        $this->assertFalse($rule->isValid());
        $this->assertFalse($rule->validate([]));
        $this->assertFalse($rule->isValid());
        $this->assertEquals('wrong type error message', $rule->getFirstError());
        $this->assertEquals(['wrong type error message'], $rule->getErrors(), 'Wrong errors array');

        // format
        $rule = new DateTimeRule();
        $this->assertFalse($rule->isValid());
        $this->assertFalse($rule->validate('4499'));
        $this->assertFalse($rule->isValid());
        $this->assertEquals("Value has wrong format", $rule->getFirstError());
        $this->assertEquals(['Value has wrong format'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new DateTimeRule())->setWrongFormat( 'wrong format error message');
        $this->assertEquals('wrong format error message', $rule->getWrongFormat());
        $this->assertFalse($rule->isValid());
        $this->assertFalse($rule->validate('4499'));
        $this->assertFalse($rule->isValid());
        $this->assertEquals('wrong format error message', $rule->getFirstError());
        $this->assertEquals(['wrong format error message'], $rule->getErrors(), 'Wrong errors array');

        // min
        $tomorrow = (new DateTime())->modify("+1 day");

        $rule = (new DateTimeRule())->setMin($tomorrow);
        $this->assertFalse($rule->isValid());
        $this->assertFalse($rule->validate( new DateTime()));
        $this->assertFalse($rule->isValid());
        $this->assertEquals('Value is before minimal value', $rule->getFirstError());
        $this->assertEquals(['Value is before minimal value'], $rule->getErrors(), 'Wrong errors array');

        // override message
        $rule = (new DateTimeRule())->setMin($tomorrow)->setBeforeMin('something after minimal');
        $this->assertEquals('something after minimal', $rule->getBeforeMin());
        $this->assertFalse($rule->isValid());
        $this->assertFalse($rule->validate( new DateTime()));
        $this->assertFalse($rule->isValid());
        $this->assertEquals('something after minimal', $rule->getFirstError());
        $this->assertEquals(['something after minimal'], $rule->getErrors());

        // max
        $yesterday = (new DateTime())->modify("-1 day");

        $rule = (new DateTimeRule())->setMax($yesterday);
        $this->assertFalse($rule->isValid());
        $this->assertFalse($rule->validate((new DateTime())->format($rule->getFormat())));
        $this->assertFalse($rule->isValid());
        $this->assertEquals('Value is after maximal value', $rule->getFirstError());
        $this->assertEquals(['Value is after maximal value'], $rule->getErrors());

        // override
        $rule = (new DateTimeRule())->setMax($yesterday)->setAfterMax('Test short message');
        $this->assertEquals('Test short message', $rule->getAfterMax());
        $this->assertFalse($rule->isValid());
        $this->assertFalse($rule->validate((new DateTime())->format($rule->getFormat())));
        $this->assertFalse($rule->isValid());
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

        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'date', 'format' => 'd.m.Y', 'wrongFormat' => 'wrong format on: :attribute ans format: :format']);
        $this->assertEquals("d.m.Y", $rule->getFormat());
        $this->assertTrue($rule->validate('25.07.2982'), $rule->getFirstError());

        $this->assertFalse($rule->validate('25.2982'), $rule->getFirstError());
        $this->assertEquals('wrong format on: testField ans format: d.m.Y', $rule->getFirstError());

        // not twos
        $this->expectException(RuleConfigurationException::class);
        DateTimeRule::selfCreateFromValidatorConfig(['test', null]);
        DateTimeRule::selfCreateFromValidatorConfig([['test'], [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        DateTimeRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        DateTimeRule::selfCreateFromValidatorConfig(['test', 'test2']);
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
        DateTimeRule::selfCreateFromValidatorConfig(['test', [static::class, 'successResulCallableStaticFunction']]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        DateTimeRule::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        DateTimeRule::selfCreateFromValidatorConfig(['test']);

        $this->expectException(RuleConfigurationException::class);
        DateTimeRule::selfCreateFromValidatorConfig(['test', 'test222']);
    }

    /**
     * @return void
     * @throws RuleConfigurationException
     * @throws ValidationException
     */
    public function testAssertsOnCreateFromFormConfig()
    {
        // correct attribute name and validator name
        DateTimeRule::selfCreateFromValidatorConfig(['name', 'date']);
        DateTimeRule::selfCreateFromValidatorConfig(['name', 'datetime']);

        // enable assert
        assert_options(ASSERT_ACTIVE, 1);

        //
        $this->expectError();

        // wrong attribute
        DateTimeRule::selfCreateFromValidatorConfig([null, 'date']);
        DateTimeRule::selfCreateFromValidatorConfig(['', 'date']);

        // wrong validator name
        DateTimeRule::selfCreateFromValidatorConfig(['name', 'wrongName']);
        DateTimeRule::selfCreateFromValidatorConfig(['name', []]);
        DateTimeRule::selfCreateFromValidatorConfig(['name', true]);
        DateTimeRule::selfCreateFromValidatorConfig(['name', fn () => true]);
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


        // wrongType
        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'date']);
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("testField has wrong type", $rule->getFirstError());
        $this->assertEquals(['testField has wrong type'], $rule->getErrors(), 'Wrong errors array');

        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'date', 'wrongType' => 'wrong type og :attribute']);
        $result = $rule->validate(null);
        $this->assertFalse($result);
        $this->assertEquals("wrong type og testField", $rule->getFirstError());
        $this->assertEquals(['wrong type og testField'], $rule->getErrors(), 'Wrong errors array');

        // wrongFormat
        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'datetime']);
        $result = $rule->validate('4499');
        $this->assertFalse($result);
        $this->assertEquals("testField has wrong Y-m-d H:i", $rule->getFirstError());
        $this->assertEquals(['testField has wrong Y-m-d H:i'], $rule->getErrors(), 'Wrong errors array');

        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'date', 'wrongFormat' => 'wrong format on: :attribute ans format: :format']);
        $result = $rule->validate('4499');
        $this->assertFalse($result);
        $this->assertEquals("wrong format on: testField ans format: Y-m-d", $rule->getFirstError());
        $this->assertEquals(['wrong format on: testField ans format: Y-m-d'], $rule->getErrors());

        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'datetime', 'wrongFormat' => 'wrong format on: :attribute ans format: :format']);
        $result = $rule->validate('4499');
        $this->assertFalse($result);
        $this->assertEquals("wrong format on: testField ans format: Y-m-d H:i", $rule->getFirstError());
        $this->assertEquals(['wrong format on: testField ans format: Y-m-d H:i'], $rule->getErrors());

        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'date', 'format' => 'd.m.Y', 'wrongFormat' => 'wrong format on: :attribute ans format: :format']);
        $result = $rule->validate('4499');
        $this->assertFalse($result);
        $this->assertEquals("wrong format on: testField ans format: d.m.Y", $rule->getFirstError());
        $this->assertEquals(['wrong format on: testField ans format: d.m.Y'], $rule->getErrors(), 'Wrong errors array');

        // min
        /*$rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'datetime', "min" => 5, 'toShort' => ':attribute min :min chars length']);
        $result = $rule->validate('123');
        $this->assertFalse($result);

        $this->assertEquals("testField min 5 chars length", $rule->getFirstError());
        $this->assertEquals(['testField min 5 chars length'], $rule->getErrors());

        // max
        $rule = DatesValidationBasicRule::selfCreateFromValidatorConfig(['testField', 'datetime', 'max' => 2, 'toLong' => ':attribute max :max chars length']);
        $result = $rule->validate("123");
        $this->assertFalse($result);

        $this->assertEquals("testField max 2 chars length", $rule->getFirstError());
        $this->assertEquals(['testField max 2 chars length'], $rule->getErrors());*/
    }

    /**
     *
     *
     * @throws ValidationException
     */
    public function testWrongFormatException ()
    {
        $result = (new DateTimeRule())->validate(new stdClass());
        $this->assertFalse($result, 'new stdClass() is valid string');
    }
}
