# Machete

Another PHP validation library

Written for PHP 7.4. Not tested on version 8, but it must be fine

! Machete doesn't apply any prefilters on your data and modify it. 

Don't use it without pre cleared data, like $_GET, $_POST etc...

Machete doesn't work with $_FILES data.

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

Via composer 

````
composer require iljaaa/machete
````

Or download it, define iljaaa/machete namespace in autoload and use

# How to use validation for forms

1. Create form class and extend it by Validate class ([more info](#form-validation-class))
2. [Override rules method](#rules-method)
3. [Load data in your class](#loading-data-in-form)
4. Call the validate() method. It returns the boolean result of the validation

After validation use method [isValid](#validator-state-public-methods) for get validation result without check data, 
it's faster because the result of validations saved in state. 

Before you call validate the method isValid will always return false.

To check: has the data in the form checked before? (was there call of the validate method) use [isVasValidated](#validator-state-public-methods)

## Form validation class

To create your own form of the validation class, just extend <b>\Iljaaa\Machete\Validation</b>

It's an abstract class, and it has only one abstract method <b>rules()</b><br>
to return array of validation rules

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
            ['attribute', 'rule', ... additional options],
        ];
    }

} 
```

## Loading data in the form

To load data in the validation class use method <b>load</b>:

```php
public function load(array $data): void;
```

Data will be saved in internal storage. 

Set one item value

```php
public function setValue (string $name, $value): void
```

Or use magic (it will call setValue in base):

```php
$form->yourDataKey = $value;
```

## Getting data from the form

To get data from the internal storage use:

```php
public function getData(): array;
```

Get one item from data:

```php
public function getValue (string $name): mixed
```

or use magic (it will call getValue in base):

```php
$it = $form->yourDataKey;
```

You can validate protected and public attributes of the child validation class.<br> 

If you define named attribute in child class, and use any method from the loading methods, 
value will be not put in the internal storage it will be saved to your attribute

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
    'name' => 'Ilja', // this value will be written to $form->name
    'number' => 1 // this to internal storage
]);

```

## Rules method

Rules method must return an array of rules. Every rule is an array of description one validation rule. 
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
['attribute_name', 'rule', ... additonal parameters],
```

The First element of an array is a form attribute name for the validation. 
You may use an array of names if you need to check more then one an attribute 

```php
[['attribute_name', 'second_attribute'], 'rule', ... additonal parameters],
```

The second is the rule name, you may use provided rules (like: string, int. in ....) 
or create self validation method 

Next goes are an additional options, different for every rule


# Get validation error messages


```php
/**
 * Array of errors grouped by attribute 
 */
public function getErrors(): array
```

```php
/**
 * First error found  
 */
public function getFirstError(): string
```

```php
/**
 * Array of errors for attribute 
 */
public function getErrorsForAttribute(string $attribute): array
```

```php
/**
 * First error found for attribute
 */
public function getFirstErrorForAttribute(string $attribute): string
```

# Use rules as stand alone

Most rules can be used without the form. 
Can create instance of validation rule, 
parameterize it and pass data to validate method.

Chapter of provided rules has examples of every rule.

# Provided rules

Rule short name must be described in the second position of the rule array

- [required](#required)
- [string](#string)
- [int and float](#int-and-float)
- [date and datetime](#date-and-datetime)
- [in (in_array)](#in)
- [regex](#regex)

## required

Required rule use <b>empty()</b> function to check the value. 
Be careful with zeros.

```php
['attribute',  'required', ....]
```

Rule additional parameters:

This rule doesn't have additional parameters.

Overridden error messages:

| Param   | type   | Are                                                 | Default                |
|---------|--------|:----------------------------------------------------|:-----------------------|
| message | string | error message on the main check of the false result | :attribute is required |

When you override default error messages you can use named variables to be replaced by values in the error message.

Replaced values:

<b>message</b>
- :attribute - from attribute name

<b>stand alone use</b>

Tell me any reason to use this rule standalone? Use empty() function 

## string

String rule use ms_strlen function to calculate the string length.

Before other checks a value will be checked with <b>is_string</b> function.   
And if it returns a false result, other checks of rule will not run 

```php
['attribute',  'string',  ...]
```

Rule additional parameters:

| Param     | type   | Are                   | Default            |
|-----------|--------|:----------------------|:-------------------|
| min       | int    | minimum string length | null               |
| min       | int    | maximum string length | null               |

Overridden error messages:

| Param     | type   | Are                                                | Default                              |
|-----------|--------|:---------------------------------------------------|:-------------------------------------|
| wrongType | string | error message if you try to check not a string     | :attribute has wrong type            |
| toShort   | string | error message if value is shorter than a min value | :attribute to short, min length :min |
| toLong    | string | error message if value is longer than a max value  | :attribute to long, max length :max  |

Replaced values:

<b>wrongType</b> 
- :attribute - from attribute name

<b>toShort</b> 
- :attribute - from attribute name
- :min - min len to check

<b>toLong</b> 
- :attribute - from attribute name
- :max - max len to check

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

If you use float validator and pass to validation 
function int value, it will be auto converted to float. 

For all other types and you get the wrong type error and false validation result.  

```php
['attribute',  'float', ....]
['attribute',  'int', ....]
```

Additional parameters:

| Param | type      | Are           | Default for int | Default for float |
|-------|-----------|:--------------|:----------------|-------------------|
| min   | int/float | minimum value | null            | null              |
| min   | int/float | maximum value | null            | null              |

Overridden error messages:

| Param     | type   | Are                                                         | Default for int                      |
|-----------|--------|:------------------------------------------------------------|:-------------------------------------|
| wrongType | string | error message if you try to check something with wrong type | :attribute has wrong type            |
| toSmall   | string | error message if value is less then a min value             | :attribute to small, min length :min |
| toBig     | string | error message if value is mere then a max value             | :attribute to big, max length :max   |

Replaced values:

<b>wrongType</b>
- :attribute - form attribute name

<b>toSmall</b>
- :attribute - form attribute name
- :min - min value to check

<b>toBig</b>
- :attribute - form attribute name
- :max - max value to check

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

Used to check form date and datetime field values or \DateTime objects

It's same validator with different date format:
- data: Y-m-d
- datetime: Y-m-d H:i

```php
['attribute',  'date', ....]
['attribute',  'datetime', ....]
```

Additional parameters:

| Param  | type                               | Are              | Default for date | Default for datetime |
|--------|------------------------------------|:-----------------|------------------|:---------------------|
| format | string                             | Self date format | Y-m-d            | Y-m-d H:i            |
| min    | string in rule format or \DateTime | minimal date     | null             | null                 |
| min    | string in rule format or \DateTime | maximal date     | null             | null                 |

Overridden error messages:

| Param       | type   | Are                                                              | Default                           |
|-------------|--------|------------------------------------------------------------------|:----------------------------------|
| wrongType   | string | error message if ypu try to check something wrong                | :attribute is not available value |
| wrongFormat | string | error message if value will not be converted from current format | :attribute has wrong :format      |
| beforeMin   | string | error message if value is before than a min date                 | :attribute is before :min value   |
| afterMax    | string | error message if value is after than a max date                  | :attribute is after :max value    |

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

The third parameter must be a haystack array (or an object with implementation of Traversable interface, more tests a needed) 
If it is not correct you get wrong parameter exception.

Additional parameters:

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

The third parameter must be a regex pattern.  
If you not set item or set wrong you get rule configuration exception.

Additional parameters:

Rule doesn't have additional parameters.

Overridden error messages:

| Param   | type   | Default                 |
|---------|--------|:------------------------|
| message | string | :attribute is not valid |

Replaced values:

<b>message</b>
- :attribute - from attribute name

<b>standalone use</b> 

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

You can use callable object in validation form just pass it to second position of the rule array

Validation will check return value of the object and if it returns false, 
the validation will change form state to false.

If your function doesn't returns a boolean value you will get a validation exception. 

A few examples of creating self validation functions:

```php
['attributeName', function () {}]
['attributeName', fn ($value, string $attribute, Rule $rule) => true]
['attributeName', [$this, 'publicOrProtectedMethod']]
['attributeName', [YoureClass::class, 'publicStaticMethod']]
```

Before the object is called it will be checked by <b>is_callable</b> function<br>
If the is_callable function returns a false result, 
you will get the false result of all validation and wrong type error.

Overridden error messages:

| Param     | type   | Default                                            |
|-----------|--------|:---------------------------------------------------|
| wrongType | string | :attribute was is checked by a not callable object |


In validation your function will be called with these parameters:

```php
function yourValidationFunction($value, string $attribute, CallableRule $rule): bool
```

Where:
- $value - it's a value to check
- string $attribute - name of form attribute
- CallableRule $rule - instance of CallableRule class, it's wrapper for user callable functions

To add an error message from callable function use the rule object:
```php
function yourValidationFunction($value, string $attribute, CallableRule $rule): bool 
{
    $rule->addError('test error');
    return false;
}
```

standalone use callable rule:

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

To create your own validation rule , 
you need create class and implements <b>\Iljaaa\Machete\rules\UserRule</b> interface.

Then you can use the rule in validator like this:

```php
['attributeName', 'rule', YourRuleClass::class]
```

Rule interface has only one method:
```php
public function validate ($value, string $attribute, UserRuleWrapper $userRuleWrapper, Validation $validation): bool;
```

Where:

- $value - it's your value to check
- string $attribute - name of form attribute
- UserRuleWrapper $userRuleWrapper - this is an instance of the special interface to wrap all user rules
- Validation $validation - validation form instance

If your value is invalid, add string error to a wrapper object, 
it also sets validation result to false

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

If you want know (was a form validated) what is the form of the validation 
and what is result of validation, do something like this:

```php
if ($form->isVasValidate() && $form->isValid() == false) echo "Form is not valid" 
```

If you want to know (was a form loaded and validated) if the form was loaded and validated, 
and if attribute has an error display it
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

Return the answer on the question: has the form been validated before?

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
- string len
- test in rule with \Traversable
- id? unsigned int 
- array
- associated array
- array of accosted arrays
- additional options in self rules
- think about splitting int and float into different validators is it works with different types
- think about static cache of fields validation state in validator for speed up 
- isVasValidated -> isItValidated
