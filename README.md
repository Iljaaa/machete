# Machete

Another PHP validation library

Written for PHP 7.4. Not tested on version 8, but it must be fine

! Machete don't applies any prefilters on yore data and modified it. 

Dont use it with not pre cleared data, like $_GET, $_POST etc...

Machete don works with $_FILES data.

Contents:
- [installation](#installation)
- [How to use validation for forms](#how-to-use-validation-for-forms)
  - [Form validation class](#form-validation-class)
  - [Loading data in from](#loading-data-in-form)
  - [Getting data from form](#getting-data-from-form)
  - [Rules method](#rules-method)
  - [Drill to one rule row](#drill-to-one-rule-row)
- [Get validation error messages](#get-validation-error-messages)
- [Use rules as stand alone](#use-rules-as-stand-alone)
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
class YourFromValidationClass extends Validation
{
    public string $name = "";

    public function rules (): array
    {
        return [
            [['name', 'dateOfBirth'], 'required'],
            ['name', 'string', 'max' => 100],
            ['dateOfBirth', 'date'],
        ];
    }

}

$form = new YourFromValidationClass();
$form->load([
    'name' => 'Vlad Țepeș III',
    'dateOfBirth' => '1430-05-01'
]);

if ($form->validate()){
    // data is valid
}
else {
    $errors = $form->getErrors()
}


```

# Installation

Wia composer 

````
composer require iljaaa/machete
````

Or download it, define iljaaa/machete namespace in autoload und use

# How to use validation for forms

1. Create form class and extend it by Validate class ([more info](#form-validation-class))
2. [Override rules method](#rules-method)
3. [Load data in you class](#loading-data-in-form)
4. Call the validate() method. It return boolean result of validate

After validation use method [isValid](#validator-state-public-methods) for get validation result without check data, 
it's faster because the result of validations saved in state. 

Before you call validate the method isValid will always return false.

For check is was data in form checked before (was call of validate method) use [isVasValidated](#validator-state-public-methods)

## Form validation class

For create self from validation class just extend <b>\Iljaaa\Machete\Validation</b>

It's abstract class, and it has only one abstract method <b>rules()</b><br>
for return array of validation rules

```php
public function rules(): array;
```

Example:

```php
class FormValidation extends \Iljaaa\Machete\Validation 
{
    public function rules(): array 
    {
        return [
            ['attribute', 'rule', ... additional otions],
        ];
    }

} 
```

## Loading data in form

For load data in validation class use method <b>load</b>:

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

Rules method must return array of rules. Every rule is array of description one validation rule. 
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

Rule array looks like this:

```php
['attribute_name', 'rule', ... additonal params],
```

First element of array is form attribute name for validation. 
You may use array of names if you need to check any attributes 

```php
[['attribute_name', 'second_attribute'], 'rule', ... additonal params],
```

Second is rule name, you may use provided rules (like: string, int. in ....) or create self validation method 

Next are additional options, different for every rule


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

# Use rules as stand alone

Most rules can be used without form. 
Can create instance of validation rule, 
parameterize it and pas data to validate method.

Chapter of provided rules has examples of every rule.

# Provided rules

Rule short name set in rule described array in second position

- [required](#required)
- [string](#string)
- [int and float](#int-and-float)
- [date and datetime](#date-and-datetime)
- [in (in_array)](#in)
- [regex](#regex)

## required

Required rule use <b>empty()</b> function for check value. 
Be careful with zeros.

```php
['attribute',  'required', ....]
```

Rule additional params:

This rules doesn't have additional params.

Overridden error messages:

| Param   | type   | Are                                      | Default                |
|---------|--------|:-----------------------------------------|:-----------------------|
| message | string | error message on main check false result | :attribute is required |

When you override default error messages you can use named variables to be replaced by values in error message.

Replaced values:

<b>message</b>
- :attribute - from attribute name

<b>stand alone use</b>

Tell me any reason use it rule stand alone? Use empty() function 

## string

String rule use ms_strlen function for calculate string length.

Before other checks a value will be checked <b>is_string</b> function.   
And if it return a false result other checks of rule will be not run 

```php
['attribute',  'string',  ...]
```

Rule additional params:

| Param     | type   | Are                   | Default            |
|-----------|--------|:----------------------|:-------------------|
| min       | int    | minimum string length | null               |
| min       | int    | maximum string length | null               |

Overridden error messages:

| Param     | type   | Are                                         | Default                              |
|-----------|--------|:--------------------------------------------|:-------------------------------------|
| wrongType | string | error message if you try check not a string | :attribute has wrong type            |
| toShort   | string | error message if value shorter of min value | :attribute to short, min length :min |
| toLong    | string | error message if value longer of max value  | :attribute to long, max length :max  |

Replaced values:

<b>wrongType</b> 
- :attribute - from attribute name

<b>toShort</b> 
- :attribute - from attribute name
- :min - min len for check

<b>toLong</b> 
- :attribute - from attribute name
- :max - max len for check

<b>stand alone use</b>

```php
$result = (new StringRule())
    ->setMin(int $min)
    ->setMax(int $max)
    ->setWrongType(string $wrongType)
    ->setToShort(string $toShort);
    ->setToLong(string $toLong);
    ->validate($value, ?string $attribute = null, ?Validation $validation = null);
```

## int and float

If you use float validator and pas to validate function int value, it will be auto converted to float. 

For all other types and you get wrong type error and false validation result.  

```php
['attribute',  'float', ....]
['attribute',  'int', ....]
```

Additional params:

| Param | type      | Are           | Default for int | Default for float |
|-------|-----------|:--------------|:----------------|-------------------|
| min   | int/float | minimum value | null            | null              |
| min   | int/float | maximum value | null            | null              |

Overridden error messages:

| Param     | type   | Are                                                        | Default for int                      |
|-----------|--------|:-----------------------------------------------------------|:-------------------------------------|
| wrongType | string | error message if you try checked something with wrong type | :attribute has wrong type            |
| toSmall   | string | error message if value less min                            | :attribute to small, min length :min |
| toBig     | string | error message if value mere max                            | :attribute to big, max length :max   |

Replaced values:

<b>wrongType</b>
- :attribute - form attribute name

<b>toSmall</b>
- :attribute - form attribute name
- :min - min value for check

<b>toBig</b>
- :attribute - form attribute name
- :max - max value for check

<b>stand alone use</b>

```php
$result = (new IntRule())
    ->setMin (float $min)
    ->setMax(float $max)
    ->setWrongType(string $message)
    ->setToSmall(string $message);
    ->setToBig(string $message);
    ->validate($value, ?string $attribute = null, ?Validation $validation = null);
```

## date and datetime

Used for check form date and datetime field values or \DateTime objects

It's same validator with different date format:
- data: Y-m-d
- datetime: Y-m-d H:i

```php
['attribute',  'date', ....]
['attribute',  'datetime', ....]
```

Additional params:

| Param  | type                              | Are              | Default for date | Default for datetime |
|--------|-----------------------------------|:-----------------|------------------|:---------------------|
| format | string                            | Self date format | Y-m-d            | Y-m-d H:i            |
| min    | string in rule format / \DateTime | minimal date     | null             | null                 |
| min    | string in rule format / \DateTime | maximal date     | null             | null                 |

Overridden error messages:

| Param       | type   | Are                                                             | Default                           |
|-------------|--------|-----------------------------------------------------------------|:----------------------------------|
| wrongType   | string | error message if ypu try check something wrong                  | :attribute is not available value |
| wrongFormat | string | error message if value will be not converted from cerent format | :attribute has wrong :format      |
| beforeMin   | string | error message if value before min date                          | :attribute is before :min value   |
| afterMax    | string | error message if value after max date                           | :attribute is after :max value    |

Replaced values:

<b>wrongType</b>
- :attribute - from attribute name

<b>wrongFormat</b>
- :attribute - from attribute name
- :format - current format

<b>beforeMin</b>
- :attribute - form attribute name
- :min - mix date in current format

<b>afterMax</b>
- :attribute - form attribute name
- :max - max date in current format


<b>stand alone use</b>

```php
$result = (new DateTimeRule())
    ->setFormat (string $format)
    ->setMin (\DateTime $min)
    ->setMinAsString (string $min, ?string $format = null)
    ->setMax (\DateTime $max)
    ->setMaxAsString (string $max, ?string $format = null)
    ->setWrongType (string $wrongType)
    ->setWrongFormat (string $wrongFormat)
    ->setBeforeMin (string $beforeMin)
    ->setAfterMax (string $afterMax)
    ->validate($value, ?string $attribute = null, ?Validation $validation = null);
```
  

## in

In rule used <b>in_array</b> function in base

```php
['age',  'in', array $haystack, ...]
```

The third param mast be haystack array (or object with implementation of Traversable interface, need more tests) 
If it not correct you get wrong param exception.

Additional params:

| Param   | type   | Are                              | Default      |
|---------|--------|:---------------------------------|:-------------|
| strict  | bool   | strict flag in in_array function | false        |

Overridden error messages:

| Param   | type   | Default                 |
|---------|--------|:------------------------|
| message | string | :attribute not in array |

Replaced values:

<b>message</b>
- :attribute - from attribute name

<b>stand alone use</b>

```php
$result = (new InValidationRule())
    ->inArray($needle, $haystack, bool $strict = false);

or

$result = (new InValidationRule($haystack))
    ->validate($value, ?string $attribute = null, ?Validation $validation = null);

or

$result = (new InValidationRule())
    ->setHaystack(array $haystack)
    ->setMessage(string $message)
    ->setStrict(bool $strict)
    ->validate($value, ?string $attribute = null, ?Validation $validation = null);
```

## regex

Regex rule use filter_var function in base 

```php
filter_var($value, FILTER_VALIDATE_REGEXP, ["options" => ["regexp" => $regex]]);
```

```php
['attribute',  'regex', string $pattern, ...]
```

The third param mast be regex pattern.  
If you not set item or set wrong you get rule configuration exception.

Additional params:

Rule don't have additional params.

Overridden error messages:

| Param   | type   | Default                 |
|---------|--------|:------------------------|
| message | string | :attribute is not valid |

Replaced values:

<b>message</b>
- :attribute - from attribute name

<b>stand alone use</b> 

```php
$result = (new RegexValidationRule())
    ->isMatch($value, ?string $attribute = null, ?Validation $validation = null);

or

$result = (new RegexValidationRule(string $pattern))
    ->validate($value, ?string $attribute = null, ?Validation $validation = null);

or

$result = (new RegexValidationRule())
    ->setRegex(string $regexPattern)
    ->setMessage(string $message)
    ->validate($value, ?string $attribute = null, ?Validation $validation = null);

```

## Self validation functions

You can use callable object in validation form just pas in on second position of rule array

Validation will check return value of the object and if it returned false change form state on false.

If your function return not boolean value you get a validation exception. 

A few examples of create self validation functions:

```php
['attributeName', function () {}]
['attributeName', fn ($value, string $attribute, Rule $rule) => true]
['attributeName', [$this, 'publicOrProtectedMethod']]
['attributeName', [YoureClass::class, 'publicStaticMethod']]
```

Before object was called it will be checked by <b>is_callable</b> function<br>
If the is_callable function return a false result, you get false result of all validation and wrong type error.

Overridden error messages:

| Param     | type   | Default                                       |
|-----------|--------|:----------------------------------------------|
| wrongType | string | :attribute was checked by not callable object |


On validation your function will be call with params:

```php
function yourValidationFunction($value, string $attribute, CallableRule $rule): bool
```

Where:
- $value - it's value for check
- string $attribute - name of form attribute
- CallableRule $rule - instance of CallableRule class, it's wrapper for user callable functions

For add error message from callable function use rule object:
```php
function yourValidationFunction($value, string $attribute, CallableRule $rule): bool 
{
    $rule->addError('test error');
    return false;
}
```

stand alone use callable rule:

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

For create your own validation rule , 
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

If your value is invalid add string error to wrapper object, 
it also set validation result to false

```php
$userRuleWrapper->addError('test error');
```

Example of rule class:
```php
class YourRuleClass implements \Iljaaa\Machete\rules\UserRule 
{

    /**
     * @var array $config your rule config row
     */
    public function __construct (array $config)
    {
    
    }

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

If you want know was a form validated and what is result of validation do something like this:

```php
if ($form->isVasValidate() && $form->isValid() == false) echo "Form is not valid" 
```

If you want know was a form loaded and validated. And if attribute has an error display it
```php
if ($form->isVasValidate() && $form->isAttributeValid('attribute') == false) {
    echo $form->getFirstErrorForAttribute("attribute")
} 
```

# Validator state public methods

```php
/**
 * Is was validate method call 
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
- test in rule with \Traversable
- id? unsigned int 
- array
- associated array
- array of accosted arrays
- additional options in self rules
- think about split int and float on different validators vis its works with different types
- think about static cache of fields validation state in validator for speed up 
