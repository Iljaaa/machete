<?php

use Iljaaa\Machete\exceptions\ValidationException;

require(__DIR__.'/../vendor/autoload.php');

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     */
     public function testLoadAttributes ()
     {
         $validator = new class extends \Iljaaa\Machete\Validation
         {
             public string $string = '';

             public function rules (): array
             {
                 return [
                     [['string'], 'required', 'message' => 'String is reqqqqured'],
                     [['number'], 'required'],
                     [['notset'], 'required'],
                     [['valid'], 'required'],
                 ];
             }
         };

        $validator->load ([
            'string' => ' ',
            'number' => 0,
            'valid' => 'aaaaa'
        ]);


         $this->assertFalse($validator->isValid(), 'is validate flag wrong');
         $this->assertFalse($validator->validate());
         $this->assertFalse($validator->isValid());
         $this->assertTrue($validator->isValid(), 'is validate flag wrong 2');

         $this->assertFalse($validator->isFieldValid('string'));
         $this->assertFalse($validator->isFieldValid('number'));
         $this->assertFalse($validator->isFieldValid('notset'));
         $this->assertTrue($validator->isFieldValid('valid'));

         $this->assertEquals('String is reqqqqured', $validator->getFirstErrorForField('string'));
         $this->assertEquals('number required', $validator->getFirstErrorForField('number'));
         $this->assertEquals('notset required', $validator->getFirstErrorForField('notset'));
         $this->assertEmpty($validator->getFirstErrorForField('valid'));
     }


    /**
     *
     *
     * @throws ValidationException
     */
     public function testClassParams ()
     {
         $validator = new class extends \Iljaaa\Machete\Validation
         {
             public string $name = 'sdkjasasdasd';

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
         $this->assertTrue($validator->validate(), "class is not valid");
         $this->assertTrue($validator->isValid(), 'after validate isValid is wrong');

         $this->assertNull($validator->getFirstErrorForField('name'), 'error of name mast by null');
         // $this->assertEquals('Short field to long', $validator->getFirstErrorForField('shortString'));
    }



    /**
     *
     *
     * @throws ValidationException
     */
     public function testLoadParams ()
     {
         $validator = new class extends \Iljaaa\Machete\Validation
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

         $validator->load(['name' => 'asdfasdfasf']);

         $this->assertFalse($validator->isValid(), 'start is isValid flag is wrong');
         $this->assertTrue($validator->validate(), "class is not valid");
         $this->assertTrue($validator->isValid(), 'after validate isValid is wrong');

         $this->assertNull($validator->getFirstErrorForField('name'), 'error of name mast by null');
         // $this->assertEquals('Short field to long', $validator->getFirstErrorForField('shortString'));
    }

    public function testCallable()
    {

        $validator = new class extends \Iljaaa\Machete\Validation
        {
            public string $name = 'sdkjasasdasd';

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

            public function nonStaticValidateMethod($value, string $field, \Iljaaa\Machete\rules\Rule $r): bool {
                return true;
            }
        };

        $this->assertFalse($validator->isValid(), 'start is isValid flag is wrong');
        $this->assertTrue($validator->validate(), "class is not valid");
        $this->assertTrue($validator->isValid(), 'start is isValid flag is wrong');

    }

    public static function functionForTestStaticCall(): bool {
        return true;
    }

    /**
     * @return void
     */
    public function testUnknownStringValidator ()
    {
        $this->expectException(ValidationException::class);

        $validator = new class extends \Iljaaa\Machete\Validation {
            public function rules (): array
            {
                return [
                    [['name'], 'unknownValidatorName'],
                ];
            }
        };

        $validator->validate();
    }
}
