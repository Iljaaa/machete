<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\InValidationRule;

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
     **/
    public function testDefaultFalseValues ()
    {
        $v = (new InValidationRule(['aaa']));
        $this->assertFalse($v->isValid(), 'its not false');
        $result = $v->validate('aaa');
        $this->assertTrue($result, 'object is valid');
    }

    /**
     * @return void
     */
    public function testDirectMethod ()
    {
        $result = (new InValidationRule())->inArray(2, [1, 2]);
        $this->assertTrue($result);

        $result = (new InValidationRule())->inArray(2, [1, 2], true);
        $this->assertTrue($result);

        $result = (new InValidationRule())->inArray(3, [1, 2], true);
        $this->assertFalse($result);


    }

    public function testExceptionsOnValidate ()
    {
        $this->expectException(ValidationException::class);
        (new InValidationRule())->validate([]);

        $this->expectException(ValidationException::class);
        (new InValidationRule())->validate([1 => ['aaa']]);

        $this->expectException(ValidationException::class);
        (new InValidationRule())->validate([0 => ['aaa'], 2 => true]);
    }

    /**
     *
     **/
    public function testValues ()
    {
        $result = (new InValidationRule([null]))->validate(null);
        $this->assertTrue($result, 'array is not empty');

        $result = (new InValidationRule([1, 2]))->validate(2);
        $this->assertTrue($result, 'array is not empty');

        $result = (new InValidationRule([[1]]))->validate([1]);
        $this->assertTrue($result, 'array is not empty');

        $result = (new InValidationRule([new stdClass()]))->validate(new stdClass());
        $this->assertTrue($result, 'object is valid');
        $result = (new InValidationRule())->inArray(new stdClass(), [new stdClass()]);
        $this->assertTrue($result, 'object is valid');

        $result = (new InValidationRule([new class {}]))->validate(new class {});
        $this->assertFalse($result, 'object is valid');
        $result = (new InValidationRule())->inArray(new class {}, [new class {}]);
        $this->assertFalse($result, 'object is valid');

        $c = new class {};
        $result = (new InValidationRule([$c]))->validate($c);
        $this->assertTrue($result, 'object is valid');

        $result = (new InValidationRule())->setHaystack([$c])->validate($c);
        $this->assertTrue($result, 'object is valid');

        $result = (new InValidationRule())->inArray($c, [$c]);
        $this->assertTrue($result, 'object is valid');
    }

    /**
     *
     **/
    public function testDescription ()
    {
        $rule = new InValidationRule(['hi']);
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('Not in array', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['Not in array'], $rule->getErrors(), 'Wrong errors array');

        $rule = (new InValidationRule([]))->setMessage('required test message');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertFalse($rule->validate([]), 'wrong result');
        $this->assertFalse($rule->isValid(), 'wrong result');
        $this->assertEquals('required test message', $rule->getFirstError(), 'Wrong first error');
        $this->assertEquals(['required test message'], $rule->getErrors(), 'Wrong errors array');
    }

}
