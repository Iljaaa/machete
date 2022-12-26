# machete
Another validation library

PHP version 7.4, not tested on version 8

! Machete dont have pre filters on validate values

```php
public function rules (): array
{
    return [
        ['firstname', 'string', 'min' => 22],
        [['lastname'], [$this, 'myCustomValidationMethod'], 'message' => 'Replaced error message'],
        [['age'],  'number', 'min' => 18, 'max' => 100]
    ];
}
```

first parameter is field name

second is validator like string, number. in ....

next is named params dif-rend for each rule 


Use validation class
1. extend Validate class
2. owerride rules():array method
3. load data, by data method
4. call validate() method e.c. method validate return boolean result of validate

after validation use method isValid() for get validation result, before you call validate() isValid 
be allways return false wot is wrong

Provided rules
==

rule set is second paramether of rule array

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

Self validation functions
--

Second paramether can be callable
like this
['name', function () {}]

or
['name', fn ($value, string $attribute, Role $rol) => true]

All what can pass is_callable function
this callable object be call with params
($value, string $attribute, Role $role)

To do:
- role exception
- write normal description to exception classes
- required - ready
- string - ready
- number -
- in -
- callable - +/-
- array
- associated array
