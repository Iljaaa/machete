<?php

use Iljaaa\Machete\rules\validationRules\RequiredValidationRule;

/**
 * Test string component
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.0
 * @see https://github.com/Iljaaa/machete
 */
class RequiresValidationRuleTest extends \PHPUnit\Framework\TestCase
{


    /**
     *
     **/
    public function testDefaultFalseValues ()
    {
        $v = (new RequiredValidationRule());
        $this->assertFalse($v->isValid(), 'its not false');
        $result = $v->validate(new stdClass());
        $this->assertTrue($result, 'object is valid');
    }

    /**
     *
     **/
    public function testValues ()
    {
        $result = (new RequiredValidationRule())->validate(new stdClass());
        $this->assertTrue($result, 'object is valid');

        $result = (new RequiredValidationRule())->validate(new class {});
        $this->assertTrue($result, 'object is valid');

        $result = (new RequiredValidationRule())->validate([]);
        $this->assertFalse($result, 'array is not empty');

        $result = (new RequiredValidationRule())->validate([1]);
        $this->assertTrue($result, 'array is empty');

        $result = (new RequiredValidationRule())->validate(null);
        $this->assertFalse($result, 'null is required');

        $result = (new RequiredValidationRule())->validate(0);
        $this->assertFalse($result, 'object is not valid');

        $result = (new RequiredValidationRule())->validate(1);
        $this->assertTrue($result, 'object is not valid');

        $result = (new RequiredValidationRule())->validate('');
        $this->assertFalse($result, 'object is not valid');

        $result = (new RequiredValidationRule())->validate(' ');
        $this->assertTrue($result, 'whitespace is not true');

        $result = (new RequiredValidationRule())->validate('some string');
        $this->assertTrue($result, 'object is not valid');
    }

    /**
     *
     **/
    public function testDescription ()
    {
        $rule = new RequiredValidationRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('It\'s required', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['It\'s required'], $rule->getErrors(), 'Wrong errors array');

        $rule = new RequiredValidationRule(['message' => 'required test message']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required test message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['required test message'], $rule->getErrors(), 'Wrong errors array');
    }

}
