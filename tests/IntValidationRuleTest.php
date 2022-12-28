<?php

use Iljaaa\Machete\rules\validationRules\IntRule;

/**
 * Test string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.1
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class IntValidationRuleTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     **/
    public function testOther ()
    {
        // $this->expectException(\Iljaaa\Machete\exceptions\ValidationException::class);
        $result = (new IntRule())->validate(null);
        $this->assertFalse($result, 'new stdClass() is valid string');
    }

    /**
     *
     **/
    public function testDescription ()
    {
        // type
        $rule = new IntRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate('sadfsdfdsf'), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals("It's not int", $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['It\'s not int'], $rule->getErrors(), 'Wrong errors array');

        // override
        $rule = (new IntRule())->setWrongType('wrong type message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('wrong type message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['wrong type message'], $rule->getErrors(), 'Wrong errors array');


        // override
        $rule = IntRule::selfCreateFromValidatorConfig(['wrongType' => 'wrong type message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('wrong type message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['wrong type message'], $rule->getErrors(), 'Wrong errors array');



        // default message
        $rule = (new IntRule())->setMin(10)->setToSmall("test to small");
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(5), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test to small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test to small'], $rule->getErrors(), 'Wrong errors array');

        // override message
        $rule = IntRule::selfCreateFromValidatorConfig(['min' => 10, 'toSmall' => 'Test to big message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(5), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Test to big message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Test to big message'], $rule->getErrors(), 'Wrong errors array');



        // short

        // default message
        $rule = (new IntRule())->setMax(5)->setToBig("test to small");
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(15), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('test to small', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test to small'], $rule->getErrors(), 'Wrong errors array');

        // override message
        $rule = IntRule::selfCreateFromValidatorConfig(['max' => 5, 'toBig' => 'Test to big message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate(15), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Test to big message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Test to big message'], $rule->getErrors(), 'Wrong errors array');


        $rule = IntRule::selfCreateFromValidatorConfig(['max' => 5, 'toBig' => 'Test to big message'])->setToBig('rrrrrrr');
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
        $result = (new IntRule())->setMax(10)->validate(9);
        $this->assertTrue($result);

        $result = (new IntRule())->setMax(10)->validate(11);
        $this->assertFalse($result);

        $result = (new IntRule())->setMin(10)->validate(11);
        $this->assertTrue($result);

        $result = (new IntRule())->setMin(10)->validate(9);
        $this->assertFalse($result);

        //
        $result = IntRule::selfCreateFromValidatorConfig(['max' => 10])->validate(9);
        $this->assertTrue($result);

        $result = IntRule::selfCreateFromValidatorConfig(['max' => 10])->validate(11);
        $this->assertFalse($result);

        $result = IntRule::selfCreateFromValidatorConfig(['min' => 10])->validate(11);
        $this->assertTrue($result);

        $result = IntRule::selfCreateFromValidatorConfig(['min' => 10])->validate(9);
        $this->assertFalse($result);
    }

}
