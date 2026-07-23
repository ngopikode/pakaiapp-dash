# Helpers

* [Introduction](#introduction)
* [Available Methods](#available-methods)
* [Other Utilities](#other-utilities)
  + [Benchmarking](#benchmarking)
  + [Dates and Time](#dates)
  + [Deferred Functions](#deferred-functions)
  + [Lottery](#lottery)
  + [Pipeline](#pipeline)
  + [Sleep](#sleep)
  + [Timebox](#timebox)
  + [URI](#uri)

## [Introduction](#introduction)

Laravel includes a variety of global "helper" PHP functions. Many of these functions are used by the framework itself; however, you are free to use them in your own applications if you find them convenient.

## [Available Methods](#available-methods)

### [Arrays & Objects](#arrays-and-objects-method-list)

[Arr::accessible](#method-array-accessible)
[Arr::add](#method-array-add)
[Arr::array](#method-array-array)
[Arr::boolean](#method-array-boolean)
[Arr::collapse](#method-array-collapse)
[Arr::crossJoin](#method-array-crossjoin)
[Arr::divide](#method-array-divide)
[Arr::dot](#method-array-dot)
[Arr::every](#method-array-every)
[Arr::except](#method-array-except)
[Arr::exceptValues](#method-array-except-values)
[Arr::exists](#method-array-exists)
[Arr::first](#method-array-first)
[Arr::flatten](#method-array-flatten)
[Arr::float](#method-array-float)
[Arr::forget](#method-array-forget)
[Arr::from](#method-array-from)
[Arr::get](#method-array-get)
[Arr::has](#method-array-has)
[Arr::hasAll](#method-array-hasall)
[Arr::hasAny](#method-array-hasany)
[Arr::integer](#method-array-integer)
[Arr::isAssoc](#method-array-isassoc)
[Arr::isList](#method-array-islist)
[Arr::join](#method-array-join)
[Arr::keyBy](#method-array-keyby)
[Arr::last](#method-array-last)
[Arr::map](#method-array-map)
[Arr::mapSpread](#method-array-map-spread)
[Arr::mapWithKeys](#method-array-map-with-keys)
[Arr::only](#method-array-only)
[Arr::onlyValues](#method-array-only-values)
[Arr::partition](#method-array-partition)
[Arr::pluck](#method-array-pluck)
[Arr::prepend](#method-array-prepend)
[Arr::prependKeysWith](#method-array-prependkeyswith)
[Arr::pull](#method-array-pull)
[Arr::push](#method-array-push)
[Arr::query](#method-array-query)
[Arr::random](#method-array-random)
[Arr::reject](#method-array-reject)
[Arr::select](#method-array-select)
[Arr::set](#method-array-set)
[Arr::shuffle](#method-array-shuffle)
[Arr::sole](#method-array-sole)
[Arr::some](#method-array-some)
[Arr::sort](#method-array-sort)
[Arr::sortDesc](#method-array-sort-desc)
[Arr::sortRecursive](#method-array-sort-recursive)
[Arr::string](#method-array-string)
[Arr::take](#method-array-take)
[Arr::toCssClasses](#method-array-to-css-classes)
[Arr::toCssStyles](#method-array-to-css-styles)
[Arr::undot](#method-array-undot)
[Arr::where](#method-array-where)
[Arr::whereNotNull](#method-array-where-not-null)
[Arr::wrap](#method-array-wrap)
[data\_fill](#method-data-fill)
[data\_get](#method-data-get)
[data\_set](#method-data-set)
[data\_forget](#method-data-forget)
[head](#method-head)
[last](#method-last)

### [Numbers](#numbers-method-list)

[Number::abbreviate](#method-number-abbreviate)
[Number::clamp](#method-number-clamp)
[Number::currency](#method-number-currency)
[Number::defaultCurrency](#method-default-currency)
[Number::defaultLocale](#method-default-locale)
[Number::fileSize](#method-number-file-size)
[Number::forHumans](#method-number-for-humans)
[Number::format](#method-number-format)
[Number::ordinal](#method-number-ordinal)
[Number::pairs](#method-number-pairs)
[Number::parse](#method-number-parse)
[Number::parseInt](#method-number-parse-int)
[Number::parseFloat](#method-number-parse-float)
[Number::percentage](#method-number-percentage)
[Number::spell](#method-number-spell)
[Number::spellOrdinal](#method-number-spell-ordinal)
[Number::trim](#method-number-trim)
[Number::useLocale](#method-number-use-locale)
[Number::withLocale](#method-number-with-locale)
[Number::useCurrency](#method-number-use-currency)
[Number::withCurrency](#method-number-with-currency)

### [Paths](#paths-method-list)

[app\_path](#method-app-path)
[base\_path](#method-base-path)
[config\_path](#method-config-path)
[database\_path](#method-database-path)
[lang\_path](#method-lang-path)
[public\_path](#method-public-path)
[resource\_path](#method-resource-path)
[storage\_path](#method-storage-path)

### [URLs](#urls-method-list)

[action](#method-action)
[asset](#method-asset)
[route](#method-route)
[secure\_asset](#method-secure-asset)
[secure\_url](#method-secure-url)
[to\_action](#method-to-action)
[to\_route](#method-to-route)
[uri](#method-uri)
[url](#method-url)

### [Miscellaneous](#miscellaneous-method-list)

[abort](#method-abort)
[abort\_if](#method-abort-if)
[abort\_unless](#method-abort-unless)
[app](#method-app)
[auth](#method-auth)
[back](#method-back)
[bcrypt](#method-bcrypt)
[blank](#method-blank)
[broadcast](#method-broadcast)
[broadcast\_if](#method-broadcast-if)
[broadcast\_unless](#method-broadcast-unless)
[cache](#method-cache)
[class\_uses\_recursive](#method-class-uses-recursive)
[collect](#method-collect)
[config](#method-config)
[context](#method-context)
[cookie](#method-cookie)
[csrf\_field](#method-csrf-field)
[csrf\_token](#method-csrf-token)
[decrypt](#method-decrypt)
[dd](#method-dd)
[dispatch](#method-dispatch)
[dispatch\_sync](#method-dispatch-sync)
[dump](#method-dump)
[encrypt](#method-encrypt)
[env](#method-env)
[event](#method-event)
[fake](#method-fake)
[filled](#method-filled)
[info](#method-info)
[literal](#method-literal)
[logger](#method-logger)
[method\_field](#method-method-field)
[now](#method-now)
[old](#method-old)
[once](#method-once)
[optional](#method-optional)
[policy](#method-policy)
[redirect](#method-redirect)
[report](#method-report)
[report\_if](#method-report-if)
[report\_unless](#method-report-unless)
[request](#method-request)
[rescue](#method-rescue)
[resolve](#method-resolve)
[response](#method-response)
[retry](#method-retry)
[session](#method-session)
[tap](#method-tap)
[throw\_if](#method-throw-if)
[throw\_unless](#method-throw-unless)
[today](#method-today)
[trait\_uses\_recursive](#method-trait-uses-recursive)
[transform](#method-transform)
[validator](#method-validator)
[value](#method-value)
[view](#method-view)
[with](#method-with)
[when](#method-when)

## [Arrays & Objects](#arrays)

#### [`Arr::accessible()`](#method-array-accessible)

The `Arr::accessible` method determines if the given value is array accessible:

```
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

$isAccessible = Arr::accessible(['a' => 1, 'b' => 2]);

// true

$isAccessible = Arr::accessible(new Collection);

// true

$isAccessible = Arr::accessible('abc');

// false

$isAccessible = Arr::accessible(new stdClass);

// false
```

#### [`Arr::add()`](#method-array-add)

The `Arr::add` method adds a given key / value pair to an array if the given key doesn't already exist in the array or is set to `null`:

```
use Illuminate\Support\Arr;

$array = Arr::add(['name' => 'Desk'], 'price', 100);

// ['name' => 'Desk', 'price' => 100]

$array = Arr::add(['name' => 'Desk', 'price' => null], 'price', 100);

// ['name' => 'Desk', 'price' => 100]
```

#### [`Arr::array()`](#method-array-array)

The `Arr::array` method retrieves a value from a deeply nested array using "dot" notation (just as [Arr::get()](#method-array-get) does), but throws an `InvalidArgumentException` if the requested value is not an `array`:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];

$value = Arr::array($array, 'languages');

// ['PHP', 'Ruby']

$value = Arr::array($array, 'name');

// throws InvalidArgumentException
```

#### [`Arr::boolean()`](#method-array-boolean)

The `Arr::boolean` method retrieves a value from a deeply nested array using "dot" notation (just as [Arr::get()](#method-array-get) does), but throws an `InvalidArgumentException` if the requested value is not a `boolean`:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Joe', 'available' => true];

$value = Arr::boolean($array, 'available');

// true

$value = Arr::boolean($array, 'name');

// throws InvalidArgumentException
```

#### [`Arr::collapse()`](#method-array-collapse)

The `Arr::collapse` method collapses an array of arrays or collections into a single array:

```
use Illuminate\Support\Arr;

$array = Arr::collapse([[1, 2, 3], [4, 5, 6], [7, 8, 9]]);

// [1, 2, 3, 4, 5, 6, 7, 8, 9]
```

#### [`Arr::crossJoin()`](#method-array-crossjoin)

The `Arr::crossJoin` method cross joins the given arrays, returning a Cartesian product with all possible permutations:

```
use Illuminate\Support\Arr;

$matrix = Arr::crossJoin([1, 2], ['a', 'b']);

/*
    [
        [1, 'a'],
        [1, 'b'],
        [2, 'a'],
        [2, 'b'],
    ]
*/

$matrix = Arr::crossJoin([1, 2], ['a', 'b'], ['I', 'II']);

/*
    [
        [1, 'a', 'I'],
        [1, 'a', 'II'],
        [1, 'b', 'I'],
        [1, 'b', 'II'],
        [2, 'a', 'I'],
        [2, 'a', 'II'],
        [2, 'b', 'I'],
        [2, 'b', 'II'],
    ]
*/
```

#### [`Arr::divide()`](#method-array-divide)

The `Arr::divide` method returns two arrays: one containing the keys and the other containing the values of the given array:

```
use Illuminate\Support\Arr;

[$keys, $values] = Arr::divide(['name' => 'Desk']);

// $keys: ['name']

// $values: ['Desk']
```

#### [`Arr::dot()`](#method-array-dot)

The `Arr::dot` method flattens a multi-dimensional array into a single level array that uses "dot" notation to indicate depth:

```
use Illuminate\Support\Arr;

$array = ['products' => ['desk' => ['price' => 100]]];

$flattened = Arr::dot($array);

// ['products.desk.price' => 100]
```

#### [`Arr::every()`](#method-array-every)

The `Arr::every` method ensures that all values in the array pass a given truth test:

```
use Illuminate\Support\Arr;

$array = [1, 2, 3];

Arr::every($array, fn ($i) => $i > 0);

// true

Arr::every($array, fn ($i) => $i > 2);

// false
```

#### [`Arr::except()`](#method-array-except)

The `Arr::except` method removes the given key / value pairs from an array:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Desk', 'price' => 100];

$filtered = Arr::except($array, ['price']);

// ['name' => 'Desk']
```

#### [`Arr::exceptValues()`](#method-array-except-values)

The `Arr::exceptValues` method removes the specified values from an array:

```
use Illuminate\Support\Arr;

$array = ['foo', 'bar', 'baz', 'qux'];

$filtered = Arr::exceptValues($array, ['foo', 'baz']);

// ['bar', 'qux']
```

You may also pass `true` to the `strict` argument to use strict type comparisons when filtering:

```
use Illuminate\Support\Arr;

$array = [1, '1', 2, '2'];

$filtered = Arr::exceptValues($array, [1, 2], strict: true);

// ['1', '2']
```

#### [`Arr::exists()`](#method-array-exists)

The `Arr::exists` method checks that the given key exists in the provided array:

```
use Illuminate\Support\Arr;

$array = ['name' => 'John Doe', 'age' => 17];

$exists = Arr::exists($array, 'name');

// true

$exists = Arr::exists($array, 'salary');

// false
```

#### [`Arr::first()`](#method-array-first)

The `Arr::first` method returns the first element of an array passing a given truth test:

```
use Illuminate\Support\Arr;

$array = [100, 200, 300];

$first = Arr::first($array, function (int $value, int $key) {
    return $value >= 150;
});

// 200
```

A default value may also be passed as the third parameter to the method. This value will be returned if no value passes the truth test:

```
use Illuminate\Support\Arr;

$first = Arr::first($array, $callback, $default);
```

#### [`Arr::flatten()`](#method-array-flatten)

The `Arr::flatten` method flattens a multi-dimensional array into a single level array:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];

$flattened = Arr::flatten($array);

// ['Joe', 'PHP', 'Ruby']
```

#### [`Arr::float()`](#method-array-float)

The `Arr::float` method retrieves a value from a deeply nested array using "dot" notation (just as [Arr::get()](#method-array-get) does), but throws an `InvalidArgumentException` if the requested value is not a `float`:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Joe', 'balance' => 123.45];

$value = Arr::float($array, 'balance');

// 123.45

$value = Arr::float($array, 'name');

// throws InvalidArgumentException
```

#### [`Arr::forget()`](#method-array-forget)

The `Arr::forget` method removes a given key / value pairs from a deeply nested array using "dot" notation:

```
use Illuminate\Support\Arr;

$array = ['products' => ['desk' => ['price' => 100]]];

Arr::forget($array, 'products.desk');

// ['products' => []]
```

#### [`Arr::from()`](#method-array-from)

The `Arr::from` method converts various input types into a plain PHP array. It supports a range of input types, including arrays, objects, and several common Laravel interfaces, such as `Arrayable`, `Enumerable`, `Jsonable`, and `JsonSerializable`. Additionally, it handles `Traversable` and `WeakMap` instances:

```
use Illuminate\Support\Arr;

Arr::from((object) ['foo' => 'bar']); // ['foo' => 'bar']

class TestJsonableObject implements Jsonable
{
    public function toJson($options = 0)
    {
        return json_encode(['foo' => 'bar']);
    }
}

Arr::from(new TestJsonableObject); // ['foo' => 'bar']
```

#### [`Arr::get()`](#method-array-get)

The `Arr::get` method retrieves a value from a deeply nested array using "dot" notation:

```
use Illuminate\Support\Arr;

$array = ['products' => ['desk' => ['price' => 100]]];

$price = Arr::get($array, 'products.desk.price');

// 100
```

The `Arr::get` method also accepts a default value, which will be returned if the specified key is not present in the array:

```
use Illuminate\Support\Arr;

$discount = Arr::get($array, 'products.desk.discount', 0);

// 0
```

#### [`Arr::has()`](#method-array-has)

The `Arr::has` method checks whether a given item or items exists in an array using "dot" notation:

```
use Illuminate\Support\Arr;

$array = ['product' => ['name' => 'Desk', 'price' => 100]];

$contains = Arr::has($array, 'product.name');

// true

$contains = Arr::has($array, ['product.price', 'product.discount']);

// false
```

#### [`Arr::hasAll()`](#method-array-hasall)

The `Arr::hasAll` method determines if all of the specified keys exist in the given array using "dot" notation:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Taylor', 'language' => 'PHP'];

Arr::hasAll($array, ['name']); // true
Arr::hasAll($array, ['name', 'language']); // true
Arr::hasAll($array, ['name', 'IDE']); // false
```

#### [`Arr::hasAny()`](#method-array-hasany)

The `Arr::hasAny` method checks whether any item in a given set exists in an array using "dot" notation:

```
use Illuminate\Support\Arr;

$array = ['product' => ['name' => 'Desk', 'price' => 100]];

$contains = Arr::hasAny($array, 'product.name');

// true

$contains = Arr::hasAny($array, ['product.name', 'product.discount']);

// true

$contains = Arr::hasAny($array, ['category', 'product.discount']);

// false
```

#### [`Arr::integer()`](#method-array-integer)

The `Arr::integer` method retrieves a value from a deeply nested array using "dot" notation (just as [Arr::get()](#method-array-get) does), but throws an `InvalidArgumentException` if the requested value is not an `int`:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Joe', 'age' => 42];

$value = Arr::integer($array, 'age');

// 42

$value = Arr::integer($array, 'name');

// throws InvalidArgumentException
```

#### [`Arr::isAssoc()`](#method-array-isassoc)

The `Arr::isAssoc` method returns `true` if the given array is an associative array. An array is considered "associative" if it doesn't have sequential numerical keys beginning with zero:

```
use Illuminate\Support\Arr;

$isAssoc = Arr::isAssoc(['product' => ['name' => 'Desk', 'price' => 100]]);

// true

$isAssoc = Arr::isAssoc([1, 2, 3]);

// false
```

#### [`Arr::isList()`](#method-array-islist)

The `Arr::isList` method returns `true` if the given array's keys are sequential integers beginning from zero:

```
use Illuminate\Support\Arr;

$isList = Arr::isList(['foo', 'bar', 'baz']);

// true

$isList = Arr::isList(['product' => ['name' => 'Desk', 'price' => 100]]);

// false
```

#### [`Arr::join()`](#method-array-join)

The `Arr::join` method joins array elements with a string. Using this method's third argument, you may also specify the joining string for the final element of the array:

```
use Illuminate\Support\Arr;

$array = ['Tailwind', 'Alpine', 'Laravel', 'Livewire'];

$joined = Arr::join($array, ', ');

// Tailwind, Alpine, Laravel, Livewire

$joined = Arr::join($array, ', ', ', and ');

// Tailwind, Alpine, Laravel, and Livewire
```

#### [`Arr::keyBy()`](#method-array-keyby)

The `Arr::keyBy` method keys the array by the given key. If multiple items have the same key, only the last one will appear in the new array:

```
use Illuminate\Support\Arr;

$array = [
    ['product_id' => 'prod-100', 'name' => 'Desk'],
    ['product_id' => 'prod-200', 'name' => 'Chair'],
];

$keyed = Arr::keyBy($array, 'product_id');

/*
    [
        'prod-100' => ['product_id' => 'prod-100', 'name' => 'Desk'],
        'prod-200' => ['product_id' => 'prod-200', 'name' => 'Chair'],
    ]
*/
```

#### [`Arr::last()`](#method-array-last)

The `Arr::last` method returns the last element of an array passing a given truth test:

```
use Illuminate\Support\Arr;

$array = [100, 200, 300, 110];

$last = Arr::last($array, function (int $value, int $key) {
    return $value >= 150;
});

// 300
```

A default value may be passed as the third argument to the method. This value will be returned if no value passes the truth test:

```
use Illuminate\Support\Arr;

$last = Arr::last($array, $callback, $default);
```

#### [`Arr::map()`](#method-array-map)

The `Arr::map` method iterates through the array and passes each value and key to the given callback. The array value is replaced by the value returned by the callback:

```
use Illuminate\Support\Arr;

$array = ['first' => 'james', 'last' => 'kirk'];

$mapped = Arr::map($array, function (string $value, string $key) {
    return ucfirst($value);
});

// ['first' => 'James', 'last' => 'Kirk']
```

#### [`Arr::mapSpread()`](#method-array-map-spread)

The `Arr::mapSpread` method iterates over the array, passing each nested item value into the given closure. The closure is free to modify the item and return it, thus forming a new array of modified items:

```
use Illuminate\Support\Arr;

$array = [
    [0, 1],
    [2, 3],
    [4, 5],
    [6, 7],
    [8, 9],
];

$mapped = Arr::mapSpread($array, function (int $even, int $odd) {
    return $even + $odd;
});

/*
    [1, 5, 9, 13, 17]
*/
```

#### [`Arr::mapWithKeys()`](#method-array-map-with-keys)

The `Arr::mapWithKeys` method iterates through the array and passes each value to the given callback. The callback should return an associative array containing a single key / value pair:

```
use Illuminate\Support\Arr;

$array = [
    [
        'name' => 'John',
        'department' => 'Sales',
        'email' => '[email protected]',
    ],
    [
        'name' => 'Jane',
        'department' => 'Marketing',
        'email' => '[email protected]',
    ]
];

$mapped = Arr::mapWithKeys($array, function (array $item, int $key) {
    return [$item['email'] => $item['name']];
});

/*
    [
        '[email protected]' => 'John',
        '[email protected]' => 'Jane',
    ]
*/
```

#### [`Arr::only()`](#method-array-only)

The `Arr::only` method returns only the specified key / value pairs from the given array:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Desk', 'price' => 100, 'orders' => 10];

$slice = Arr::only($array, ['name', 'price']);

// ['name' => 'Desk', 'price' => 100]
```

#### [`Arr::onlyValues()`](#method-array-only-values)

The `Arr::onlyValues` method returns only the specified values from an array:

```
use Illuminate\Support\Arr;

$array = ['foo', 'bar', 'baz', 'qux'];

$filtered = Arr::onlyValues($array, ['foo', 'baz']);

// ['foo', 'baz']
```

You may also pass `true` to the `strict` argument to use strict type comparisons when filtering:

```
use Illuminate\Support\Arr;

$array = [1, '1', 2, '2'];

$filtered = Arr::onlyValues($array, [1, 2], strict: true);

// [1, 2]
```

#### [`Arr::partition()`](#method-array-partition)

The `Arr::partition` method may be combined with PHP array destructuring to separate elements that pass a given truth test from those that do not:

```
<?php

use Illuminate\Support\Arr;

$numbers = [1, 2, 3, 4, 5, 6];

[$underThree, $equalOrAboveThree] = Arr::partition($numbers, function (int $i) {
    return $i < 3;
});

dump($underThree);

// [1, 2]

dump($equalOrAboveThree);

// [3, 4, 5, 6]
```

#### [`Arr::pluck()`](#method-array-pluck)

The `Arr::pluck` method retrieves all of the values for a given key from an array:

```
use Illuminate\Support\Arr;

$array = [
    ['developer' => ['id' => 1, 'name' => 'Taylor']],
    ['developer' => ['id' => 2, 'name' => 'Abigail']],
];

$names = Arr::pluck($array, 'developer.name');

// ['Taylor', 'Abigail']
```

You may also specify how you wish the resulting list to be keyed:

```
use Illuminate\Support\Arr;

$names = Arr::pluck($array, 'developer.name', 'developer.id');

// [1 => 'Taylor', 2 => 'Abigail']
```

#### [`Arr::prepend()`](#method-array-prepend)

The `Arr::prepend` method will push an item onto the beginning of an array:

```
use Illuminate\Support\Arr;

$array = ['one', 'two', 'three', 'four'];

$array = Arr::prepend($array, 'zero');

// ['zero', 'one', 'two', 'three', 'four']
```

If needed, you may specify the key that should be used for the value:

```
use Illuminate\Support\Arr;

$array = ['price' => 100];

$array = Arr::prepend($array, 'Desk', 'name');

// ['name' => 'Desk', 'price' => 100]
```

#### [`Arr::prependKeysWith()`](#method-array-prependkeyswith)

The `Arr::prependKeysWith` prepends all key names of an associative array with the given prefix:

```
use Illuminate\Support\Arr;

$array = [
    'name' => 'Desk',
    'price' => 100,
];

$keyed = Arr::prependKeysWith($array, 'product.');

/*
    [
        'product.name' => 'Desk',
        'product.price' => 100,
    ]
*/
```

#### [`Arr::pull()`](#method-array-pull)

The `Arr::pull` method returns and removes a key / value pair from an array:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Desk', 'price' => 100];

$name = Arr::pull($array, 'name');

// $name: Desk

// $array: ['price' => 100]
```

A default value may be passed as the third argument to the method. This value will be returned if the key doesn't exist:

```
use Illuminate\Support\Arr;

$value = Arr::pull($array, $key, $default);
```

#### [`Arr::push()`](#method-array-push)

The `Arr::push` method pushes an item into an array using "dot" notation. If an array does not exist at the given key, it will be created:

```
use Illuminate\Support\Arr;

$array = [];

Arr::push($array, 'office.furniture', 'Desk');

// $array: ['office' => ['furniture' => ['Desk']]]
```

#### [`Arr::query()`](#method-array-query)

The `Arr::query` method converts the array into a query string:

```
use Illuminate\Support\Arr;

$array = [
    'name' => 'Taylor',
    'order' => [
        'column' => 'created_at',
        'direction' => 'desc'
    ]
];

Arr::query($array);

// name=Taylor&order[column]=created_at&order[direction]=desc
```

#### [`Arr::random()`](#method-array-random)

The `Arr::random` method returns a random value from an array:

```
use Illuminate\Support\Arr;

$array = [1, 2, 3, 4, 5];

$random = Arr::random($array);

// 4 - (retrieved randomly)
```

You may also specify the number of items to return as an optional second argument. Note that providing this argument will return an array even if only one item is desired:

```
use Illuminate\Support\Arr;

$items = Arr::random($array, 2);

// [2, 5] - (retrieved randomly)
```

#### [`Arr::reject()`](#method-array-reject)

The `Arr::reject` method removes items from an array using the given closure:

```
use Illuminate\Support\Arr;

$array = [100, '200', 300, '400', 500];

$filtered = Arr::reject($array, function (string|int $value, int $key) {
    return is_string($value);
});

// [0 => 100, 2 => 300, 4 => 500]
```

#### [`Arr::select()`](#method-array-select)

The `Arr::select` method selects an array of values from an array:

```
use Illuminate\Support\Arr;

$array = [
    ['id' => 1, 'name' => 'Desk', 'price' => 200],
    ['id' => 2, 'name' => 'Table', 'price' => 150],
    ['id' => 3, 'name' => 'Chair', 'price' => 300],
];

Arr::select($array, ['name', 'price']);

// [['name' => 'Desk', 'price' => 200], ['name' => 'Table', 'price' => 150], ['name' => 'Chair', 'price' => 300]]
```

#### [`Arr::set()`](#method-array-set)

The `Arr::set` method sets a value within a deeply nested array using "dot" notation:

```
use Illuminate\Support\Arr;

$array = ['products' => ['desk' => ['price' => 100]]];

Arr::set($array, 'products.desk.price', 200);

// ['products' => ['desk' => ['price' => 200]]]
```

#### [`Arr::shuffle()`](#method-array-shuffle)

The `Arr::shuffle` method randomly shuffles the items in the array:

```
use Illuminate\Support\Arr;

$array = Arr::shuffle([1, 2, 3, 4, 5]);

// [3, 2, 5, 1, 4] - (generated randomly)
```

#### [`Arr::sole()`](#method-array-sole)

The `Arr::sole` method retrieves a single value from an array using the given closure. If more than one value within the array matches the given truth test, an `Illuminate\Support\MultipleItemsFoundException` exception will be thrown. If no values match the truth test, an `Illuminate\Support\ItemNotFoundException` exception will be thrown:

```
use Illuminate\Support\Arr;

$array = ['Desk', 'Table', 'Chair'];

$value = Arr::sole($array, fn (string $value) => $value === 'Desk');

// 'Desk'
```

#### [`Arr::some()`](#method-array-some)

The `Arr::some` method ensures that at least one of the values in the array passes a given truth test:

```
use Illuminate\Support\Arr;

$array = [1, 2, 3];

Arr::some($array, fn ($i) => $i > 2);

// true
```

#### [`Arr::sort()`](#method-array-sort)

The `Arr::sort` method sorts an array by its values:

```
use Illuminate\Support\Arr;

$array = ['Desk', 'Table', 'Chair'];

$sorted = Arr::sort($array);

// ['Chair', 'Desk', 'Table']
```

You may also sort the array by the results of a given closure:

```
use Illuminate\Support\Arr;

$array = [
    ['name' => 'Desk'],
    ['name' => 'Table'],
    ['name' => 'Chair'],
];

$sorted = array_values(Arr::sort($array, function (array $value) {
    return $value['name'];
}));

/*
    [
        ['name' => 'Chair'],
        ['name' => 'Desk'],
        ['name' => 'Table'],
    ]
*/
```

#### [`Arr::sortDesc()`](#method-array-sort-desc)

The `Arr::sortDesc` method sorts an array in descending order by its values:

```
use Illuminate\Support\Arr;

$array = ['Desk', 'Table', 'Chair'];

$sorted = Arr::sortDesc($array);

// ['Table', 'Desk', 'Chair']
```

You may also sort the array by the results of a given closure:

```
use Illuminate\Support\Arr;

$array = [
    ['name' => 'Desk'],
    ['name' => 'Table'],
    ['name' => 'Chair'],
];

$sorted = array_values(Arr::sortDesc($array, function (array $value) {
    return $value['name'];
}));

/*
    [
        ['name' => 'Table'],
        ['name' => 'Desk'],
        ['name' => 'Chair'],
    ]
*/
```

#### [`Arr::sortRecursive()`](#method-array-sort-recursive)

The `Arr::sortRecursive` method recursively sorts an array using the `sort` function for numerically indexed sub-arrays and the `ksort` function for associative sub-arrays:

```
use Illuminate\Support\Arr;

$array = [
    ['Roman', 'Taylor', 'Li'],
    ['PHP', 'Ruby', 'JavaScript'],
    ['one' => 1, 'two' => 2, 'three' => 3],
];

$sorted = Arr::sortRecursive($array);

/*
    [
        ['JavaScript', 'PHP', 'Ruby'],
        ['one' => 1, 'three' => 3, 'two' => 2],
        ['Li', 'Roman', 'Taylor'],
    ]
*/
```

If you would like the results sorted in descending order, you may use the `Arr::sortRecursiveDesc` method.

```
$sorted = Arr::sortRecursiveDesc($array);
```

#### [`Arr::string()`](#method-array-string)

The `Arr::string` method retrieves a value from a deeply nested array using "dot" notation (just as [Arr::get()](#method-array-get) does), but throws an `InvalidArgumentException` if the requested value is not a `string`:

```
use Illuminate\Support\Arr;

$array = ['name' => 'Joe', 'languages' => ['PHP', 'Ruby']];

$value = Arr::string($array, 'name');

// Joe

$value = Arr::string($array, 'languages');

// throws InvalidArgumentException
```

#### [`Arr::take()`](#method-array-take)

The `Arr::take` method returns a new array with the specified number of items:

```
use Illuminate\Support\Arr;

$array = [0, 1, 2, 3, 4, 5];

$chunk = Arr::take($array, 3);

// [0, 1, 2]
```

You may also pass a negative integer to take the specified number of items from the end of the array:

```
$array = [0, 1, 2, 3, 4, 5];

$chunk = Arr::take($array, -2);

// [4, 5]
```

#### [`Arr::toCssClasses()`](#method-array-to-css-classes)

The `Arr::toCssClasses` method conditionally compiles a CSS class string. The method accepts an array of classes where the array key contains the class or classes you wish to add, while the value is a boolean expression. If the array element has a numeric key, it will always be included in the rendered class list:

```
use Illuminate\Support\Arr;

$isActive = false;
$hasError = true;

$array = ['p-4', 'font-bold' => $isActive, 'bg-red' => $hasError];

$classes = Arr::toCssClasses($array);

/*
    'p-4 bg-red'
*/
```

#### [`Arr::toCssStyles()`](#method-array-to-css-styles)

The `Arr::toCssStyles` method conditionally compiles a CSS style string. The method accepts an array of CSS declarations where the array key contains the CSS declaration you wish to add, while the value is a boolean expression. If the array element has a numeric key, it will always be included in the compiled CSS style string:

```
use Illuminate\Support\Arr;

$hasColor = true;

$array = ['background-color: blue', 'color: blue' => $hasColor];

$classes = Arr::toCssStyles($array);

/*
    'background-color: blue; color: blue;'
*/
```

This method powers Laravel's functionality allowing [merging classes with a Blade component's attribute bag](../the-basics/blade.md#conditionally-merge-classes) as well as the `@class` [Blade directive](../the-basics/blade.md#conditional-classes).

#### [`Arr::undot()`](#method-array-undot)

The `Arr::undot` method expands a single-dimensional array that uses "dot" notation into a multi-dimensional array:

```
use Illuminate\Support\Arr;

$array = [
    'user.name' => 'Kevin Malone',
    'user.occupation' => 'Accountant',
];

$array = Arr::undot($array);

// ['user' => ['name' => 'Kevin Malone', 'occupation' => 'Accountant']]
```

#### [`Arr::where()`](#method-array-where)

The `Arr::where` method filters an array using the given closure:

```
use Illuminate\Support\Arr;

$array = [100, '200', 300, '400', 500];

$filtered = Arr::where($array, function (string|int $value, int $key) {
    return is_string($value);
});

// [1 => '200', 3 => '400']
```

#### [`Arr::whereNotNull()`](#method-array-where-not-null)

The `Arr::whereNotNull` method removes all `null` values from the given array:

```
use Illuminate\Support\Arr;

$array = [0, null];

$filtered = Arr::whereNotNull($array);

// [0 => 0]
```

#### [`Arr::wrap()`](#method-array-wrap)

The `Arr::wrap` method wraps the given value in an array. If the given value is already an array it will be returned without modification:

```
use Illuminate\Support\Arr;

$string = 'Laravel';

$array = Arr::wrap($string);

// ['Laravel']
```

If the given value is `null`, an empty array will be returned:

```
use Illuminate\Support\Arr;

$array = Arr::wrap(null);

// []
```

#### [`data_fill()`](#method-data-fill)

The `data_fill` function sets a missing value within a nested array or object using "dot" notation:

```
$data = ['products' => ['desk' => ['price' => 100]]];

data_fill($data, 'products.desk.price', 200);

// ['products' => ['desk' => ['price' => 100]]]

data_fill($data, 'products.desk.discount', 10);

// ['products' => ['desk' => ['price' => 100, 'discount' => 10]]]
```

This function also accepts asterisks as wildcards and will fill the target accordingly:

```
$data = [
    'products' => [
        ['name' => 'Desk 1', 'price' => 100],
        ['name' => 'Desk 2'],
    ],
];

data_fill($data, 'products.*.price', 200);

/*
    [
        'products' => [
            ['name' => 'Desk 1', 'price' => 100],
            ['name' => 'Desk 2', 'price' => 200],
        ],
    ]
*/
```

#### [`data_get()`](#method-data-get)

The `data_get` function retrieves a value from a nested array or object using "dot" notation:

```
$data = ['products' => ['desk' => ['price' => 100]]];

$price = data_get($data, 'products.desk.price');

// 100
```

The `data_get` function also accepts a default value, which will be returned if the specified key is not found:

```
$discount = data_get($data, 'products.desk.discount', 0);

// 0
```

The function also accepts wildcards using asterisks, which may target any key of the array or object:

```
$data = [
    'product-one' => ['name' => 'Desk 1', 'price' => 100],
    'product-two' => ['name' => 'Desk 2', 'price' => 150],
];

data_get($data, '*.name');

// ['Desk 1', 'Desk 2'];
```

The `{first}` and `{last}` placeholders may be used to retrieve the first or last items in an array:

```
$flight = [
    'segments' => [
        ['from' => 'LHR', 'departure' => '9:00', 'to' => 'IST', 'arrival' => '15:00'],
        ['from' => 'IST', 'departure' => '16:00', 'to' => 'PKX', 'arrival' => '20:00'],
    ],
];

data_get($flight, 'segments.{first}.arrival');

// 15:00
```

#### [`data_set()`](#method-data-set)

The `data_set` function sets a value within a nested array or object using "dot" notation:

```
$data = ['products' => ['desk' => ['price' => 100]]];

data_set($data, 'products.desk.price', 200);

// ['products' => ['desk' => ['price' => 200]]]
```

This function also accepts wildcards using asterisks and will set values on the target accordingly:

```
$data = [
    'products' => [
        ['name' => 'Desk 1', 'price' => 100],
        ['name' => 'Desk 2', 'price' => 150],
    ],
];

data_set($data, 'products.*.price', 200);

/*
    [
        'products' => [
            ['name' => 'Desk 1', 'price' => 200],
            ['name' => 'Desk 2', 'price' => 200],
        ],
    ]
*/
```

By default, any existing values are overwritten. If you wish to only set a value if it doesn't exist, you may pass `false` as the fourth argument to the function:

```
$data = ['products' => ['desk' => ['price' => 100]]];

data_set($data, 'products.desk.price', 200, overwrite: false);

// ['products' => ['desk' => ['price' => 100]]]
```

#### [`data_forget()`](#method-data-forget)

The `data_forget` function removes a value within a nested array or object using "dot" notation:

```
$data = ['products' => ['desk' => ['price' => 100]]];

data_forget($data, 'products.desk.price');

// ['products' => ['desk' => []]]
```

This function also accepts wildcards using asterisks and will remove values on the target accordingly:

```
$data = [
    'products' => [
        ['name' => 'Desk 1', 'price' => 100],
        ['name' => 'Desk 2', 'price' => 150],
    ],
];

data_forget($data, 'products.*.price');

/*
    [
        'products' => [
            ['name' => 'Desk 1'],
            ['name' => 'Desk 2'],
        ],
    ]
*/
```

#### [`head()`](#method-head)

The `head` function returns the first element in the given array. If the array is empty, `false` will be returned:

```
$array = [100, 200, 300];

$first = head($array);

// 100
```

#### [`last()`](#method-last)

The `last` function returns the last element in the given array. If the array is empty, `false` will be returned:

```
$array = [100, 200, 300];

$last = last($array);

// 300
```

## [Numbers](#numbers)

#### [`Number::abbreviate()`](#method-number-abbreviate)

The `Number::abbreviate` method returns the human-readable format of the provided numerical value, with an abbreviation for the units:

```
use Illuminate\Support\Number;

$number = Number::abbreviate(1000);

// 1K

$number = Number::abbreviate(489939);

// 490K

$number = Number::abbreviate(1230000, precision: 2);

// 1.23M
```

#### [`Number::clamp()`](#method-number-clamp)

The `Number::clamp` method ensures a given number stays within a specified range. If the number is lower than the minimum, the minimum value is returned. If the number is higher than the maximum, the maximum value is returned:

```
use Illuminate\Support\Number;

$number = Number::clamp(105, min: 10, max: 100);

// 100

$number = Number::clamp(5, min: 10, max: 100);

// 10

$number = Number::clamp(10, min: 10, max: 100);

// 10

$number = Number::clamp(20, min: 10, max: 100);

// 20
```

#### [`Number::currency()`](#method-number-currency)

The `Number::currency` method returns the currency representation of the given value as a string:

```
use Illuminate\Support\Number;

$currency = Number::currency(1000);

// $1,000.00

$currency = Number::currency(1000, in: 'EUR');

// €1,000.00

$currency = Number::currency(1000, in: 'EUR', locale: 'de');

// 1.000,00 €

$currency = Number::currency(1000, in: 'EUR', locale: 'de', precision: 0);

// 1.000 €
```

#### [`Number::defaultCurrency()`](#method-default-currency)

The `Number::defaultCurrency` method returns the default currency being used by the `Number` class:

```
use Illuminate\Support\Number;

$currency = Number::defaultCurrency();

// USD
```

#### [`Number::defaultLocale()`](#method-default-locale)

The `Number::defaultLocale` method returns the default locale being used by the `Number` class:

```
use Illuminate\Support\Number;

$locale = Number::defaultLocale();

// en
```

#### [`Number::fileSize()`](#method-number-file-size)

The `Number::fileSize` method returns the file size representation of the given byte value as a string:

```
use Illuminate\Support\Number;

$size = Number::fileSize(1024);

// 1 KB

$size = Number::fileSize(1024 * 1024);

// 1 MB

$size = Number::fileSize(1024, precision: 2);

// 1.00 KB
```

#### [`Number::forHumans()`](#method-number-for-humans)

The `Number::forHumans` method returns the human-readable format of the provided numerical value:

```
use Illuminate\Support\Number;

$number = Number::forHumans(1000);

// 1 thousand

$number = Number::forHumans(489939);

// 490 thousand

$number = Number::forHumans(1230000, precision: 2);

// 1.23 million
```

#### [`Number::format()`](#method-number-format)

The `Number::format` method formats the given number into a locale specific string:

```
use Illuminate\Support\Number;

$number = Number::format(100000);

// 100,000

$number = Number::format(100000, precision: 2);

// 100,000.00

$number = Number::format(100000.123, maxPrecision: 2);

// 100,000.12

$number = Number::format(100000, locale: 'de');

// 100.000
```

#### [`Number::ordinal()`](#method-number-ordinal)

The `Number::ordinal` method returns a number's ordinal representation:

```
use Illuminate\Support\Number;

$number = Number::ordinal(1);

// 1st

$number = Number::ordinal(2);

// 2nd

$number = Number::ordinal(21);

// 21st
```

#### [`Number::pairs()`](#method-number-pairs)

The `Number::pairs` method generates an array of number pairs (sub-ranges) based on a specified range and step value. This method can be useful for dividing a larger range of numbers into smaller, manageable sub-ranges for things like pagination or batching tasks. The `pairs` method returns an array of arrays, where each inner array represents a pair (sub-range) of numbers:

```
use Illuminate\Support\Number;

$result = Number::pairs(25, 10);

// [[0, 9], [10, 19], [20, 25]]

$result = Number::pairs(25, 10, offset: 0);

// [[0, 10], [10, 20], [20, 25]]
```

#### [`Number::parse()`](#method-number-parse)

The `Number::parse` method parses a localized numeric string using PHP's `NumberFormatter`:

```
use Illuminate\Support\Number;

$result = Number::parse('10,123', locale: 'en');

// 10123.0

$result = Number::parse('10,123', locale: 'fr');

// 10.123
```

#### [`Number::parseInt()`](#method-number-parse-int)

The `Number::parseInt` method parse a string into an integer according to the specified locale:

```
use Illuminate\Support\Number;

$result = Number::parseInt('10.123');

// (int) 10

$result = Number::parseInt('10,123', locale: 'fr');

// (int) 10
```

#### [`Number::parseFloat()`](#method-number-parse-float)

The `Number::parseFloat` method parse a string into a float according to the specified locale:

```
use Illuminate\Support\Number;

$result = Number::parseFloat('10');

// (float) 10.0

$result = Number::parseFloat('10', locale: 'fr');

// (float) 10.0
```

#### [`Number::percentage()`](#method-number-percentage)

The `Number::percentage` method returns the percentage representation of the given value as a string:

```
use Illuminate\Support\Number;

$percentage = Number::percentage(10);

// 10%

$percentage = Number::percentage(10, precision: 2);

// 10.00%

$percentage = Number::percentage(10.123, maxPrecision: 2);

// 10.12%

$percentage = Number::percentage(10, precision: 2, locale: 'de');

// 10,00%
```

#### [`Number::spell()`](#method-number-spell)

The `Number::spell` method transforms the given number into a string of words:

```
use Illuminate\Support\Number;

$number = Number::spell(102);

// one hundred and two

$number = Number::spell(88, locale: 'fr');

// quatre-vingt-huit
```

The `after` argument allows you to specify a value after which all numbers should be spelled out:

```
$number = Number::spell(10, after: 10);

// 10

$number = Number::spell(11, after: 10);

// eleven
```

The `until` argument allows you to specify a value before which all numbers should be spelled out:

```
$number = Number::spell(5, until: 10);

// five

$number = Number::spell(10, until: 10);

// 10
```

#### [`Number::spellOrdinal()`](#method-number-spell-ordinal)

The `Number::spellOrdinal` method returns the number's ordinal representation as a string of words:

```
use Illuminate\Support\Number;

$number = Number::spellOrdinal(1);

// first

$number = Number::spellOrdinal(2);

// second

$number = Number::spellOrdinal(21);

// twenty-first
```

#### [`Number::trim()`](#method-number-trim)

The `Number::trim` method removes any trailing zero digits after the decimal point of the given number:

```
use Illuminate\Support\Number;

$number = Number::trim(12.0);

// 12

$number = Number::trim(12.30);

// 12.3
```

#### [`Number::useLocale()`](#method-number-use-locale)

The `Number::useLocale` method sets the default number locale globally, which affects how numbers and currency are formatted by subsequent invocations to the `Number` class's methods:

```
use Illuminate\Support\Number;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Number::useLocale('de');
}
```

#### [`Number::withLocale()`](#method-number-with-locale)

The `Number::withLocale` method executes the given closure using the specified locale and then restores the original locale after the callback has executed:

```
use Illuminate\Support\Number;

$number = Number::withLocale('de', function () {
    return Number::format(1500);
});
```

#### [`Number::useCurrency()`](#method-number-use-currency)

The `Number::useCurrency` method sets the default number currency globally, which affects how the currency is formatted by subsequent invocations to the `Number` class's methods:

```
use Illuminate\Support\Number;

/**
 * Bootstrap any application services.
 */
public function boot(): void
{
    Number::useCurrency('GBP');
}
```

#### [`Number::withCurrency()`](#method-number-with-currency)

The `Number::withCurrency` method executes the given closure using the specified currency and then restores the original currency after the callback has executed:

```
use Illuminate\Support\Number;

$number = Number::withCurrency('GBP', function () {
    // ...
});
```

## [Paths](#paths)

#### [`app_path()`](#method-app-path)

The `app_path` function returns the fully qualified path to your application's `app` directory. You may also use the `app_path` function to generate a fully qualified path to a file relative to the application directory:

```
$path = app_path();

$path = app_path('Http/Controllers/Controller.php');
```

#### [`base_path()`](#method-base-path)

The `base_path` function returns the fully qualified path to your application's root directory. You may also use the `base_path` function to generate a fully qualified path to a given file relative to the project root directory:

```
$path = base_path();

$path = base_path('vendor/bin');
```

#### [`config_path()`](#method-config-path)

The `config_path` function returns the fully qualified path to your application's `config` directory. You may also use the `config_path` function to generate a fully qualified path to a given file within the application's configuration directory:

```
$path = config_path();

$path = config_path('app.php');
```

#### [`database_path()`](#method-database-path)

The `database_path` function returns the fully qualified path to your application's `database` directory. You may also use the `database_path` function to generate a fully qualified path to a given file within the database directory:

```
$path = database_path();

$path = database_path('factories/UserFactory.php');
```

#### [`lang_path()`](#method-lang-path)

The `lang_path` function returns the fully qualified path to your application's `lang` directory. You may also use the `lang_path` function to generate a fully qualified path to a given file within the directory:

```
$path = lang_path();

$path = lang_path('en/messages.php');
```

By default, the Laravel application skeleton does not include the `lang` directory. If you would like to customize Laravel's language files, you may publish them via the `lang:publish` Artisan command.

#### [`public_path()`](#method-public-path)

The `public_path` function returns the fully qualified path to your application's `public` directory. You may also use the `public_path` function to generate a fully qualified path to a given file within the public directory:

```
$path = public_path();

$path = public_path('css/app.css');
```

#### [`resource_path()`](#method-resource-path)

The `resource_path` function returns the fully qualified path to your application's `resources` directory. You may also use the `resource_path` function to generate a fully qualified path to a given file within the resources directory:

```
$path = resource_path();

$path = resource_path('sass/app.scss');
```

#### [`storage_path()`](#method-storage-path)

The `storage_path` function returns the fully qualified path to your application's `storage` directory. You may also use the `storage_path` function to generate a fully qualified path to a given file within the storage directory:

```
$path = storage_path();

$path = storage_path('app/file.txt');
```

## [URLs](#urls)

#### [`action()`](#method-action)

The `action` function generates a URL for the given controller action:

```
use App\Http\Controllers\HomeController;

$url = action([HomeController::class, 'index']);
```

If the method accepts route parameters, you may pass them as the second argument to the method:

```
$url = action([UserController::class, 'profile'], ['id' => 1]);
```

#### [`asset()`](#method-asset)

The `asset` function generates a URL for an asset using the current scheme of the request (HTTP or HTTPS):

```
$url = asset('img/photo.jpg');
```

You can configure the asset URL host by setting the `ASSET_URL` variable in your `.env` file. This can be useful if you host your assets on an external service like Amazon S3 or another CDN:

```
// ASSET_URL=http://example.com/assets

$url = asset('img/photo.jpg'); // http://example.com/assets/img/photo.jpg
```

#### [`route()`](#method-route)

The `route` function generates a URL for a given [named route](../the-basics/routing.md#named-routes):

```
$url = route('route.name');
```

If the route accepts parameters, you may pass them as the second argument to the function:

```
$url = route('route.name', ['id' => 1]);
```

By default, the `route` function generates an absolute URL. If you wish to generate a relative URL, you may pass `false` as the third argument to the function:

```
$url = route('route.name', ['id' => 1], false);
```

#### [`secure_asset()`](#method-secure-asset)

The `secure_asset` function generates a URL for an asset using HTTPS:

```
$url = secure_asset('img/photo.jpg');
```

#### [`secure_url()`](#method-secure-url)

The `secure_url` function generates a fully qualified HTTPS URL to the given path. Additional URL segments may be passed in the function's second argument:

```
$url = secure_url('user/profile');

$url = secure_url('user/profile', [1]);
```

#### [`to_action()`](#method-to-action)

The `to_action` function generates a [redirect HTTP response](../the-basics/responses.md#redirects) for a given controller action:

```
use App\Http\Controllers\UserController;

return to_action([UserController::class, 'show'], ['user' => 1]);
```

If necessary, you may pass the HTTP status code that should be assigned to the redirect and any additional response headers as the third and fourth arguments to the `to_action` method:

```
return to_action(
    [UserController::class, 'show'],
    ['user' => 1],
    302,
    ['X-Framework' => 'Laravel']
);
```

#### [`to_route()`](#method-to-route)

The `to_route` function generates a [redirect HTTP response](../the-basics/responses.md#redirects) for a given [named route](../the-basics/routing.md#named-routes):

```
return to_route('users.show', ['user' => 1]);
```

If necessary, you may pass the HTTP status code that should be assigned to the redirect and any additional response headers as the third and fourth arguments to the `to_route` method:

```
return to_route('users.show', ['user' => 1], 302, ['X-Framework' => 'Laravel']);
```

#### [`uri()`](#method-uri)

The `uri` function generates a [fluent URI instance](#uri) for the given URI:

```
$uri = uri('https://example.com')
    ->withPath('/users')
    ->withQuery(['page' => 1]);
```

If the `uri` function is given an array containing a callable controller and method pair, the function will create a `Uri` instance for the controller method's route path:

```
use App\Http\Controllers\UserController;

$uri = uri([UserController::class, 'show'], ['user' => $user]);
```

If the controller is invokable, you may simply provide the controller class name:

```
use App\Http\Controllers\UserIndexController;

$uri = uri(UserIndexController::class);
```

If the value given to the `uri` function matches the name of a [named route](../the-basics/routing.md#named-routes), a `Uri` instance will be generated for that route's path:

```
$uri = uri('users.show', ['user' => $user]);
```

#### [`url()`](#method-url)

The `url` function generates a fully qualified URL to the given path:

```
$url = url('user/profile');

$url = url('user/profile', [1]);
```

If no path is provided, an `Illuminate\Routing\UrlGenerator` instance is returned:

```
$current = url()->current();

$full = url()->full();

$previous = url()->previous();
```

For more information on working with the `url` function, consult the [URL generation documentation](../the-basics/urls.md#generating-urls).

## [Miscellaneous](#miscellaneous)

#### [`abort()`](#method-abort)

The `abort` function throws [an HTTP exception](../the-basics/errors.md#http-exceptions) which will be rendered by the [exception handler](../the-basics/errors.md#handling-exceptions):

```
abort(403);
```

You may also provide the exception's message and custom HTTP response headers that should be sent to the browser:

```
abort(403, 'Unauthorized.', $headers);
```

#### [`abort_if()`](#method-abort-if)

The `abort_if` function throws an HTTP exception if a given boolean expression evaluates to `true`:

```
abort_if(! Auth::user()->isAdmin(), 403);
```

Like the `abort` method, you may also provide the exception's response text as the third argument and an array of custom response headers as the fourth argument to the function.

#### [`abort_unless()`](#method-abort-unless)

The `abort_unless` function throws an HTTP exception if a given boolean expression evaluates to `false`:

```
abort_unless(Auth::user()->isAdmin(), 403);
```

Like the `abort` method, you may also provide the exception's response text as the third argument and an array of custom response headers as the fourth argument to the function.

#### [`app()`](#method-app)

The `app` function returns the [service container](../architecture-concepts/container.md) instance:

```
$container = app();
```

You may pass a class or interface name to resolve it from the container:

```
$api = app('HelpSpot\API');
```

#### [`auth()`](#method-auth)

The `auth` function returns an [authenticator](../security/authentication.md) instance. You may use it as an alternative to the `Auth` facade:

```
$user = auth()->user();
```

If needed, you may specify which guard instance you would like to access:

```
$user = auth('admin')->user();
```

#### [`back()`](#method-back)

The `back` function generates a [redirect HTTP response](../the-basics/responses.md#redirects) to the user's previous location:

```
return back($status = 302, $headers = [], $fallback = '/');

return back();
```

#### [`bcrypt()`](#method-bcrypt)

The `bcrypt` function [hashes](../security/hashing.md) the given value using Bcrypt. You may use this function as an alternative to the `Hash` facade:

```
$password = bcrypt('my-secret-password');
```

#### [`blank()`](#method-blank)

The `blank` function determines whether the given value is "blank":

```
blank('');
blank('   ');
blank(null);
blank(collect());

// true

blank(0);
blank(true);
blank(false);

// false
```

For the inverse of `blank`, see the [filled](#method-filled) function.

#### [`broadcast()`](#method-broadcast)

The `broadcast` function [broadcasts](broadcasting.md) the given [event](events.md) to its listeners:

```
broadcast(new UserRegistered($user));

broadcast(new UserRegistered($user))->toOthers();
```

#### [`broadcast_if()`](#method-broadcast-if)

The `broadcast_if` function [broadcasts](broadcasting.md) the given [event](events.md) to its listeners if a given boolean expression evaluates to `true`:

```
broadcast_if($user->isActive(), new UserRegistered($user));

broadcast_if($user->isActive(), new UserRegistered($user))->toOthers();
```

#### [`broadcast_unless()`](#method-broadcast-unless)

The `broadcast_unless` function [broadcasts](broadcasting.md) the given [event](events.md) to its listeners if a given boolean expression evaluates to `false`:

```
broadcast_unless($user->isBanned(), new UserRegistered($user));

broadcast_unless($user->isBanned(), new UserRegistered($user))->toOthers();
```

#### [`cache()`](#method-cache)

The `cache` function may be used to get values from the [cache](cache.md). If the given key does not exist in the cache, an optional default value will be returned:

```
$value = cache('key');

$value = cache('key', 'default');
```

You may add items to the cache by passing an array of key / value pairs to the function. You should also pass the number of seconds or duration the cached value should be considered valid:

```
cache(['key' => 'value'], 300);

cache(['key' => 'value'], now()->plus(seconds: 10));
```

#### [`class_uses_recursive()`](#method-class-uses-recursive)

The `class_uses_recursive` function returns all traits used by a class, including traits used by all of its parent classes:

```
$traits = class_uses_recursive(App\Models\User::class);
```

#### [`collect()`](#method-collect)

The `collect` function creates a [collection](collections.md) instance from the given value:

```
$collection = collect(['Taylor', 'Abigail']);
```

#### [`config()`](#method-config)

The `config` function gets the value of a [configuration](../getting-started/configuration.md) variable. The configuration values may be accessed using "dot" syntax, which includes the name of the file and the option you wish to access. You may also provide a default value that will be returned if the configuration option does not exist:

```
$value = config('app.timezone');

$value = config('app.timezone', $default);
```

You may set configuration variables at runtime by passing an array of key / value pairs. However, note that this function only affects the configuration value for the current request and does not update your actual configuration values:

```
config(['app.debug' => true]);
```

#### [`context()`](#method-context)

The `context` function gets the value from the current [context](context.md). You may also provide a default value that will be returned if the context key does not exist:

```
$value = context('trace_id');

$value = context('trace_id', $default);
```

You may set context values by passing an array of key / value pairs:

```
use Illuminate\Support\Str;

context(['trace_id' => Str::uuid()->toString()]);
```

#### [`cookie()`](#method-cookie)

The `cookie` function creates a new [cookie](../the-basics/requests.md#cookies) instance:

```
$cookie = cookie('name', 'value', $minutes);
```

#### [`csrf_field()`](#method-csrf-field)

The `csrf_field` function generates an HTML `hidden` input field containing the value of the CSRF token. For example, using [Blade syntax](../the-basics/blade.md):

```
{{ csrf_field() }}
```

#### [`csrf_token()`](#method-csrf-token)

The `csrf_token` function retrieves the value of the current CSRF token:

```
$token = csrf_token();
```

#### [`decrypt()`](#method-decrypt)

The `decrypt` function [decrypts](../security/encryption.md) the given value. You may use this function as an alternative to the `Crypt` facade:

```
$password = decrypt($value);
```

For the inverse of `decrypt`, see the [encrypt](#method-encrypt) function.

#### [`dd()`](#method-dd)

The `dd` function dumps the given variables and ends the execution of the script:

```
dd($value);

dd($value1, $value2, $value3, ...);
```

If you do not want to halt the execution of your script, use the [dump](#method-dump) function instead.

#### [`dispatch()`](#method-dispatch)

The `dispatch` function pushes the given [job](queues.md#creating-jobs) onto the Laravel [job queue](queues.md):

```
dispatch(new App\Jobs\SendEmails);
```

#### [`dispatch_sync()`](#method-dispatch-sync)

The `dispatch_sync` function pushes the given job to the [sync](queues.md#synchronous-dispatching) queue so that it is processed immediately:

```
dispatch_sync(new App\Jobs\SendEmails);
```

#### [`dump()`](#method-dump)

The `dump` function dumps the given variables:

```
dump($value);

dump($value1, $value2, $value3, ...);
```

If you want to stop executing the script after dumping the variables, use the [dd](#method-dd) function instead.

#### [`encrypt()`](#method-encrypt)

The `encrypt` function [encrypts](../security/encryption.md) the given value. You may use this function as an alternative to the `Crypt` facade:

```
$secret = encrypt('my-secret-value');
```

For the inverse of `encrypt`, see the [decrypt](#method-decrypt) function.

#### [`env()`](#method-env)

The `env` function retrieves the value of an [environment variable](../getting-started/configuration.md#environment-configuration) or returns a default value:

```
$env = env('APP_ENV');

$env = env('APP_ENV', 'production');
```

If you execute the `config:cache` command during your deployment process, you should be sure that you are only calling the `env` function from within your configuration files. Once the configuration has been cached, the `.env` file will not be loaded and all calls to the `env` function will return external environment variables such as server-level or system-level environment variables or `null`.

#### [`event()`](#method-event)

The `event` function dispatches the given [event](events.md) to its listeners:

```
event(new UserRegistered($user));
```

#### [`fake()`](#method-fake)

The `fake` function resolves a [Faker](https://github.com/FakerPHP/Faker) singleton from the container, which can be useful when creating fake data in model factories, database seeding, tests, and prototyping views:

```
@for ($i = 0; $i < 10; $i++)
    <dl>
        <dt>Name</dt>
        <dd>{{ fake()->name() }}</dd>

        <dt>Email</dt>
        <dd>{{ fake()->unique()->safeEmail() }}</dd>
    </dl>
@endfor
```

By default, the `fake` function will utilize the `app.faker_locale` configuration option in your `config/app.php` configuration. Typically, this configuration option is set via the `APP_FAKER_LOCALE` environment variable. You may also specify the locale by passing it to the `fake` function. Each locale will resolve an individual singleton:

```
fake('nl_NL')->name()
```

#### [`filled()`](#method-filled)

The `filled` function determines whether the given value is not "blank":

```
filled(0);
filled(true);
filled(false);

// true

filled('');
filled('   ');
filled(null);
filled(collect());

// false
```

For the inverse of `filled`, see the [blank](#method-blank) function.

#### [`info()`](#method-info)

The `info` function will write information to your application's [log](../the-basics/logging.md):

```
info('Some helpful information!');
```

An array of contextual data may also be passed to the function:

```
info('User login attempt failed.', ['id' => $user->id]);
```

#### [`literal()`](#method-literal)

The `literal` function creates a new [stdClass](https://www.php.net/manual/en/class.stdclass.php) instance with the given named arguments as properties:

```
$obj = literal(
    name: 'Joe',
    languages: ['PHP', 'Ruby'],
);

$obj->name; // 'Joe'
$obj->languages; // ['PHP', 'Ruby']
```

#### [`logger()`](#method-logger)

The `logger` function can be used to write a `debug` level message to the [log](../the-basics/logging.md):

```
logger('Debug message');
```

An array of contextual data may also be passed to the function:

```
logger('User has logged in.', ['id' => $user->id]);
```

A [logger](../the-basics/logging.md) instance will be returned if no value is passed to the function:

```
logger()->error('You are not allowed here.');
```

#### [`method_field()`](#method-method-field)

The `method_field` function generates an HTML `hidden` input field containing the spoofed value of the form's HTTP verb. For example, using [Blade syntax](../the-basics/blade.md):

```
<form method="POST">
    {{ method_field('DELETE') }}
</form>
```

#### [`now()`](#method-now)

The `now` function creates a new `Illuminate\Support\Carbon` instance for the current time:

```
$now = now();
```

#### [`old()`](#method-old)

The `old` function [retrieves](../the-basics/requests.md#retrieving-input) an [old input](../the-basics/requests.md#old-input) value flashed into the session:

```
$value = old('value');

$value = old('value', 'default');
```

Since the "default value" provided as the second argument to the `old` function is often an attribute of an Eloquent model, Laravel allows you to simply pass the entire Eloquent model as the second argument to the `old` function. When doing so, Laravel will assume the first argument provided to the `old` function is the name of the Eloquent attribute that should be considered the "default value":

```
{{ old('name', $user->name) }}

// Is equivalent to...

{{ old('name', $user) }}
```

#### [`once()`](#method-once)

The `once` function executes the given callback and caches the result in memory for the duration of the request. Any subsequent calls to the `once` function with the same callback will return the previously cached result:

```
function random(): int
{
    return once(function () {
        return random_int(1, 1000);
    });
}

random(); // 123
random(); // 123 (cached result)
random(); // 123 (cached result)
```

When the `once` function is executed from within an object instance, the cached result will be unique to that object instance:

```
<?php

class NumberService
{
    public function all(): array
    {
        return once(fn () => [1, 2, 3]);
    }
}

$service = new NumberService;

$service->all();
$service->all(); // (cached result)

$secondService = new NumberService;

$secondService->all();
$secondService->all(); // (cached result)
```

#### [`optional()`](#method-optional)

The `optional` function accepts any argument and allows you to access properties or call methods on that object. If the given object is `null`, properties and methods will return `null` instead of causing an error:

```
return optional($user->address)->street;

{!! old('name', optional($user)->name) !!}
```

The `optional` function also accepts a closure as its second argument. The closure will be invoked if the value provided as the first argument is not null:

```
return optional(User::find($id), function (User $user) {
    return $user->name;
});
```

#### [`policy()`](#method-policy)

The `policy` method retrieves a [policy](../security/authorization.md#creating-policies) instance for a given class:

```
$policy = policy(App\Models\User::class);
```

#### [`redirect()`](#method-redirect)

The `redirect` function returns a [redirect HTTP response](../the-basics/responses.md#redirects), or returns the redirector instance if called with no arguments:

```
return redirect($to = null, $status = 302, $headers = [], $secure = null);

return redirect('/home');

return redirect()->route('route.name');
```

#### [`report()`](#method-report)

The `report` function will report an exception using your [exception handler](../the-basics/errors.md#handling-exceptions):

```
report($e);
```

The `report` function also accepts a string as an argument. When a string is given to the function, the function will create an exception with the given string as its message:

```
report('Something went wrong.');
```

#### [`report_if()`](#method-report-if)

The `report_if` function will report an exception using your [exception handler](../the-basics/errors.md#handling-exceptions) if a given boolean expression evaluates to `true`:

```
report_if($shouldReport, $e);

report_if($shouldReport, 'Something went wrong.');
```

#### [`report_unless()`](#method-report-unless)

The `report_unless` function will report an exception using your [exception handler](../the-basics/errors.md#handling-exceptions) if a given boolean expression evaluates to `false`:

```
report_unless($reportingDisabled, $e);

report_unless($reportingDisabled, 'Something went wrong.');
```

#### [`request()`](#method-request)

The `request` function returns the current [request](../the-basics/requests.md) instance or obtains an input field's value from the current request:

```
$request = request();

$value = request('key', $default);
```

#### [`rescue()`](#method-rescue)

The `rescue` function executes the given closure and catches any exceptions that occur during its execution. All exceptions that are caught will be sent to your [exception handler](../the-basics/errors.md#handling-exceptions); however, the request will continue processing:

```
return rescue(function () {
    return $this->method();
});
```

You may also pass a second argument to the `rescue` function. This argument will be the "default" value that should be returned if an exception occurs while executing the closure:

```
return rescue(function () {
    return $this->method();
}, false);

return rescue(function () {
    return $this->method();
}, function () {
    return $this->failure();
});
```

A `report` argument may be provided to the `rescue` function to determine if the exception should be reported via the `report` function:

```
return rescue(function () {
    return $this->method();
}, report: function (Throwable $throwable) {
    return $throwable instanceof InvalidArgumentException;
});
```

#### [`resolve()`](#method-resolve)

The `resolve` function resolves a given class or interface name to an instance using the [service container](../architecture-concepts/container.md):

```
$api = resolve('HelpSpot\API');
```

#### [`response()`](#method-response)

The `response` function creates a [response](../the-basics/responses.md) instance or obtains an instance of the response factory:

```
return response('Hello World', 200, $headers);

return response()->json(['foo' => 'bar'], 200, $headers);
```

#### [`retry()`](#method-retry)

The `retry` function attempts to execute the given callback until the given maximum attempt threshold is met. If the callback does not throw an exception, its return value will be returned. If the callback throws an exception, it will automatically be retried. If the maximum attempt count is exceeded, the exception will be thrown:

```
return retry(5, function () {
    // Attempt 5 times while resting 100ms between attempts...
}, 100);
```

The sleep duration also accepts a `CarbonInterval` instance:

```
use function Illuminate\Support\seconds;

return retry(5, function () {
    // Attempt 5 times while resting 5 seconds between attempts...
}, seconds(5));
```

If you would like to manually calculate the number of milliseconds to sleep between attempts, you may pass a closure as the third argument to the `retry` function:

```
use Exception;

return retry(5, function () {
    // ...
}, function (int $attempt, Exception $exception) {
    return $attempt * 100;
});
```

For convenience, you may provide an array as the first argument to the `retry` function. This array will be used to determine how many milliseconds to sleep between subsequent attempts:

```
return retry([100, 200], function () {
    // Sleep for 100ms on first retry, 200ms on second retry...
});
```

To only retry under specific conditions, you may pass a closure as the fourth argument to the `retry` function:

```
use App\Exceptions\TemporaryException;
use Exception;

return retry(5, function () {
    // ...
}, 100, function (Exception $exception) {
    return $exception instanceof TemporaryException;
});
```

#### [`session()`](#method-session)

The `session` function may be used to get or set [session](../the-basics/session.md) values:

```
$value = session('key');
```

You may set values by passing an array of key / value pairs to the function:

```
session(['chairs' => 7, 'instruments' => 3]);
```

The session store will be returned if no value is passed to the function:

```
$value = session()->get('key');

session()->put('key', $value);
```

#### [`tap()`](#method-tap)

The `tap` function accepts two arguments: an arbitrary `$value` and a closure. The `$value` will be passed to the closure and then be returned by the `tap` function. The return value of the closure is irrelevant:

```
$user = tap(User::first(), function (User $user) {
    $user->name = 'Taylor';

    $user->save();
});
```

If no closure is passed to the `tap` function, you may call any method on the given `$value`. The return value of the method you call will always be `$value`, regardless of what the method actually returns in its definition. For example, the Eloquent `update` method typically returns an integer. However, we can force the method to return the model itself by chaining the `update` method call through the `tap` function:

```
$user = tap($user)->update([
    'name' => $name,
    'email' => $email,
]);
```

To add a `tap` method to a class, you may add the `Illuminate\Support\Traits\Tappable` trait to the class. The `tap` method of this trait accepts a Closure as its only argument. The object instance itself will be passed to the Closure and then be returned by the `tap` method:

```
return $user->tap(function (User $user) {
    // ...
});
```

#### [`throw_if()`](#method-throw-if)

The `throw_if` function throws the given exception if a given boolean expression evaluates to `true`:

```
throw_if(! Auth::user()->isAdmin(), AuthorizationException::class);

throw_if(
    ! Auth::user()->isAdmin(),
    AuthorizationException::class,
    'You are not allowed to access this page.'
);
```

#### [`throw_unless()`](#method-throw-unless)

The `throw_unless` function throws the given exception if a given boolean expression evaluates to `false`:

```
throw_unless(Auth::user()->isAdmin(), AuthorizationException::class);

throw_unless(
    Auth::user()->isAdmin(),
    AuthorizationException::class,
    'You are not allowed to access this page.'
);
```

#### [`today()`](#method-today)

The `today` function creates a new `Illuminate\Support\Carbon` instance for the current date:

```
$today = today();
```

#### [`trait_uses_recursive()`](#method-trait-uses-recursive)

The `trait_uses_recursive` function returns all traits used by a trait:

```
$traits = trait_uses_recursive(\Illuminate\Notifications\Notifiable::class);
```

#### [`transform()`](#method-transform)

The `transform` function executes a closure on a given value if the value is not [blank](#method-blank) and then returns the return value of the closure:

```
$callback = function (int $value) {
    return $value * 2;
};

$result = transform(5, $callback);

// 10
```

A default value or closure may be passed as the third argument to the function. This value will be returned if the given value is blank:

```
$result = transform(null, $callback, 'The value is blank');

// The value is blank
```

#### [`validator()`](#method-validator)

The `validator` function creates a new [validator](../the-basics/validation.md) instance with the given arguments. You may use it as an alternative to the `Validator` facade:

```
$validator = validator($data, $rules, $messages);
```

#### [`value()`](#method-value)

The `value` function returns the value it is given. However, if you pass a closure to the function, the closure will be executed and its returned value will be returned:

```
$result = value(true);

// true

$result = value(function () {
    return false;
});

// false
```

Additional arguments may be passed to the `value` function. If the first argument is a closure then the additional parameters will be passed to the closure as arguments, otherwise they will be ignored:

```
$result = value(function (string $name) {
    return $name;
}, 'Taylor');

// 'Taylor'
```

#### [`view()`](#method-view)

The `view` function retrieves a [view](../the-basics/views.md) instance:

```
return view('auth.login');
```

#### [`with()`](#method-with)

The `with` function returns the value it is given. If a closure is passed as the second argument to the function, the closure will be executed and its returned value will be returned:

```
$callback = function (mixed $value) {
    return is_numeric($value) ? $value * 2 : 0;
};

$result = with(5, $callback);

// 10

$result = with(null, $callback);

// 0

$result = with(5, null);

// 5
```

#### [`when()`](#method-when)

The `when` function returns the value it is given if a given condition evaluates to `true`. Otherwise, `null` is returned. If a closure is passed as the second argument to the function, the closure will be executed and its returned value will be returned:

```
$value = when(true, 'Hello World');

$value = when(true, fn () => 'Hello World');
```

The `when` function is primarily useful for conditionally rendering HTML attributes:

```
<div {!! when($condition, 'wire:poll="calculate"') !!}>
    ...
</div>
```

## [Other Utilities](#other-utilities)

### [Benchmarking](#benchmarking)

Sometimes you may wish to quickly test the performance of certain parts of your application. On those occasions, you may utilize the `Benchmark` support class to measure the number of milliseconds it takes for the given callbacks to complete:

```
<?php

use App\Models\User;
use Illuminate\Support\Benchmark;

Benchmark::dd(fn () => User::find(1)); // 0.1 ms

Benchmark::dd([
    'Scenario 1' => fn () => User::count(), // 0.5 ms
    'Scenario 2' => fn () => User::all()->count(), // 20.0 ms
]);
```

By default, the given callbacks will be executed once (one iteration), and their duration will be displayed in the browser / console.

To invoke a callback more than once, you may specify the number of iterations that the callback should be invoked as the second argument to the method. When executing a callback more than once, the `Benchmark` class will return the average number of milliseconds it took to execute the callback across all iterations:

```
Benchmark::dd(fn () => User::count(), iterations: 10); // 0.5 ms
```

Sometimes, you may want to benchmark the execution of a callback while still obtaining the value returned by the callback. The `value` method will return a tuple containing the value returned by the callback and the number of milliseconds it took to execute the callback:

```
[$count, $duration] = Benchmark::value(fn () => User::count());
```

### [Dates and Time](#dates)

Laravel includes [Carbon](https://carbon.nesbot.com/guide/getting-started/introduction.html), a powerful date and time manipulation library. To create a new `Carbon` instance, you may invoke the `now` function. This function is globally available within your Laravel application:

```
$now = now();
```

Or, you may create a new `Carbon` instance using the `Illuminate\Support\Carbon` class:

```
use Illuminate\Support\Carbon;

$now = Carbon::now();
```

Laravel also augments `Carbon` instances with `plus` and `minus` methods, allowing easy manipulation of the instance's date and time:

```
return now()->plus(minutes: 5);
return now()->plus(hours: 8);
return now()->plus(weeks: 4);

return now()->minus(minutes: 5);
return now()->minus(hours: 8);
return now()->minus(weeks: 4);
```

For a thorough discussion of Carbon and its features, please consult the [official Carbon documentation](https://carbon.nesbot.com/guide/getting-started/introduction.html).

#### [Interval Functions](#interval-functions)

Laravel also offers `milliseconds`, `seconds`, `minutes`, `hours`, `days`, `weeks`, `months`, and `years` functions that return `CarbonInterval` instances, which extend PHP's [DateInterval](https://www.php.net/manual/en/class.dateinterval.php) class. These functions may be used anywhere that Laravel accepts a `DateInterval` instance:

```
use Illuminate\Support\Facades\Cache;

use function Illuminate\Support\{minutes};

Cache::put('metrics', $metrics, minutes(10));
```

### [Deferred Functions](#deferred-functions)

While Laravel's [queued jobs](queues.md) allow you to queue tasks for background processing, sometimes you may have simple tasks you would like to defer without configuring or maintaining a long-running queue worker.

Deferred functions allow you to defer the execution of a closure until after the HTTP response has been sent to the user, keeping your application feeling fast and responsive. To defer the execution of a closure, simply pass the closure to the `Illuminate\Support\defer` function:

```
use App\Services\Metrics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use function Illuminate\Support\defer;

Route::post('/orders', function (Request $request) {
    // Create order...

    defer(fn () => Metrics::reportOrder($order));

    return $order;
});
```

By default, deferred functions will only be executed if the HTTP response, Artisan command, or queued job from which `Illuminate\Support\defer` is invoked completes successfully. This means that deferred functions will not be executed if a request results in a `4xx` or `5xx` HTTP response. If you would like a deferred function to always execute, you may chain the `always` method onto your deferred function:

```
defer(fn () => Metrics::reportOrder($order))->always();
```

If you have the [Swoole PHP extension](https://www.php.net/manual/en/book.swoole.php) installed, Laravel's `defer` function may conflict with Swoole's own global `defer` function, leading to web server errors. Make sure you call Laravel's `defer` helper by explicitly namespacing it: `use function Illuminate\Support\defer;`

#### [Cancelling Deferred Functions](#cancelling-deferred-functions)

If you need to cancel a deferred function before it is executed, you can use the `forget` method to cancel the function by its name. To name a deferred function, provide a second argument to the `Illuminate\Support\defer` function:

```
defer(fn () => Metrics::report(), 'reportMetrics');

defer()->forget('reportMetrics');
```

#### [Disabling Deferred Functions in Tests](#disabling-deferred-functions-in-tests)

When writing tests, it may be useful to disable deferred functions. You may call `withoutDefer` in your test to instruct Laravel to invoke all deferred functions immediately:

Pest
PHPUnit

```
test('without defer', function () {
    $this->withoutDefer();

    // ...
});
```

```
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_without_defer(): void
    {
        $this->withoutDefer();

        // ...
    }
}
```

If you would like to disable deferred functions for all tests within a test case, you may call the `withoutDefer` method from the `setUp` method on your base `TestCase` class:

```
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutDefer();
    }
}
```

### [Lottery](#lottery)

Laravel's lottery class may be used to execute callbacks based on a set of given odds. This can be particularly useful when you only want to execute code for a percentage of your incoming requests:

```
use Illuminate\Support\Lottery;

Lottery::odds(1, 20)
    ->winner(fn () => $user->won())
    ->loser(fn () => $user->lost())
    ->choose();
```

You may combine Laravel's lottery class with other Laravel features. For example, you may wish to only report a small percentage of slow queries to your exception handler. And, since the lottery class is callable, we may pass an instance of the class into any method that accepts callables:

```
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Lottery;

DB::whenQueryingForLongerThan(
    CarbonInterval::seconds(2),
    Lottery::odds(1, 100)->winner(fn () => report('Querying > 2 seconds.')),
);
```

#### [Testing Lotteries](#testing-lotteries)

Laravel provides some simple methods to allow you to easily test your application's lottery invocations:

```
// Lottery will always win...
Lottery::alwaysWin();

// Lottery will always lose...
Lottery::alwaysLose();

// Lottery will win then lose, and finally return to normal behavior...
Lottery::fix([true, false]);

// Lottery will return to normal behavior...
Lottery::determineResultsNormally();
```

### [Pipeline](#pipeline)

Laravel's `Pipeline` facade provides a convenient way to "pipe" a given input through a series of invokable classes, closures, or callables, giving each class the opportunity to inspect or modify the input and invoke the next callable in the pipeline:

```
use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Pipeline;

$user = Pipeline::send($user)
    ->through([
        function (User $user, Closure $next) {
            // ...

            return $next($user);
        },
        function (User $user, Closure $next) {
            // ...

            return $next($user);
        },
    ])
    ->then(fn (User $user) => $user);
```

As you can see, each invokable class or closure in the pipeline is provided the input and a `$next` closure. Invoking the `$next` closure will invoke the next callable in the pipeline. As you may have noticed, this is very similar to [middleware](../the-basics/middleware.md).

When the last callable in the pipeline invokes the `$next` closure, the callable provided to the `then` method will be invoked. Typically, this callable will simply return the given input. For convenience, if you simply want to return the input after it has been processed, you may use the `thenReturn` method.

Of course, as discussed previously, you are not limited to providing closures to your pipeline. You may also provide invokable classes. If a class name is provided, the class will be instantiated via Laravel's [service container](../architecture-concepts/container.md), allowing dependencies to be injected into the invokable class:

```
$user = Pipeline::send($user)
    ->through([
        GenerateProfilePhoto::class,
        ActivateSubscription::class,
        SendWelcomeEmail::class,
    ])
    ->thenReturn();
```

The `withinTransaction` method may be invoked on the pipeline to automatically wrap all steps of the pipeline within a single database transaction:

```
$user = Pipeline::send($user)
    ->withinTransaction()
    ->through([
        ProcessOrder::class,
        TransferFunds::class,
        UpdateInventory::class,
    ])
    ->thenReturn();
```

### [Sleep](#sleep)

Laravel's `Sleep` class is a light-weight wrapper around PHP's native `sleep` and `usleep` functions, offering greater testability while also exposing a developer friendly API for working with time:

```
use Illuminate\Support\Sleep;

$waiting = true;

while ($waiting) {
    Sleep::for(1)->second();

    $waiting = /* ... */;
}
```

The `Sleep` class offers a variety of methods that allow you to work with different units of time:

```
// Return a value after sleeping...
$result = Sleep::for(1)->second()->then(fn () => 1 + 1);

// Sleep while a given value is true...
Sleep::for(1)->second()->while(fn () => shouldKeepSleeping());

// Pause execution for 90 seconds...
Sleep::for(1.5)->minutes();

// Pause execution for 2 seconds...
Sleep::for(2)->seconds();

// Pause execution for 500 milliseconds...
Sleep::for(500)->milliseconds();

// Pause execution for 5,000 microseconds...
Sleep::for(5000)->microseconds();

// Pause execution until a given time...
Sleep::until(now()->plus(minutes: 1));

// Alias of PHP's native "sleep" function...
Sleep::sleep(2);

// Alias of PHP's native "usleep" function...
Sleep::usleep(5000);
```

To easily combine units of time, you may use the `and` method:

```
Sleep::for(1)->second()->and(10)->milliseconds();
```

#### [Testing Sleep](#testing-sleep)

When testing code that utilizes the `Sleep` class or PHP's native sleep functions, your test will pause execution. As you might expect, this makes your test suite significantly slower. For example, imagine you are testing the following code:

```
$waiting = /* ... */;

$seconds = 1;

while ($waiting) {
    Sleep::for($seconds++)->seconds();

    $waiting = /* ... */;
}
```

Typically, testing this code would take *at least* one second. Luckily, the `Sleep` class allows us to "fake" sleeping so that our test suite stays fast:

Pest
PHPUnit

```
it('waits until ready', function () {
    Sleep::fake();

    // ...
});
```

```
public function test_it_waits_until_ready()
{
    Sleep::fake();

    // ...
}
```

When faking the `Sleep` class, the actual execution pause is bypassed, leading to a substantially faster test.

Once the `Sleep` class has been faked, it is possible to make assertions against the expected "sleeps" that should have occurred. To illustrate this, let's imagine we are testing code that pauses execution three times, with each pause increasing by a single second. Using the `assertSequence` method, we can assert that our code "slept" for the proper amount of time while keeping our test fast:

Pest
PHPUnit

```
it('checks if ready three times', function () {
    Sleep::fake();

    // ...

    Sleep::assertSequence([
        Sleep::for(1)->second(),
        Sleep::for(2)->seconds(),
        Sleep::for(3)->seconds(),
    ]);
}
```

```
public function test_it_checks_if_ready_three_times()
{
    Sleep::fake();

    // ...

    Sleep::assertSequence([
        Sleep::for(1)->second(),
        Sleep::for(2)->seconds(),
        Sleep::for(3)->seconds(),
    ]);
}
```

Of course, the `Sleep` class offers a variety of other assertions you may use when testing:

```
use Carbon\CarbonInterval as Duration;
use Illuminate\Support\Sleep;

// Assert that sleep was called 3 times...
Sleep::assertSleptTimes(3);

// Assert against the duration of sleep...
Sleep::assertSlept(function (Duration $duration): bool {
    return /* ... */;
}, times: 1);

// Assert that the Sleep class was never invoked...
Sleep::assertNeverSlept();

// Assert that, even if Sleep was called, no execution paused occurred...
Sleep::assertInsomniac();
```

Sometimes it may be useful to perform an action whenever a fake sleep occurs. To achieve this, you may provide a callback to the `whenFakingSleep` method. In the following example, we use Laravel's [time manipulation helpers](../testing/mocking.md#interacting-with-time) to instantly progress time by the duration of each sleep:

```
use Carbon\CarbonInterval as Duration;

$this->freezeTime();

Sleep::fake();

Sleep::whenFakingSleep(function (Duration $duration) {
    // Progress time when faking sleep...
    $this->travel($duration->totalMilliseconds)->milliseconds();
});
```

As progressing time is a common requirement, the `fake` method accepts a `syncWithCarbon` argument to keep Carbon in sync when sleeping within a test:

```
Sleep::fake(syncWithCarbon: true);

$start = now();

Sleep::for(1)->second();

$start->diffForHumans(); // 1 second ago
```

Laravel uses the `Sleep` class internally whenever it is pausing execution. For example, the [retry](#method-retry) helper uses the `Sleep` class when sleeping, allowing for improved testability when using that helper.

### [Timebox](#timebox)

Laravel's `Timebox` class ensures that the given callback always takes a fixed amount of time to execute, even if its actual execution completes sooner. This is particularly useful for cryptographic operations and user authentication checks, where attackers might exploit variations in execution time to infer sensitive information.

If the execution exceeds the fixed duration, `Timebox` has no effect. It is up to the developer to choose a sufficiently long time as the fixed duration to account for worst-case scenarios.

The call method accepts a closure and a time limit in microseconds, and then executes the closure and waits until the time limit is reached:

```
use Illuminate\Support\Timebox;

(new Timebox)->call(function ($timebox) {
    // ...
}, microseconds: 10000);
```

If an exception is thrown within the closure, this class will respect the defined delay and re-throw the exception after the delay.

### [URI](#uri)

Laravel's `Uri` class provides a convenient and fluent interface for creating and manipulating URIs. This class wraps the functionality provided by the underlying League URI package and integrates seamlessly with Laravel's routing system.

You can create a `Uri` instance easily using static methods:

```
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvokableController;
use Illuminate\Support\Uri;

// Generate a URI instance from the given string...
$uri = Uri::of('https://example.com/path');

// Generate URI instances to paths, named routes, or controller actions...
$uri = Uri::to('/dashboard');
$uri = Uri::route('users.show', ['user' => 1]);
$uri = Uri::signedRoute('users.show', ['user' => 1]);
$uri = Uri::temporarySignedRoute('user.index', now()->plus(minutes: 5));
$uri = Uri::action([UserController::class, 'index']);
$uri = Uri::action(InvokableController::class);

// Generate a URI instance from the current request URL...
$uri = $request->uri();
```

Once you have a URI instance, you can fluently modify it:

```
$uri = Uri::of('https://example.com')
    ->withScheme('http')
    ->withHost('test.com')
    ->withPort(8000)
    ->withPath('/users')
    ->withQuery(['page' => 2])
    ->withFragment('section-1');
```

#### [Inspecting URIs](#inspecting-uris)

The `Uri` class also allows you to easily inspect the various components of the underlying URI:

```
$scheme = $uri->scheme();
$authority = $uri->authority();
$host = $uri->host();
$port = $uri->port();
$path = $uri->path();
$segments = $uri->pathSegments();
$query = $uri->query();
$fragment = $uri->fragment();
```

#### [Manipulating Query Strings](#manipulating-query-strings)

The `Uri` class offers several methods that may be used to manipulate a URI's query string. The `withQuery` method may be used to merge additional query string parameters into the existing query string:

```
$uri = $uri->withQuery(['sort' => 'name']);
```

The `withQueryIfMissing` method may be used to merge additional query string parameters into the existing query string if the given keys do not already exist in the query string:

```
$uri = $uri->withQueryIfMissing(['page' => 1]);
```

The `replaceQuery` method may be used to complete replace the existing query string with a new one:

```
$uri = $uri->replaceQuery(['page' => 1]);
```

The `pushOntoQuery` method may be used to push additional parameters onto a query string parameter that has an array value:

```
$uri = $uri->pushOntoQuery('filter', ['active', 'pending']);
```

The `withoutQuery` method may be used to remove parameters from the query string:

```
$uri = $uri->withoutQuery(['page']);
```

#### [Generating Responses From URIs](#generating-responses-from-uris)

The `redirect` method may be used to generate a `RedirectResponse` instance to the given URI:

```
$uri = Uri::of('https://example.com');

return $uri->redirect();
```

Or, you may simply return the `Uri` instance from a route or controller action, which will automatically generate a redirect response to the returned URI:

```
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Uri;

Route::get('/redirect', function () {
    return Uri::to('/index')
        ->withQuery(['sort' => 'name']);
});
```