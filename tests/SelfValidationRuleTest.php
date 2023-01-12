<?php

use Iljaaa\Machete\exceptions\RuleConfigurationException;
use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\UserRuleWrapper;
use Iljaaa\Machete\Validation;

/**
 * Test implement rule interface
 *
 * @author ilja <the.ilja@gmail.com>
 * @version 1.0.2
 * @package Iljaaa\Machete
 * @see https://github.com/Iljaaa/machete
 */
class SelfValidationRuleTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test set callable object as instance
     */
    public function testUserRoleAsInstance()
    {
        $form = new class extends \Iljaaa\Machete\Validation
        {
            public string $name = 'test';

            private \Iljaaa\Machete\rules\UserRule $rule;

            public function __construct ()
            {
                parent::__construct();

                $this->rule = new class implements \Iljaaa\Machete\rules\UserRule {

                    public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool
                    {
                        $userRuleWrapper->addError('test error');
                        return false;
                    }

                };
            }

            public function rules (): array
            {
                return [
                    [['test'], 'rule', $this->rule]
                ];
            }

        };


        $this->assertFalse($form->isValid(), 'wrong result');
        $this->assertFalse($form->validate(), 'wrong result');
        $this->assertFalse($form->isValid(), 'wrong result');
        $this->assertEquals('test error', $form->getFirstError(), 'Wrong first error');
        $this->assertEquals(['test' => ['test error']], $form->getErrors(), 'Wrong errors array');
        $this->assertEquals(['test error'], $form->getErrorsForAttribute('test'), 'Wrong errors array');
        $this->assertEquals('test error', $form->getFirstErrorForAttribute('test'), 'Wrong errors array');

    }


    /**
     * Test set callable object as instance
     */
    public function testRuleAsInstance ()
    {
        $form = new class extends \Iljaaa\Machete\Validation
        {
            public string $name = 'test';

            private \Iljaaa\Machete\rules\UserRule $rule;

            public function __construct ()
            {
                parent::__construct();

                $this->rule = new class implements \Iljaaa\Machete\rules\UserRule {

                    public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool
                    {
                        return true;
                    }

                };
            }

            public function rules (): array
            {
                return [
                    [['name'], 'rule', $this->rule]
                ];
            }

        };


        $this->assertFalse($form->isValid(), 'wrong result');
        $this->assertTrue($form->validate(), 'wrong result');
        $this->assertTrue($form->isValid(), 'wrong result');
        $this->assertEquals('', $form->getFirstError(), 'Wrong first error');
        $this->assertEquals([], $form->getErrors(), 'Wrong errors array');
    }


    /**
     * @return void
     * @throws ValidationException
     */
    public function testExceptions ()
    {
        // disable assert
        assert_options(ASSERT_ACTIVE, 0);

        $this->expectException(ValidationException::class);

        $rule = new UserRuleWrapper();
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
        UserRuleWrapper::selfCreateFromValidatorConfig(['asdasd', 'rule', TestRuleClass::class]);
        UserRuleWrapper::selfCreateFromValidatorConfig([['asdasd'], 'rule', 'rule' => TestRuleClass::class]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        UserRuleWrapper::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        UserRuleWrapper::selfCreateFromValidatorConfig(['asdasd', 'dadas']);
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
        // $this->expectException(RuleConfigurationException::class);
        UserRuleWrapper::selfCreateFromValidatorConfig(['asdasd', 'rule', TestRuleClass::class]);

        // throws
        $this->expectException(RuleConfigurationException::class);
        UserRuleWrapper::selfCreateFromValidatorConfig([]);

        $this->expectException(RuleConfigurationException::class);
        UserRuleWrapper::selfCreateFromValidatorConfig(['aaaa']);

        $this->expectException(RuleConfigurationException::class);
        UserRuleWrapper::selfCreateFromValidatorConfig(['asdasd', 'dadas']);
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
        UserRuleWrapper::selfCreateFromValidatorConfig([null, fn () => true]);
        UserRuleWrapper::selfCreateFromValidatorConfig(['', fn () => true]);
    }


    /**
     * Test set callable object as instance
     */
    public function testErrorMessages()
    {
        $form = new class extends \Iljaaa\Machete\Validation
        {
            public string $name = 'test';
            public string $sName = '';

            private \Iljaaa\Machete\rules\UserRule $rule;

            public function __construct ()
            {
                parent::__construct();

                $this->rule = new class implements \Iljaaa\Machete\rules\UserRule {

                    public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool
                    {
                        if (empty($value)) {
                            return $userRuleWrapper->addError('test error')->isValid();
                        }

                        return $userRuleWrapper->isValid();
                    }

                };
            }

            public function rules (): array
            {
                return [
                    [['name'], 'rule', $this->rule],
                    [['sName'], 'rule', $this->rule],
                ];
            }

        };


        $this->assertFalse($form->isValid(), 'wrong result');
        $this->assertFalse($form->validate(), 'wrong result');
        $this->assertFalse($form->isValid(), 'wrong result');
        $this->assertEquals('test error', $form->getFirstError(), 'Wrong first error');
        $this->assertEquals(['sName' => ['test error']], $form->getErrors());
        $this->assertEquals([], $form->getErrorsForAttribute('test'));
        $this->assertEquals([], $form->getErrorsForAttribute('name'));
        $this->assertEquals(['test error'], $form->getErrorsForAttribute('sName'));
        $this->assertEquals('test error', $form->getFirstErrorForAttribute('sName'));

        $this->assertFalse($form->isValid(), 'wrong result');
        $this->assertFalse($form->validate(['sName']), 'wrong result');
        $this->assertFalse($form->isValid(), 'wrong result');
        $this->assertEquals('test error', $form->getFirstError(), 'Wrong first error');
        $this->assertEquals(['sName' => ['test error']], $form->getErrors());
        $this->assertEquals([], $form->getErrorsForAttribute('test'));
        $this->assertEquals([], $form->getErrorsForAttribute('name'));
        $this->assertEquals(['test error'], $form->getErrorsForAttribute('sName'));
        $this->assertEquals('test error', $form->getFirstErrorForAttribute('sName'));

        $this->assertFalse($form->isValid(), 'wrong result');
        $this->assertTrue($form->validate(['name']), 'wrong result');
        $this->assertTrue($form->isValid(), 'wrong result');
        $this->assertEquals('', $form->getFirstError(), 'Wrong first error');
        $this->assertEquals([], $form->getErrors());
        $this->assertEquals([], $form->getErrorsForAttribute('test'));
        $this->assertEquals([], $form->getErrorsForAttribute('name'));
        $this->assertEquals([], $form->getErrorsForAttribute('sName'));
        $this->assertEquals('', $form->getFirstErrorForAttribute('sName'));

    }


}


class TestRuleClass implements \Iljaaa\Machete\rules\UserRule
{
    public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool
    {
        return false;
    }

}
