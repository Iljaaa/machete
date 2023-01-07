<?php

use Iljaaa\Machete\exceptions\ValidationException;
use Iljaaa\Machete\rules\validationRules\CallableRule;
use Iljaaa\Machete\Validation;

require(__DIR__.'/../vendor/autoload.php');

/**
 *
 * @author ilja <the.ilja@gmail.com>
 * @package Iljaaa\Machete
 * @version 1.0.4
 * @see https://github.com/Iljaaa/machete
 */
class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     * @throws ValidationException
     */
     public function testAllRules ()
     {
         $validator = new class extends Validation
         {
             public string $string = '';

             public string $myNameWho = 'SlimShady';

             public function rules (): array
             {
                 return [
                     [['string'], 'required', 'message' => 'String is required'],
                     [['number'], 'required'],
                     [['myNameWho'], 'string'],
                     [['myNameWho'], 'required', 'message' => 'Its required field'],
                     [['myNameWho'], 'regex', '/^S/'],
                     [['number'], 'int', "min" => 7],
                     [['number'], 'float', "min" => 7],
                     [['notSet'], 'required'],
                     [['valid'], 'required'],
                     ['phones', 'in', ['345', '123', '333']],
                     [['number', 'valid'], fn () => true]
                 ];
             }
         };

        $validator->load ([
            'string' => '',
            'number' => 10,
            'valid' => 'some value',
        ]);


         $this->assertFalse($validator->isValid(), 'is validate flag wrong');
         $this->assertFalse($validator->isVasValidated());
         $this->assertFalse($validator->validate());
         $this->assertTrue($validator->isVasValidated());
         $this->assertFalse($validator->isValid());
         $this->assertFalse($validator->isValid(), 'is validate flag wrong 2');

         $this->assertFalse($validator->isAttributeValid('string'));
         $this->assertTrue($validator->isAttributeValid('number'));
         $this->assertFalse($validator->isAttributeValid('notSet'));
         $this->assertTrue($validator->isAttributeValid('valid'));

         $this->assertEquals('String is required', $validator->getFirstErrorForAttribute('string'));
         $this->assertEquals('', $validator->getFirstErrorForAttribute('number'));
         $this->assertEquals('notSet is required', $validator->getFirstErrorForAttribute('notSet'));
         $this->assertEmpty($validator->getFirstErrorForAttribute('valid'));
     }

    /**
     *
     * @throws ValidationException
     */
     public function testOther ()
     {
         $validator = new class extends Validation
         {
             public string $string = '';

             public function rules (): array
             {
                 return [
                     [['string'], 'required', 'message' => 'String is required'],
                     [['number'], 'required'],
                     [['notSet'], 'required'],
                     [['valid'], 'required'],
                     ['phones', 'in', ['345', '123', '333']]
                 ];
             }
         };

        $validator->load ([
            'string' => '',
            'number' => 10,
            'valid' => 'some value'
        ]);


         $this->assertFalse($validator->isValid(), 'is validate flag wrong');
         $this->assertFalse($validator->isVasValidated());
         $this->assertFalse($validator->validate());
         $this->assertTrue($validator->isVasValidated());
         $this->assertFalse($validator->isValid());
         $this->assertFalse($validator->isValid(), 'is validate flag wrong 2');

         $this->assertFalse($validator->isAttributeValid('string'));
         $this->assertTrue($validator->isAttributeValid('number'));
         $this->assertFalse($validator->isAttributeValid('notSet'));
         $this->assertTrue($validator->isAttributeValid('valid'));

         $this->assertEquals('String is required', $validator->getFirstErrorForAttribute('string'));
         $this->assertEquals('', $validator->getFirstErrorForAttribute('number'));
         $this->assertEquals('notSet is required', $validator->getFirstErrorForAttribute('notSet'));
         $this->assertEmpty($validator->getFirstErrorForAttribute('valid'));
     }


    /**
     * Test when data in class attributes
     * @throws ValidationException
     */
     public function testClassParamsValidation ()
     {
         $validator = new class extends Validation
         {
             protected string $name = 'my test name';

             public function rules(): array
             {
                 return [
                     [['name'], 'string', 'min' => 7],
                     // [['shortString'], 'string', 'max' => 3, 'toLong' => 'Short field to long'],
                     // [['validString'], 'string', 'min' => 3, 'max' => 6],
                 ];
             }
         };

         $this->assertFalse($validator->isValid(), 'start is isValid flag is wrong');
         $this->assertFalse($validator->isVasValidated(), 'start is isValid flag is wrong');
         $this->assertTrue($validator->validate(), "class is not valid");
         $this->assertTrue($validator->isVasValidated(), 'start is isValid flag is wrong');
         $this->assertTrue($validator->isValid(), 'after validate isValid is wrong');

         $this->assertEmpty($validator->getFirstErrorForAttribute('name'), 'error of name mast by null');
         // $this->assertEquals('Short field to long', $validator->getFirstErrorForField('shortString'));
    }

    /**
     * Test loaf validator data from load function
     *
     * @throws ValidationException
     */
     public function testLoadParamsFunction ()
     {
         $validator = new class extends Validation
         {
             public function rules(): array
             {
                 return [
                     [['name'], 'string', 'min' => 7],
                     // [['shortString'], 'string', 'max' => 3, 'toLong' => 'Short field to long'],
                     // [['validString'], 'string', 'min' => 3, 'max' => 6],
                 ];
             }
         };

         $validator->load(['name' => 'superName']);

         $this->assertFalse($validator->isVasValidated());
         $this->assertFalse($validator->isValid(), 'start is isValid flag is wrong');
         $this->assertFalse($validator->isVasValidated());
         $this->assertTrue($validator->validate(), "class is not valid");
         $this->assertTrue($validator->isVasValidated());
         $this->assertTrue($validator->isValid(), 'after validate isValid is wrong');

         $this->assertEmpty($validator->getFirstErrorForAttribute('name'), 'error of name mast by null');
         // $this->assertEquals('Short field to long', $validator->getFirstErrorForField('shortString'));
    }

    /**
     * @return void
     * @throws ValidationException
     */
    public function testCallableParam()
    {
        $validator = new class extends Validation
        {
            public string $name = 'superName';

            public function rules(): array
            {
                return [
                    [['name'], 'string', 'min' => 7],
                    [['name'], [$this, 'nonStaticValidateMethod']],
                    [['name'], [ValidatorTest::class, 'functionForTestStaticCall']]
                    // [['shortString'], 'string', 'max' => 3, 'toLong' => 'Short field to long'],
                    // [['validString'], 'string', 'min' => 3, 'max' => 6],
                ];
            }

            public function nonStaticValidateMethod($value, string $field, CallableRule $rule): bool {
                return true;
            }
        };

        $this->assertFalse($validator->isVasValidated());
        $this->assertFalse($validator->isValid(), 'start is isValid flag is wrong');
        $this->assertFalse($validator->isVasValidated());
        $this->assertTrue($validator->validate(), "class is not valid");
        $this->assertTrue($validator->isVasValidated());
        $this->assertTrue($validator->isValid(), 'start is isValid flag is wrong');

    }

    /**
     * Test returned errors
     * @return void
     * @throws ValidationException
     */
    public function testReturnErrorsTest ()
    {
        $validator = new class extends Validation
        {
            public string $name = 'name';
            public string $value = 'value';

            public function rules(): array
            {
                return [
                    [['name'], 'string', 'min' => 7, 'toShort' => ':attribute to short'],
                    [['name'], [$this, 'nonStaticValidateMethod']],
                    [['name'], [ValidatorTest::class, 'functionForTestStaticCallAddError']],
                    [['shortString'], 'string', 'max' => 3, 'toLong' => 'Short field to long'],
                    [['validString'], 'string', 'min' => 3, 'max' => 6],
                ];
            }

            public function nonStaticValidateMethod($value, string $field, CallableRule $r): bool {
                $r->addError('error from nonStaticValidateMethod');
                return false;
            }
        };

        $this->assertFalse($validator->isVasValidated());
        $this->assertFalse($validator->isValid(), 'start is isValid flag is wrong');
        $this->assertFalse($validator->isVasValidated());
        $this->assertFalse($validator->validate(), "class is not valid");
        $this->assertTrue($validator->isVasValidated());
        $this->assertFalse($validator->isValid(), 'start is isValid flag is wrong');

        $errors = $validator->getErrors();
        $this->assertIsArray($errors, 'errors must be array');
        $this->assertCount(3, $errors, 'wrong errors count');

        $firstError = $validator->getFirstError();
        $this->assertIsString($firstError, 'error mast be string');
        $this->assertNotEmpty($firstError, 'error is empty');

        $attributeErrors = $validator->getErrorsForAttribute('name');
        $this->assertIsArray($attributeErrors, 'errors must be array');
        $this->assertCount(3, $attributeErrors, 'wrong errors count');

        $attributeFirstError = $validator->getFirstErrorForAttribute('name');
        $this->assertIsString($attributeFirstError);
        $this->assertNotEmpty($attributeFirstError);

        $shortStringErrors = $validator->getErrorsForAttribute('shortString');
        $this->assertIsArray($shortStringErrors, 'errors must be array');
        $this->assertCount(1, $shortStringErrors, 'wrong errors count');

        $shortStringFirstError = $validator->getFirstErrorForAttribute('shortString');
        $this->assertIsString($shortStringFirstError, 'errors must be array');
        $this->assertNotEmpty($shortStringFirstError, 'error is empty');
    }

    /**
     * @throws ValidationException
     */
    public function testValidatePartsOfFields ()
    {
        $validator = new class extends Validation
        {
            public string $name = 'name';
            public string $value = '';

            public function rules(): array
            {
                return [
                    [['name'], 'required', 'message' => 'name to short'],
                    [['value'], 'required'],
                    [['value2'], 'required'],
                ];
            }
        };

        $this->assertFalse($validator->isVasValidated());
        $result = $validator->validate(['name']);
        $this->assertTrue($validator->isVasValidated());
        $this->assertTrue($result);

        $this->assertTrue($validator->isVasValidated());
        $result = $validator->validate(['value']);
        $this->assertTrue($validator->isVasValidated());
        $this->assertFalse($result);
        $this->assertCount(1, $validator->getErrors());

        $this->assertTrue($validator->isVasValidated());
        $result = $validator->validate(['value2']);
        $this->assertTrue($validator->isVasValidated());
        $this->assertFalse($result);
        $this->assertCount(1, $validator->getErrors());

        $this->assertTrue($validator->isVasValidated());
        $result = $validator->validate(['name']);
        $this->assertTrue($validator->isVasValidated());
        $this->assertTrue($result);
        $this->assertEmpty($validator->getErrors());

        $this->expectException(ValidationException::class);
        $validator->validate(['string']);
    }

    /**
     * static test function
     * @return bool
     */
    public static function functionForTestStaticCall(): bool {
        return true;
    }

    /**
     * static test function but it add error
     * @param $value
     * @param string $attribute
     * @param CallableRule $r
     * @return bool
     */
    public static function functionForTestStaticCallAddError($value, string $attribute, CallableRule $r): bool {
        $r->addError('error form functionForTestStaticCallAddError');
        return false;
    }
}
