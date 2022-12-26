<?php

use Iljaaa\Machete\rules\validationRules\FloatValidationRule;
use Iljaaa\Machete\rules\validationRules\StringValidationRule;

/**
 * Test string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class FloatValidationRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     **/
    public function testOther ()
    {
        // $this->expectException(\Iljaaa\Machete\exceptions\ValidationException::class);
        $result = (new FloatValidationRule())->validate(null);
        $this->assertFalse($result, 'new stdClass() is valid string');
    }

    /**
     *
     **/
    public function testDescription ()
    {
        // type
        $rule = new FloatValidationRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('sadfsdfdsf'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals("It's not int", $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['It\'s not int'], $rule->getErrors(), 'Wrong errors array');

        // override
        $rule = (new FloatValidationRule())->setWrongType('wrong type message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('wrong type message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['wrong type message'], $rule->getErrors(), 'Wrong errors array');


        // override
        $rule = FloatValidationRule::selfCreateFromValidatorConfig(['wrongType' => 'wrong type message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('wrong type message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['wrong type message'], $rule->getErrors(), 'Wrong errors array');



        // default message
        $rule = (new FloatValidationRule())->setMin(10)->setToSmall("test to small");
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(5), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test to small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test to small'], $rule->getErrors(), 'Wrong errors array');

        // override message
        $rule = FloatValidationRule::selfCreateFromValidatorConfig(['min' => 10, 'toSmall' => 'Test to big message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(5), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Test to big message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Test to big message'], $rule->getErrors(), 'Wrong errors array');



        // short

        // default message
        $rule = (new FloatValidationRule())->setMax(5)->setToBig("test to small");
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(15), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test to small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test to small'], $rule->getErrors(), 'Wrong errors array');

        // override message
        $rule = FloatValidationRule::selfCreateFromValidatorConfig(['max' => 5, 'toBig' => 'Test to big message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(15), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Test to big message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Test to big message'], $rule->getErrors(), 'Wrong errors array');


        $rule = FloatValidationRule::selfCreateFromValidatorConfig(['max' => 5, 'toBig' => 'Test to big message'])->setToBig('rrrrrrr');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(19), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('rrrrrrr', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['rrrrrrr'], $rule->getErrors(), 'Wrong errors array');

    }


    /**
     *
     **/
    public function testMinMax ()
    {
        //
        $result = (new FloatValidationRule())->setMax(10)->validate(9);
        $this->assertTrue($result);

        $result = (new FloatValidationRule())->setMax(10)->validate(11);
        $this->assertFalse($result);

        $result = (new FloatValidationRule())->setMin(10)->validate(11);
        $this->assertTrue($result);

        $result = (new FloatValidationRule())->setMin(10)->validate(9);
        $this->assertFalse($result);

        //
        $result = FloatValidationRule::selfCreateFromValidatorConfig(['max' => 10])->validate(9);
        $this->assertTrue($result);

        $result = FloatValidationRule::selfCreateFromValidatorConfig(['max' => 10])->validate(11);
        $this->assertFalse($result);

        $result = FloatValidationRule::selfCreateFromValidatorConfig(['min' => 10])->validate(11);
        $this->assertTrue($result);

        $result = FloatValidationRule::selfCreateFromValidatorConfig(['min' => 10])->validate(9);
        $this->assertFalse($result);
    }

}
