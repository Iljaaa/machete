# machete
Another validation library

PHP version 7.4, not tested on version 8

! Machete dont have pre filters on validate values

```php
public function rules (): array
{
    return [
        [['name'], 'validateName'],
    ];
}
```

first parameter is field name

second is validator like string, number

next is named params dif-rend for each rule 


Use validation class
1. extend Validate class
2. owerride rules():array method
3. load data, by data method
4. call validate() method e.c. method validate return boolean result of validate

after validation use method isValid() for get validation result, before you call validate() isValid 
be allways return false wot is wrong

string - ready 
number - 
callable - ?  
associated array - ?
array - ?

