# machete
Another validation library

PHP version 7.4, not tested on version 8. but he must be fine

! Machete don't applies any pre filters on yore data and modified it. 
Dont use it with not pre cleared data, like $_GET, $_POST etc...
Machete don works with $_FILES data.

Full example:
```php
todo: finish it
public function rules (): array
{
    return [
        ['firstname', 'string', 'min' => 22],
        [['lastname'], [$this, 'myCustomValidationMethod'], 'message' => 'Replaced error message'],
        [['age'],  'number', 'min' => 18, 'max' => 100]
    ];
}
```

Installation
==

Wia composer 

````
composer require iljaaa/machete
````

Or download it, define iljaaa/machete namespace in autoload und use it 

How to use validation

1. Create class and extend by Validate class
2. Override rules() method
3. Load data in you class
4. Call validate() method e.c. method validate return boolean result of validate

After validation use method isValid() for get validation result without check data it's most faster becouse of result 
of validations saved in static method. 

Before you call validate() method isValid() allways return false. 

For check is data if form vas checked use isVasValidated()

Loading data in class
==

You cam validate protected and public attributes of validation class. 
You need put value for validate in this attribute and call validate method()

If attribute is not defined in class and when you use 

```php
$yourClass->yourAttributeName = "value";
```

value will be putted in internal storage and you can get it from there by attribute name
```php
$value = $yourClass->yourAttributeName;
```

You can do not describe any attributes in your class ann load all or a part data is storage 
```php
$form->load($_GET);
```

Validator state public methods
==

```php
/**
 * is vas validate method call 
 */
public function isVasValidated(): bool
```

Return answer on question: is form was validated before?

```php
/**
 * Is data valid 
 */
public function isValid(): bool
```

```php
/**
 * Is attribute valid
 */
public function isAttributeValid (string $attribute): bool
```

Methods isValid() and isAttributeValid() always return false before you call validate() method   


Rules method
==
Its the only one abstract method to be implemented.

Rules method mast return array of named arrays, every named array descript one validation rule. It mas be in save sintaxis

```php
public function rules(): array 
{
    return [
        ['attribute', 'validator'],
        ['name', 'required'],
        ['phone', 'string', 'max' => 100],     
    ];
}
```

Drill to one rule row
==
Rule named array looks like this:
```php
['attribute_name', 'validator_name', ... additonal params],
```

First element of array is form attribute name for validation. You may use array of names if you need to check any attributes 

Second is validator name, you may use provided rules (like: string, int. in ....) or create self validation method 

Next are additional options differend for every rule

Get validation error messages
==

```php
/**
 * Array of errors grouped by attribute 
 */
public function getErrors(): array
```

```php
/**
 * First found error 
 */
public function getFirstError(): string
```

```php
/**
 * Errors array for attribute 
 */
public function getErrorsForAttribute(string $attribute): array
```

```php
/**
 * First found error for attribute
 */
public function getFirstErrorForAttribute(string $attribute): string
```

Manual use rules
==

Validation rules can be used without form. You can create instance of validation class, 
parameterize it and pas data to validate method.

Chapter of provided rules has full examples of every rule.

Provided rules
==

Rule short name set in rule described array is second position

required
--

required ruse use empty() function for check value

additional params:

| Param   | type   | Are           | Default       |
|---------|--------|:--------------|:--------------|
| message | string | error message | It's required |


string 
--

String rule use ms_strlen function for calculate string length.<br />

Before check other rules string rule check values is_string function. 
And if it return false, you get false result of validation and error from 'wrongType' param

string config array

| Param     | type   | Are                                      | Default            |
|-----------|--------|:-----------------------------------------|:-------------------|
| min       | int    | minimum string length for check          | null               |
| min       | int    | maximum string                           | null               |
| wrongType | string | error message if you try check no string | It's not a string  |
| toShort   | string |                                          | To short           |
| toLong    | string |                                          | To long            |

When you override default error messages you can use named variables who vas be replaced

<b>wrongType</a> 
- :attribute - from attribute name

<b>toShort</a> 
- :attribute - from attribute name
- :short - min len for check: example: ":attribute, min :short chars length"

<b>toShort</a> 
- :attribute - from attribute name
- :long - max len for check: example: ":attribute, min :long chars length"

manual use
--

```php
$result = (new StringRule())
    ->setMin($minIntOfFloat)
    ->setMax($maxIntAndFloat)
    ->setWrongType('value in not a string')
    ->setToShort('value to short');
    ->setToLong('value to long');
    ->validate($needle);
```


int & float
==

If you use float validator and pas to validate function int value, 
it vas auto converted to int. All other types will be not coverted 
and you get wrong type error  

```php
['attribute',  'float', ....]
['attribute',  'int', ....]
```

| Param     | type   | Are                             | Default for int    | Default for float |
|-----------|--------|:--------------------------------|:-------------------|-------------------|
| min       | int    | minimum string length for check | null               |                   |
| min       | int    | maximum string                  | null               |                   |
| wrongType | string |                                 | It is not a string | It is not a float |
| toSmall   | string |                                 | To small           |                   |
| toBig     | string |                                 | To big             |                   |

manual use
--

```php
$result = (new InValidationRule())
    ->setMin($minIntOfFloat)
    ->setMax($maxIntAndFloat)
    ->setToSmall('value to small');
    ->setToBig('value to big');
    ->validate($needle);
```

date and datetime
==

Used for check from datetime field values and \DateTime 
objects

Same validator with diffrents format
- data:
- datetime: 


| Param     | type      | Are          | Default value |
|-----------|-----------|:-------------|:--------------|
| min       | \DateTime | minimal date | null          |
| min       | \DateTime | maximal date | null          |

Error messages

| Param       | type   | Default                           |
|-------------|--------|:----------------------------------|
| wrongType   | string | :attribute is not available value |
| wrongFormat | string | :attribute has wrong :format      |
| beforeMin   | string | :attribute is before :min value   |
| afterMax    | string | :attribute is before :max value   |

in
==
synthesis of config array
```php
['age',  'in', ['array', 'iterator', 'traversableObject']]
```

the third param mast be array or object with implementation of Traversable

additional params:

| Param   | type   | Are                              | Default      |
|---------|--------|:---------------------------------|:-------------|
| message | string | error message                    | Not in array |
| strict  | bool   | strict flag in in_array function | false        |

manual use
--

```php
$result = (new InValidationRule())->inArray($needle, $haystack);

or

$result = (new InValidationRule($haystack))->validate($needle);

or

$result = (new InValidationRule())->setHaystack($haystack)->validate($needle);

```

regex
==

synthesis of config array
```php
['attribute',  'regex', 'pattern', ...adiitonalParams]
```

additional params:

| Param   | type   | Are                              | Default      |
|---------|--------|:---------------------------------|:-------------|
| message | string | error message                    | Not in array |

manual use validator: 
```php
$result = (new RegexValidationRule())->isMatch($regexPattern, $variable);

or

$result = (new RegexValidationRule($regexPattern))->validate($needle);

or

$result = (new RegexValidationRule())->setHaystack($regexPattern)->validate($needle);

```

Self validation functions
==

Callable
--

Second paramether can be callable
like this
['name', function () {}]

or
['name', fn ($value, string $attribute, Rule $rol) => true, ....]


| Param     | type   | Are                           | Default |
|-----------|--------|:------------------------------|:--------|
| message   | string | error message                 |         |
| wrongType | string | wrong type of callable object |         |

Use any callable for self made validation rule function.

Describe it as second parameter is callable object, it vas checked is_callable function 
and if it false return false result and add wrong type error to form state

Callable function must return a boolean value. 
Validation check return values and if it false add error message to form state and change summary
validation state on false. 

This object was be called with params

```php
yoreValidationFunction($value, ?string $attribute, Rule $rule): bool
```

Where:
- $value - its value for check
- string $attribute - name of checked attribute
- Rule $rule - instance of CallableValidationRule class

Manual use:
```php
$result = (new CallableRule($callableObject))->validate($value);
or
$result = (new CallableRule())->setCallable($callableObject)->validate($value);
```

Self rule
--

For create your own validation rule class, 
you need create class and implements <b>\Iljaaa\Machete\rules\UserRule</b> interface

Then you can use rule in validator lite this:

```php
['attribure', 'rule', YourRuleClass::class]
```

Rule interface has only one method
```php
public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool;
```

Where:

- $value - its value for check
- string $attribute - name of checked form attribute
- UserRuleWrapper $userRuleWrapper - this is instance of of special interface for wrap all user rules
- Validation $validation - validation from instance

If your value is invalid add string error to wrap and it also set validation result to false

```php
$userRuleWrapper->addError('test error');
```



Example of rule class:
```php
class YourRuleClass implements \Iljaaa\Machete\rules\UserRule {

    public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool
    {
        if (empty($value)) 
        {    
            $userRuleWrapper->addError('test error');
            return false;
        }
;
        return true;
    }
}
```

Self form validation class
--

For create self class just extend <b>\Iljaaa\Machete\Validation</b>

It's abstract class and it has one abstract method <b>rules(): array</b>
for return array ob validation rule

Full example self rule in self class:
```php
class FormValidation extends \Iljaaa\Machete\Validation {

    public string $myAttribute = 'test';
            
    public function rules(): array {
        return [
            ['myAttribute', 'rule', YourRuleClass::class]
        ];
    }
    
} 

class YourRuleClass implements \Iljaaa\Machete\UserRule {

    public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool
    {
        if (empty($value)) 
        {    
            $userRuleWrapper->addError('test error');
            return false;
        }
;
        return true;
    }
}
```

Use form state in views
==

If you want now is was form validated and result of validation do somthing like this:
```php
if ($form->isVasValidate() && $form->isValid() == false) echo "Form is not valid" 
```

If you want now is vas form loaded and check. And if attribute has error display it 
```php
if ($form->isVasValidate() && $form->isAttributeValid('attribute') == false) {
    echo $form->getFirstErrorForAttribute("attribute")
} 
```

To do:
- assert attribute for create form function
- update fields errors
- array
- associated array
- array of accosiated arrays
- think about static cache of fields validation state in validator for speed up 
