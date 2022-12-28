<?php

use Iljaaa\Machete\rules\validationRules\RequiredRule;

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
        $v = (new RequiredRule());
        $this->assertFalse($v->isValid(), 'its not false');
        $result = $v->validate(new stdClass());
        $this->assertTrue($result, 'object is valid');
    }

    /**
     *
     **/
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
     **/
    public function testDescription ()
    {
        $rule = new RequiredRule();
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('It\'s required', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['It\'s required'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new RequiredRule())->setMessage('required test message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required test message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['required test message'], $rule->getErrors(), 'Wrong errors array');

        $rule = RequiredRule::selfCreateFromValidatorConfig(['message' => 'required test message2']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required test message2', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['required test message2'], $rule->getErrors(), 'Wrong errors array');

        $rule = RequiredRule::selfCreateFromValidatorConfig(['message' => 'required test message2'])
            ->setMessage('required second test message');

        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required second test message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['required second test message'], $rule->getErrors(), 'Wrong errors array');
    }

}
