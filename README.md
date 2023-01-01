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

Rules method mast return array of named arrays, every named array descript one validation role. It mas be in save sintaxis

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

string rule use ms_strlen function for calculate string length

before check other rules string check is_string function. 
if they return false, you has false result of validation and error from 'wrongType' param

string additional params

| Param     | type   | Are                                      | Default            |
|-----------|--------|:-----------------------------------------|:-------------------|
| min       | int    | minimum string length for check          | null               |
| min       | int    | maximum string                           | null               |
| wrongType | string | error message if you try check no string | It's not a string  |
| toShort   | string |                                          | To short           |
| toLong    | string |                                          | To long            |

int & float
==

Int vas auto converted into float

```php
['attribute',  'float', ....]
['attribute',  'int', ....]
```

| Param     | type   | Are                                      | Default            |
|-----------|--------|:-----------------------------------------|:-------------------|
| min       | int    | minimum string length for check          | null               |
| min       | int    | maximum string                           | null               |
| wrongType | string |                                          |                    |
| toSmall   | string |                                          | To short           |
| toBig     | string |                                          | To long            |

manual use
--

```php
$result = (new InValidationRule())->validate($value);
or
$result = (new InValidationRule())
    ->setMin($minIntOfFloat)
    ->setMax($maxIntAndFloat)
    ->setToSmall();
    ->setToBig();
    ->validate($needle);

```

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

No content here, 

Callable
--

Second paramether can be callable
like this
['name', function () {}]

or
['name', fn ($value, string $attribute, Role $rol) => true, ....]


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
yoreValidationFunction($value, ?string $attribute, Role $role): bool
```

Where:
- $value - its value for check
- string $attribute - name of checked attribute
- Role $role - instance of CallableValidationRule class

Manual use:
```php
$result = (new CallableRule($callableObject))->validate($value);
or
$result = (new CallableRule())->setCallable($callableObject)->validate($value);
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
- write normal description to exception classes
- int, float - remake to use filters https://www.php.net/manual/en/filter.filters.validate.php
- date??? date as string with excepted format YYYY-MM-DD by pattern 2022-12-31, 2-3-2
- assert attribute for create form function 
- array
- associated array
- array of accosiated arrays
- rule lire interface???
- form rule validation, for self rule
- update fields errors
- think about static cache of fields validation state in validator for speed up 
