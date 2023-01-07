# Machete

Another PHP validation library

Written for PHP 7.4. Not tested on version 8, but he must be fine

! Machete don't applies any prefilters on yore data and modified it. 

Dont use it with not pre cleared data, like $_GET, $_POST etc...

Machete don works with $_FILES data.

Contents:
- [installation](#installation)
- [How to use validation for forms](#how-to-use-validation-for-forms)
  - [Form validation class](#form-validation-class)
  - [Loading and getting data in from validation class](#loading-data-in-form)
  - [Getting data from form](#getting-data-from-form)
  - [Rules method](#rules-method)
  - [Drill to one rule row](#drill-to-one-rule-row)
- [Get validation error messages](#get-validation-error-messages)
- [Manual use rules](#manual-use-rules)
- [Provided rules](#provided-rules)
  - [required](#required)
  - [string](#string)
  - [int and float](#int-and-float)
  - [date and datetime](#date-and-datetime)
  - [in (in_array)](#in)
  - [regex](#regex)
  - [Self validation functions](#self-validation-functions)
- [Self rule](#self-rule)
- [Use form state in views](#use-form-state-in-views)
- [Validator state public methods](#validator-state-public-methods)

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

# Installation

Wia composer 

````
composer require iljaaa/machete
````

Or download it, define iljaaa/machete namespace in autoload und use

# How to use validation for forms

1. Create your class and extend by Validate class ([more info](#form-validation-class))
2. [Override rules() method](#rules-method)
3. [Load data in you class](#loading-data-in-form)
4. Call validate() method e.c. method validate return boolean result of validate

After validation use method isValid() for get validation result without check data, 
it's faster because of result of validations saved in static method. 

Before you call validate() method isValid() will always return false.

For check was data in form checked before (was call of validate method) use isVasValidated()

## Form validation class

For create self from validation class just extend <b>\Iljaaa\Machete\Validation</b>

It's abstract class, and it has only one abstract method <b>rules()</b><br>
for return array ob validation rules

```php
public function rules(): array;
```

Here simple example for most popular situations

```php
class FormValidation extends \Iljaaa\Machete\Validation 
{
    public function rules(): array 
    {
        return [
            ['firstAttribute', 'string', 'max' => 255, 'toLong' => 'Name to long'],
            ['secondAttribute', 'int', 'max' => 255, 'toBig' => ':attribute to big'],
        ];
    }

} 

$form = new FormValidation();
$forn->load($_GET);
$result = $form->validate();
$errors = $form->getErrors();
```

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
```

## Loading data in form

For load data in validation class use method load:
```php
public function load(array $data): void;
```

Data will save in internal storage. 

Set one item value
```php
public function setValue (string $name, $value): void
```

Or use magic (it will be call getValue in base):
```php
$form->yourDataKey = $value;
```

## Getting data from form

To get data from storage use:
```php
public function getData(): array;
```

Get one item from data:
```php
public function getValue (string $name): mixed
```

or use magic (it will be call getValue in base):
```php
$it = $form->yourDataKey;
```

You can validate protected and public attributes of child validation class.<br> 

If you define named attribute in child class, and use any of the methods from the loading methods, 
value will be not putted in internal storage it will be saved to your attribute

Example

```php
class FormValidation extends \Iljaaa\Machete\Validation 
{
    public string $name = ''; 
    
    public function rules(): array 
    {
        return [
            ['name', 'string'],
            ['number', 'int'],
        ];
    }
} 

$form = new FormValidation();
$forn->load([
    'name' => 'Ilja', // this values will be written to $form->name
    'number' => 1 // this to internal storage
]);

```

## Rules method

Rules method mast return array of named arrays. Every row is array of description one validation rule. 
It must be in same syntax

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

## Drill to one rule row

Rule named array looks like this:
```php
['attribute_name', 'validator_name', ... additonal params],
```

First element of array is form attribute name for validation. You may use array of names if you need to check any attributes 

Second is validator name, you may use provided rules (like: string, int. in ....) or create self validation method 

Next are additional options different for every rule

# Get validation error messages


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

# Manual use rules

Most rules can be used without form. 
Can create instance of validation class, 
parameterize it and pas data to validate method.

Chapter of provided rules has full examples of every rule.

# Provided rules

Rule short name set in rule described array in second position

- [required](#required)
- [string](#string)
- [int and float](#int-and-float)
- [date and datetime](#date-and-datetime)
- [in (in_array)](#in)
- [regex](#regex)

## required

Required rule use empty() function for check value.

```php
['attribute',  'required', ....]
```

Form one rule row attributes:

| Param   | type   | Are           | Default                |
|---------|--------|:--------------|:-----------------------|
| message | string | error message | :attribute is required |

<b>manual use</b>

Tell me any reason use it stand alone? Use empty() function 

## string

String rule use ms_strlen function for calculate string length.

Before other checks value will be checked <b>is_string</b> function.   
And if return is false, you get false result of validation and one wrong type error

```php
['attribute',  'string',  ...]
```

additional params: 

| Param     | type   | Are                                      | Default            |
|-----------|--------|:-----------------------------------------|:-------------------|
| min       | int    | minimum string length for check          | null               |
| min       | int    | maximum string                           | null               |

Error messages for override:

| Param     | type   | Are                                      | Default            |
|-----------|--------|:-----------------------------------------|:-------------------|
| wrongType | string | error message if you try check no string | It's not a string  |
| toShort   | string |                                          | To short           |
| toLong    | string |                                          | To long            |

When you override default error messages you can use named variables to be replaced by values in error message

<b>wrongType</a> 
- :attribute - from attribute name

<b>toShort</a> 
- :attribute - from attribute name
- :short - min len for check: example: ":attribute, min :short chars length"

<b>toLong</a> 
- :attribute - from attribute name
- :long - max len for check: example: ":attribute, max :long chars length"

<b>manual use</b>

```php
$result = (new StringRule())
    ->setMin($minIntOfFloat)
    ->setMax($maxIntAndFloat)
    ->setWrongType('value in not a string')
    ->setToShort('value to short');
    ->setToLong('value to long');
    ->validate($needle);
```

## int and float

If you use float validator and pas to validate function int value, it will be auto converted to float. 
All other types will be not converted, and you get wrong type error and false validation result  

```php
['attribute',  'float', ....]
['attribute',  'int', ....]
```

Additional params:

| Param     | type      | Are                       | Default for int  | Default for float |
|-----------|-----------|:--------------------------|:-----------------|-------------------|
| min       | int/float | minimum value for check   | null             | null              |
| min       | int/float | maximum value for check   | null             | null              |

Error messages:

| Param     | type      | Are                       | Default for int  | Default for float |
|-----------|-----------|:--------------------------|:-----------------|-------------------|
| wrongType | string    | Wrong type error message  | It is not an int | It is not a float |
| toSmall   | string    | value < min error message | To small         |                   |
| toBig     | string    | value > max error message | To big           |                   |

<b>wrongType</a>
- :attribute - from attribute name

<b>toSmall</a>
- :attribute - from attribute name
- :short - min len for check: example: ":attribute, min :short chars length"

<b>toBig</a>
- :attribute - from attribute name
- :long - max len for check: example: ":attribute, max :long chars length"

<b>manual use</b>

```php
$result = (new InValidationRule())
    ->setMin($minIntOrFloatValue)
    ->setMax($maxIntOrFloatValue)
    ->setWrongType('wrong type error message')
    ->setToSmall('value to small');
    ->setToBig('value to big');
    ->validate($needle);
```

## date and datetime

Used for check form datetime field values and \DateTime objects

Same validator with different format:
- data: Y-m-d H:i
- datetime: Y-m-d

Additional params:

| Param  | type                              | Are                     | Default for date | Default for datetime |
|--------|-----------------------------------|:------------------------|------------------|:---------------------|
| format | string                            | Date or datetime format | Y-m-d            | Y-m-d H:i            |
| min    | string in rule format / \DateTime | minimal date for check  | null             | null                 |
| min    | string in rule format / \DateTime | maximal date for check  | null             | null                 |

Error messages

| Param       | type   | Default                           |
|-------------|--------|:----------------------------------|
| wrongType   | string | :attribute is not available value |
| wrongFormat | string | :attribute has wrong :format      |
| beforeMin   | string | :attribute is before :min value   |
| afterMax    | string | :attribute is before :max value   |

## in

In rule just used <b>in_array</b> function in base

```php
['age',  'in', array $aystack, ...]
```

The third param mast be array or object with implementation of Traversable interface. 
If it not you get rule wrong param exception.

additional params:

| Param   | type   | Are                              | Default      |
|---------|--------|:---------------------------------|:-------------|
| strict  | bool   | strict flag in in_array function | false        |

Error messages:

| Param   | type   | Are                              | Default      |
|---------|--------|:---------------------------------|:-------------|
| message | string | error message                    | Not in array |


manual use
--

```php
$result = (new InValidationRule())
    ->inArray($needle, $haystack);

or

$result = (new InValidationRule($haystack))
    ->validate($needle);

or

$result = (new InValidationRule())
    ->setHaystack(array|\Traversable $haystack)
    ->setMessage(string $message)
    ->setStrict(bool $strict)
    ->validate(mixed $value);
```

## regex

Regex rule use filter_var function in base 

```php
filter_var($value, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => $regex]]);
```

```php
['attribute',  'regex', string $pattern, ...]
```

The third param mast be regex pattern.  If you not set item or set wrong you got rule configuration exception.

Rule don't have additional params.

Error messages:

| Param   | type   | Are                              | Default      |
|---------|--------|:---------------------------------|:-------------|
| message | string | error message                    | Not in array |

<b>manual use<b> 

```php
$result = (new RegexValidationRule())
    ->isMatch(string $pattern, string $value);

or

$result = (new RegexValidationRule(string $pattern))
    ->validate(string $value);

or

$result = (new RegexValidationRule())
    ->setRegex(string $regexPattern)
    ->setMessage(string $message)
    ->validate($value);

```

## Self validation functions

You can use callable object in validation form just pas in on second position of rule description array

Validation will check return value of object and if returned false change form state on false.

If your function return not boolean value you get a validation exception 

A few methods for create self validation functions, validation rules and 
form validation classes

```php
['attributeName', function () {}]
['attributeName', fn ($value, string $attribute, Rule $rule) => true]
['attributeName', [$this, 'publicOrProtectedMethod']]
['attributeName', [YoureClass::class, 'publicStaticMethod']]
```

Second parameter in rule description row can be any callable object

Before object was called it will be checked by <b>is_callable<b> function<br>
If is_callable function return false, you get false as validation result and wrongType error.

Error messages

| Param     | type   | Default                       | Default |
|-----------|--------|:------------------------------|:--------|
| wrongType | string | wrong type of callable object |         |


On validation your function will be called width params:

```php
function yourValidationFunction($value, string $attribute, CallableRule $rule): bool
```

Where:
- $value - it's value for check
- string $attribute - name of form attribute
- CallableRule $rule - instance of CallableRule class, wrapper for user callable functions

For add error message from callable function use rule object
```php
function yourValidationFunction($value, string $attribute, CallableRule $rule): bool 
{
    $rule->addError('test error');
    return false;
}
```

Manual use callable rule:
```php
(new CallableRule(fn ($value, string $attribute, CallableRule $r) => true))
    ->validate($value);

(new CallableRule()
    ->setCallable(fn ($value, string $attribute, CallableRule $r) => true)
    ->setAttributeName('testAttribute')
    ->setWrongType('wrong callable object type')
    ->validate($yourValue);
```
 
# Self rule

For create your own validation rule class, 
you need create class and implements <b>\Iljaaa\Machete\rules\UserRule</b> interface.

Then you can use rule in validator lite this:

```php
['attributeName', 'rule', YourRuleClass::class]
```

Rule interface has only one method:
```php
public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool;
```

Where:

- $value - it's your value for check
- string $attribute - name of form attribute
- UserRuleWrapper $userRuleWrapper - this is instance of the special interface for wrap all user rules
- Validation $validation - validation from instance

If your value is invalid add string error to wrapper object, it also set validation result to false

```php
$userRuleWrapper->addError('test error');
```

Example of rule class:
```php
class YourRuleClass implements \Iljaaa\Machete\rules\UserRule 
{

    public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool
    {
        if (empty($value)) 
        {    
            $userRuleWrapper->addError('test error');
            return false;
        }

        return true;
    }
}
```

Use your class in validation:

```php
class FormValidation extends \Iljaaa\Machete\Validation 
{

    public string $myAttribute = 'test';
            
    public function rules(): array 
    {
        return [
            ['myAttribute', 'rule', YourRuleClass::class]
        ];
    }

} 
```

# Use form state in views

If you want now it was form validated and result of validation do something like this:
```php
if ($form->isVasValidate() && $form->isValid() == false) echo "Form is not valid" 
```

If you want now is vas form loaded and check. And if attribute has error display it
```php
if ($form->isVasValidate() && $form->isAttributeValid('attribute') == false) {
    echo $form->getFirstErrorForAttribute("attribute")
} 
```

# Validator state public methods

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


----------------------


To do:
- update fields errors
  - string
  - int & float
  - in
  - callable
- manual use
  - date & datetime
  - in
- getters in regex rule
- array
- associated array
- array of accosted arrays
- think about static cache of fields validation state in validator for speed up 
