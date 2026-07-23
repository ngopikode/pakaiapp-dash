# HTTP Tests

* [Introduction](#introduction)
* [Making Requests](#making-requests)
  + [Customizing Request Headers](#customizing-request-headers)
  + [Cookies](#cookies)
  + [Session / Authentication](#session-and-authentication)
  + [Debugging Responses](#debugging-responses)
  + [Exception Handling](#exception-handling)
* [Testing JSON APIs](#testing-json-apis)
  + [Fluent JSON Testing](#fluent-json-testing)
* [Testing File Uploads](#testing-file-uploads)
* [Testing Views](#testing-views)
  + [Rendering Blade and Components](#rendering-blade-and-components)
* [Caching Routes](#caching-routes)
* [Available Assertions](#available-assertions)
  + [Response Assertions](#response-assertions)
  + [Authentication Assertions](#authentication-assertions)
  + [Validation Assertions](#validation-assertions)

## [Introduction](#introduction)

Laravel provides a very fluent API for making HTTP requests to your application and examining the responses. For example, take a look at the feature test defined below:

Pest
PHPUnit

```
<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
```

The `get` method makes a `GET` request into the application, while the `assertStatus` method asserts that the returned response should have the given HTTP status code. In addition to this simple assertion, Laravel also contains a variety of assertions for inspecting the response headers, content, JSON structure, and more.

## [Making Requests](#making-requests)

To make a request to your application, you may invoke the `get`, `post`, `put`, `patch`, or `delete` methods within your test. These methods do not actually issue a "real" HTTP request to your application. Instead, the entire network request is simulated internally.

Instead of returning an `Illuminate\Http\Response` instance, test request methods return an instance of `Illuminate\Testing\TestResponse`, which provides a [variety of helpful assertions](#available-assertions) that allow you to inspect your application's responses:

Pest
PHPUnit

```
<?php

test('basic request', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_a_basic_request(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
```

In general, each of your tests should only make one request to your application. Unexpected behavior may occur if multiple requests are executed within a single test method.

For convenience, the CSRF middleware is automatically disabled when running tests.

### [Customizing Request Headers](#customizing-request-headers)

You may use the `withHeaders` method to customize the request's headers before it is sent to the application. This method allows you to add any custom headers you would like to the request:

Pest
PHPUnit

```
<?php

test('interacting with headers', function () {
    $response = $this->withHeaders([
        'X-Header' => 'Value',
    ])->post('/user', ['name' => 'Sally']);

    $response->assertStatus(201);
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     */
    public function test_interacting_with_headers(): void
    {
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->post('/user', ['name' => 'Sally']);

        $response->assertStatus(201);
    }
}
```

### [Cookies](#cookies)

You may use the `withCookie` or `withCookies` methods to set cookie values before making a request. The `withCookie` method accepts a cookie name and value as its two arguments, while the `withCookies` method accepts an array of name / value pairs:

Pest
PHPUnit

```
<?php

test('interacting with cookies', function () {
    $response = $this->withCookie('color', 'blue')->get('/');

    $response = $this->withCookies([
        'color' => 'blue',
        'name' => 'Taylor',
    ])->get('/');

    //
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_interacting_with_cookies(): void
    {
        $response = $this->withCookie('color', 'blue')->get('/');

        $response = $this->withCookies([
            'color' => 'blue',
            'name' => 'Taylor',
        ])->get('/');

        //
    }
}
```

### [Session / Authentication](#session-and-authentication)

Laravel provides several helpers for interacting with the session during HTTP testing. First, you may set the session data to a given array using the `withSession` method. This is useful for loading the session with data before issuing a request to your application:

Pest
PHPUnit

```
<?php

test('interacting with the session', function () {
    $response = $this->withSession(['banned' => false])->get('/');

    //
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_interacting_with_the_session(): void
    {
        $response = $this->withSession(['banned' => false])->get('/');

        //
    }
}
```

Laravel's session is typically used to maintain state for the currently authenticated user. Therefore, the `actingAs` helper method provides a simple way to authenticate a given user as the current user. For example, we may use a [model factory](../eloquent-orm/eloquent-factories.md) to generate and authenticate a user:

Pest
PHPUnit

```
<?php

use App\Models\User;

test('an action that requires authentication', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withSession(['banned' => false])
        ->get('/');

    //
});
```

```
<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_an_action_that_requires_authentication(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['banned' => false])
            ->get('/');

        //
    }
}
```

You may also specify which guard should be used to authenticate the given user by passing the guard name as the second argument to the `actingAs` method. The guard that is provided to the `actingAs` method will also become the default guard for the duration of the test:

```
$this->actingAs($user, 'web');
```

If you would like to ensure the request is unauthenticated, you may use the `actingAsGuest` method:

```
$this->actingAsGuest();
```

### [Debugging Responses](#debugging-responses)

After making a test request to your application, the `dump`, `dumpHeaders`, and `dumpSession` methods may be used to examine and debug the response contents:

Pest
PHPUnit

```
<?php

test('basic test', function () {
    $response = $this->get('/');

    $response->dump();
    $response->dumpHeaders();
    $response->dumpSession();
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_basic_test(): void
    {
        $response = $this->get('/');

        $response->dump();
        $response->dumpHeaders();
        $response->dumpSession();
    }
}
```

Alternatively, you may use the `dd`, `ddHeaders`, `ddBody`, `ddJson`, and `ddSession` methods to dump information about the response and then stop execution:

Pest
PHPUnit

```
<?php

test('basic test', function () {
    $response = $this->get('/');

    $response->dd();
    $response->ddHeaders();
    $response->ddBody();
    $response->ddJson();
    $response->ddSession();
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_basic_test(): void
    {
        $response = $this->get('/');

        $response->dd();
        $response->ddHeaders();
        $response->ddBody();
        $response->ddJson();
        $response->ddSession();
    }
}
```

### [Exception Handling](#exception-handling)

Sometimes you may need to test that your application is throwing a specific exception. To accomplish this, you may "fake" the exception handler via the `Exceptions` facade. Once the exception handler has been faked, you may utilize the `assertReported` and `assertNotReported` methods to make assertions against exceptions that were thrown during the request:

Pest
PHPUnit

```
<?php

use App\Exceptions\InvalidOrderException;
use Illuminate\Support\Facades\Exceptions;

test('exception is thrown', function () {
    Exceptions::fake();

    $response = $this->get('/order/1');

    // Assert an exception was thrown...
    Exceptions::assertReported(InvalidOrderException::class);

    // Assert against the exception...
    Exceptions::assertReported(function (InvalidOrderException $e) {
        return $e->getMessage() === 'The order was invalid.';
    });
});
```

```
<?php

namespace Tests\Feature;

use App\Exceptions\InvalidOrderException;
use Illuminate\Support\Facades\Exceptions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_exception_is_thrown(): void
    {
        Exceptions::fake();

        $response = $this->get('/');

        // Assert an exception was thrown...
        Exceptions::assertReported(InvalidOrderException::class);

        // Assert against the exception...
        Exceptions::assertReported(function (InvalidOrderException $e) {
            return $e->getMessage() === 'The order was invalid.';
        });
    }
}
```

The `assertNotReported` and `assertNothingReported` methods may be used to assert that a given exception was not thrown during the request or that no exceptions were thrown:

```
Exceptions::assertNotReported(InvalidOrderException::class);

Exceptions::assertNothingReported();
```

You may totally disable exception handling for a given request by invoking the `withoutExceptionHandling` method before making your request:

```
$response = $this->withoutExceptionHandling()->get('/');
```

In addition, if you would like to ensure that your application is not utilizing features that have been deprecated by the PHP language or the libraries your application is using, you may invoke the `withoutDeprecationHandling` method before making your request. When deprecation handling is disabled, deprecation warnings will be converted to exceptions, thus causing your test to fail:

```
$response = $this->withoutDeprecationHandling()->get('/');
```

The `assertThrows` method may be used to assert that code within a given closure throws an exception of the specified type:

```
$this->assertThrows(
    fn () => (new ProcessOrder)->execute(),
    OrderInvalid::class
);
```

If you would like to inspect and make assertions against the exception that is thrown, you may provide a closure as the second argument to the `assertThrows` method:

```
$this->assertThrows(
    fn () => (new ProcessOrder)->execute(),
    fn (OrderInvalid $e) => $e->orderId() === 123;
);
```

The `assertDoesntThrow` method may be used to assert that the code within a given closure does not throw any exceptions:

```
$this->assertDoesntThrow(fn () => (new ProcessOrder)->execute());
```

## [Testing JSON APIs](#testing-json-apis)

Laravel also provides several helpers for testing JSON APIs and their responses. For example, the `json`, `getJson`, `postJson`, `putJson`, `patchJson`, `deleteJson`, and `optionsJson` methods may be used to issue JSON requests with various HTTP verbs. You may also easily pass data and headers to these methods. To get started, let's write a test to make a `POST` request to `/api/user` and assert that the expected JSON data was returned:

Pest
PHPUnit

```
<?php

test('making an api request', function () {
    $response = $this->postJson('/api/user', ['name' => 'Sally']);

    $response
        ->assertStatus(201)
        ->assertJson([
            'created' => true,
        ]);
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     */
    public function test_making_an_api_request(): void
    {
        $response = $this->postJson('/api/user', ['name' => 'Sally']);

        $response
            ->assertStatus(201)
            ->assertJson([
                'created' => true,
            ]);
    }
}
```

In addition, JSON response data may be accessed as array variables on the response, making it convenient for you to inspect the individual values returned within a JSON response:

Pest
PHPUnit

```
expect($response['created'])->toBeTrue();
```

```
$this->assertTrue($response['created']);
```

The `assertJson` method converts the response to an array to verify that the given array exists within the JSON response returned by the application. So, if there are other properties in the JSON response, this test will still pass as long as the given fragment is present.

#### [Asserting Exact JSON Matches](#verifying-exact-match)

As previously mentioned, the `assertJson` method may be used to assert that a fragment of JSON exists within the JSON response. If you would like to verify that a given array **exactly matches** the JSON returned by your application, you should use the `assertExactJson` method:

Pest
PHPUnit

```
<?php

test('asserting an exact json match', function () {
    $response = $this->postJson('/user', ['name' => 'Sally']);

    $response
        ->assertStatus(201)
        ->assertExactJson([
            'created' => true,
        ]);
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     */
    public function test_asserting_an_exact_json_match(): void
    {
        $response = $this->postJson('/user', ['name' => 'Sally']);

        $response
            ->assertStatus(201)
            ->assertExactJson([
                'created' => true,
            ]);
    }
}
```

#### [Asserting on JSON Paths](#verifying-json-paths)

If you would like to verify that the JSON response contains the given data at a specified path, you should use the `assertJsonPath` method:

Pest
PHPUnit

```
<?php

test('asserting a json path value', function () {
    $response = $this->postJson('/user', ['name' => 'Sally']);

    $response
        ->assertStatus(201)
        ->assertJsonPath('team.owner.name', 'Darian');
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     */
    public function test_asserting_a_json_paths_value(): void
    {
        $response = $this->postJson('/user', ['name' => 'Sally']);

        $response
            ->assertStatus(201)
            ->assertJsonPath('team.owner.name', 'Darian');
    }
}
```

The `assertJsonPath` method also accepts a closure, which may be used to dynamically determine if the assertion should pass:

```
$response->assertJsonPath('team.owner.name', fn (string $name) => strlen($name) >= 3);
```

If you need to assert multiple JSON paths at once, you may use the `assertJsonPaths` method. The expected value for each path may also be a closure:

```
$response->assertJsonPaths([
    'team.owner.name' => 'Darian',
    'team.owner.email' => fn (string $email) => str($email)->is('*@laravel.com'),
    'team.members.0.name' => 'Sally',
]);
```

You may use the `assertJsonMissingPaths` method to assert that multiple JSON paths are missing from the response:

```
$response->assertJsonMissingPaths([
    'team.owner.password',
    'team.members.0.api_token',
]);
```

### [Fluent JSON Testing](#fluent-json-testing)

Laravel also offers a beautiful way to fluently test your application's JSON responses. To get started, pass a closure to the `assertJson` method. This closure will be invoked with an instance of `Illuminate\Testing\Fluent\AssertableJson` which can be used to make assertions against the JSON that was returned by your application. The `where` method may be used to make assertions against a particular attribute of the JSON, while the `missing` method may be used to assert that a particular attribute is missing from the JSON:

Pest
PHPUnit

```
use Illuminate\Testing\Fluent\AssertableJson;

test('fluent json', function () {
    $response = $this->getJson('/users/1');

    $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('id', 1)
                ->where('name', 'Victoria Faith')
                ->where('email', fn (string $email) => str($email)->is('[email protected]'))
                ->whereNot('status', 'pending')
                ->missing('password')
                ->etc()
        );
});
```

```
use Illuminate\Testing\Fluent\AssertableJson;

/**
 * A basic functional test example.
 */
public function test_fluent_json(): void
{
    $response = $this->getJson('/users/1');

    $response
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('id', 1)
                ->where('name', 'Victoria Faith')
                ->where('email', fn (string $email) => str($email)->is('[email protected]'))
                ->whereNot('status', 'pending')
                ->missing('password')
                ->etc()
        );
}
```

#### Understanding the `etc` Method

In the example above, you may have noticed we invoked the `etc` method at the end of our assertion chain. This method informs Laravel that there may be other attributes present on the JSON object. If the `etc` method is not used, the test will fail if other attributes that you did not make assertions against exist on the JSON object.

The intention behind this behavior is to protect you from unintentionally exposing sensitive information in your JSON responses by forcing you to either explicitly make an assertion against the attribute or explicitly allow additional attributes via the `etc` method.

However, you should be aware that not including the `etc` method in your assertion chain does not ensure that additional attributes are not being added to arrays that are nested within your JSON object. The `etc` method only ensures that no additional attributes exist at the nesting level in which the `etc` method is invoked.

#### [Asserting Attribute Presence / Absence](#asserting-json-attribute-presence-and-absence)

To assert that an attribute is present or absent, you may use the `has` and `missing` methods:

```
$response->assertJson(fn (AssertableJson $json) =>
    $json->has('data')
        ->missing('message')
);
```

In addition, the `hasAll` and `missingAll` methods allow asserting the presence or absence of multiple attributes simultaneously:

```
$response->assertJson(fn (AssertableJson $json) =>
    $json->hasAll(['status', 'data'])
        ->missingAll(['message', 'code'])
);
```

You may use the `hasAny` method to determine if at least one of a given list of attributes is present:

```
$response->assertJson(fn (AssertableJson $json) =>
    $json->has('status')
        ->hasAny('data', 'message', 'code')
);
```

#### [Asserting Against JSON Collections](#asserting-against-json-collections)

Often, your route will return a JSON response that contains multiple items, such as multiple users:

```
Route::get('/users', function () {
    return User::all();
});
```

In these situations, we may use the fluent JSON object's `has` method to make assertions against the users included in the response. For example, let's assert that the JSON response contains three users. Next, we'll make some assertions about the first user in the collection using the `first` method. The `first` method accepts a closure which receives another assertable JSON string that we can use to make assertions about the first object in the JSON collection:

```
$response
    ->assertJson(fn (AssertableJson $json) =>
        $json->has(3)
            ->first(fn (AssertableJson $json) =>
                $json->where('id', 1)
                    ->where('name', 'Victoria Faith')
                    ->where('email', fn (string $email) => str($email)->is('[email protected]'))
                    ->missing('password')
                    ->etc()
            )
    );
```

If you would like to make the same assertions against every item in a JSON collection, you may use the `each` method:

```
$response
  ->assertJson(fn (AssertableJson $json) =>
      $json->has(3)
          ->each(fn (AssertableJson $json) =>
              $json->whereType('id', 'integer')
                  ->whereType('name', 'string')
                  ->whereType('email', 'string')
                  ->missing('password')
                  ->etc()
          )
  );
```

#### [Scoping JSON Collection Assertions](#scoping-json-collection-assertions)

Sometimes, your application's routes will return JSON collections that are assigned named keys:

```
Route::get('/users', function () {
    return [
        'meta' => [...],
        'users' => User::all(),
    ];
})
```

When testing these routes, you may use the `has` method to assert against the number of items in the collection. In addition, you may use the `has` method to scope a chain of assertions:

```
$response
    ->assertJson(fn (AssertableJson $json) =>
        $json->has('meta')
            ->has('users', 3)
            ->has('users.0', fn (AssertableJson $json) =>
                $json->where('id', 1)
                    ->where('name', 'Victoria Faith')
                    ->where('email', fn (string $email) => str($email)->is('[email protected]'))
                    ->missing('password')
                    ->etc()
            )
    );
```

However, instead of making two separate calls to the `has` method to assert against the `users` collection, you may make a single call which provides a closure as its third parameter. When doing so, the closure will automatically be invoked and scoped to the first item in the collection:

```
$response
    ->assertJson(fn (AssertableJson $json) =>
        $json->has('meta')
            ->has('users', 3, fn (AssertableJson $json) =>
                $json->where('id', 1)
                    ->where('name', 'Victoria Faith')
                    ->where('email', fn (string $email) => str($email)->is('[email protected]'))
                    ->missing('password')
                    ->etc()
            )
    );
```

#### [Asserting JSON Types](#asserting-json-types)

You may only want to assert that the properties in the JSON response are of a certain type. The `Illuminate\Testing\Fluent\AssertableJson` class provides the `whereType` and `whereAllType` methods for doing just that:

```
$response->assertJson(fn (AssertableJson $json) =>
    $json->whereType('id', 'integer')
        ->whereAllType([
            'users.0.name' => 'string',
            'meta' => 'array'
        ])
);
```

You may specify multiple types using the `|` character, or passing an array of types as the second parameter to the `whereType` method. The assertion will be successful if the response value is any of the listed types:

```
$response->assertJson(fn (AssertableJson $json) =>
    $json->whereType('name', 'string|null')
        ->whereType('id', ['string', 'integer'])
);
```

The `whereType` and `whereAllType` methods recognize the following types: `string`, `integer`, `double`, `boolean`, `array`, and `null`.

## [Testing File Uploads](#testing-file-uploads)

The `Illuminate\Http\UploadedFile` class provides a `fake` method which may be used to generate dummy files or images for testing. This, combined with the `Storage` facade's `fake` method, greatly simplifies the testing of file uploads. For example, you may combine these two features to easily test an avatar upload form:

Pest
PHPUnit

```
<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('avatars can be uploaded', function () {
    Storage::fake('avatars');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->post('/avatar', [
        'avatar' => $file,
    ]);

    Storage::disk('avatars')->assertExists($file->hashName());
});
```

```
<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_avatars_can_be_uploaded(): void
    {
        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post('/avatar', [
            'avatar' => $file,
        ]);

        Storage::disk('avatars')->assertExists($file->hashName());
    }
}
```

If you would like to assert that a given file does not exist, you may use the `assertMissing` method provided by the `Storage` facade:

```
Storage::fake('avatars');

// ...

Storage::disk('avatars')->assertMissing('missing.jpg');
```

#### [Fake File Customization](#fake-file-customization)

When creating files using the `fake` method provided by the `UploadedFile` class, you may specify the width, height, and size of the image (in kilobytes) in order to better test your application's validation rules:

```
UploadedFile::fake()->image('avatar.jpg', $width, $height)->size(100);
```

In addition to creating images, you may create files of any other type using the `create` method:

```
UploadedFile::fake()->create('document.pdf', $sizeInKilobytes);
```

If needed, you may pass a `$mimeType` argument to the method to explicitly define the MIME type that should be returned by the file:

```
UploadedFile::fake()->create(
    'document.pdf', $sizeInKilobytes, 'application/pdf'
);
```

## [Testing Views](#testing-views)

Laravel also allows you to render a view without making a simulated HTTP request to the application. To accomplish this, you may call the `view` method within your test. The `view` method accepts the view name and an optional array of data. The method returns an instance of `Illuminate\Testing\TestView`, which offers several methods to conveniently make assertions about the view's contents:

Pest
PHPUnit

```
<?php

test('a welcome view can be rendered', function () {
    $view = $this->view('welcome', ['name' => 'Taylor']);

    $view->assertSee('Taylor');
});
```

```
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_a_welcome_view_can_be_rendered(): void
    {
        $view = $this->view('welcome', ['name' => 'Taylor']);

        $view->assertSee('Taylor');
    }
}
```

The `TestView` class provides the following assertion methods: `assertSee`, `assertSeeInOrder`, `assertSeeText`, `assertSeeTextInOrder`, `assertDontSee`, and `assertDontSeeText`.

If needed, you may get the raw, rendered view contents by casting the `TestView` instance to a string:

```
$contents = (string) $this->view('welcome');
```

#### [Sharing Errors](#sharing-errors)

Some views may depend on errors shared in the [global error bag provided by Laravel](../the-basics/validation.md#quick-displaying-the-validation-errors). To hydrate the error bag with error messages, you may use the `withViewErrors` method:

```
$view = $this->withViewErrors([
    'name' => ['Please provide a valid name.']
])->view('form');

$view->assertSee('Please provide a valid name.');
```

### [Rendering Blade and Components](#rendering-blade-and-components)

If necessary, you may use the `blade` method to evaluate and render a raw [Blade](../the-basics/blade.md) string. Like the `view` method, the `blade` method returns an instance of `Illuminate\Testing\TestView`:

```
$view = $this->blade(
    '<x-component :name="$name" />',
    ['name' => 'Taylor']
);

$view->assertSee('Taylor');
```

You may use the `component` method to evaluate and render a [Blade component](../the-basics/blade.md#components). The `component` method returns an instance of `Illuminate\Testing\TestComponent`:

```
$view = $this->component(Profile::class, ['name' => 'Taylor']);

$view->assertSee('Taylor');
```

## [Caching Routes](#caching-routes)

Before a test runs, Laravel boots a fresh instance of the application, including collecting all defined routes. If your applications have many route files, you may wish to add the `Illuminate\Foundation\Testing\WithCachedRoutes` trait to your test cases. On tests which use this trait, routes are built once and stored in memory, meaning the route collection process is only run once for all tests in your suite:

Pest
PHPUnit

```
<?php

use App\Http\Controllers\UserController;
use Illuminate\Foundation\Testing\WithCachedRoutes;

pest()->use(WithCachedRoutes::class);

test('basic example', function () {
    $this->get(action([UserController::class, 'index']));

    // ...
});
```

```
<?php

namespace Tests\Feature;

use App\Http\Controllers\UserController;
use Illuminate\Foundation\Testing\WithCachedRoutes;
use Tests\TestCase;

class BasicTest extends TestCase
{
    use WithCachedRoutes;

    /**
     * A basic functional test example.
     */
    public function test_basic_example(): void
    {
        $response = $this->get(action([UserController::class, 'index']));

        // ...
    }
}
```

## [Available Assertions](#available-assertions)

### [Response Assertions](#response-assertions)

Laravel's `Illuminate\Testing\TestResponse` class provides a variety of custom assertion methods that you may utilize when testing your application. These assertions may be accessed on the response that is returned by the `json`, `get`, `post`, `put`, and `delete` test methods:

[assertAccepted](#assert-accepted)
[assertBadRequest](#assert-bad-request)
[assertClientError](#assert-client-error)
[assertConflict](#assert-conflict)
[assertCookie](#assert-cookie)
[assertCookieExpired](#assert-cookie-expired)
[assertCookieNotExpired](#assert-cookie-not-expired)
[assertCookieMissing](#assert-cookie-missing)
[assertCreated](#assert-created)
[assertDontSee](#assert-dont-see)
[assertDontSeeText](#assert-dont-see-text)
[assertDownload](#assert-download)
[assertExactJson](#assert-exact-json)
[assertExactJsonStructure](#assert-exact-json-structure)
[assertFailedDependency](#assert-failed-dependency)
[assertForbidden](#assert-forbidden)
[assertFound](#assert-found)
[assertGone](#assert-gone)
[assertHeader](#assert-header)
[assertHeaderContains](#assert-header-contains)
[assertHeaderMissing](#assert-header-missing)
[assertInternalServerError](#assert-internal-server-error)
[assertJson](#assert-json)
[assertJsonCount](#assert-json-count)
[assertJsonFragment](#assert-json-fragment)
[assertJsonIsArray](#assert-json-is-array)
[assertJsonIsObject](#assert-json-is-object)
[assertJsonMissing](#assert-json-missing)
[assertJsonMissingExact](#assert-json-missing-exact)
[assertJsonMissingValidationErrors](#assert-json-missing-validation-errors)
[assertJsonPath](#assert-json-path)
[assertJsonPaths](#assert-json-paths)
[assertJsonMissingPath](#assert-json-missing-path)
[assertJsonMissingPaths](#assert-json-missing-paths)
[assertJsonStructure](#assert-json-structure)
[assertJsonValidationErrors](#assert-json-validation-errors)
[assertJsonValidationErrorFor](#assert-json-validation-error-for)
[assertLocation](#assert-location)
[assertMethodNotAllowed](#assert-method-not-allowed)
[assertMovedPermanently](#assert-moved-permanently)
[assertContent](#assert-content)
[assertNoContent](#assert-no-content)
[assertStreamed](#assert-streamed)
[assertStreamedContent](#assert-streamed-content)
[assertNotFound](#assert-not-found)
[assertOk](#assert-ok)
[assertPaymentRequired](#assert-payment-required)
[assertPlainCookie](#assert-plain-cookie)
[assertRedirect](#assert-redirect)
[assertRedirectBack](#assert-redirect-back)
[assertRedirectBackWithErrors](#assert-redirect-back-with-errors)
[assertRedirectBackWithoutErrors](#assert-redirect-back-without-errors)
[assertRedirectContains](#assert-redirect-contains)
[assertRedirectToRoute](#assert-redirect-to-route)
[assertRedirectToSignedRoute](#assert-redirect-to-signed-route)
[assertRequestTimeout](#assert-request-timeout)
[assertSee](#assert-see)
[assertSeeInOrder](#assert-see-in-order)
[assertSeeText](#assert-see-text)
[assertSeeTextInOrder](#assert-see-text-in-order)
[assertServerError](#assert-server-error)
[assertServiceUnavailable](#assert-service-unavailable)
[assertSessionHas](#assert-session-has)
[assertSessionHasInput](#assert-session-has-input)
[assertSessionHasAll](#assert-session-has-all)
[assertSessionHasErrors](#assert-session-has-errors)
[assertSessionHasErrorsIn](#assert-session-has-errors-in)
[assertSessionHasNoErrors](#assert-session-has-no-errors)
[assertSessionDoesntHaveErrors](#assert-session-doesnt-have-errors)
[assertSessionMissing](#assert-session-missing)
[assertSessionMissingInput](#assert-session-missing-input)
[assertStatus](#assert-status)
[assertSuccessful](#assert-successful)
[assertTooManyRequests](#assert-too-many-requests)
[assertUnauthorized](#assert-unauthorized)
[assertUnprocessable](#assert-unprocessable)
[assertUnsupportedMediaType](#assert-unsupported-media-type)
[assertValid](#assert-valid)
[assertInvalid](#assert-invalid)
[assertViewHas](#assert-view-has)
[assertViewHasAll](#assert-view-has-all)
[assertViewIs](#assert-view-is)
[assertViewMissing](#assert-view-missing)

#### [assertAccepted](#assert-accepted)

Assert that the response has an accepted (202) HTTP status code:

```
$response->assertAccepted();
```

#### [assertBadRequest](#assert-bad-request)

Assert that the response has a bad request (400) HTTP status code:

```
$response->assertBadRequest();
```

#### [assertClientError](#assert-client-error)

Assert that the response has a client error (>= 400, < 500) HTTP status code:

```
$response->assertClientError();
```

#### [assertConflict](#assert-conflict)

Assert that the response has a conflict (409) HTTP status code:

```
$response->assertConflict();
```

#### [assertCookie](#assert-cookie)

Assert that the response contains the given cookie:

```
$response->assertCookie($cookieName, $value = null);
```

#### [assertCookieExpired](#assert-cookie-expired)

Assert that the response contains the given cookie and it is expired:

```
$response->assertCookieExpired($cookieName);
```

#### [assertCookieNotExpired](#assert-cookie-not-expired)

Assert that the response contains the given cookie and it is not expired:

```
$response->assertCookieNotExpired($cookieName);
```

#### [assertCookieMissing](#assert-cookie-missing)

Assert that the response does not contain the given cookie:

```
$response->assertCookieMissing($cookieName);
```

#### [assertCreated](#assert-created)

Assert that the response has a 201 HTTP status code:

```
$response->assertCreated();
```

#### [assertDontSee](#assert-dont-see)

Assert that the given string is not contained within the response returned by the application. This assertion will automatically escape the given string unless you pass a second argument of `false`:

```
$response->assertDontSee($value, $escape = true);
```

#### [assertDontSeeText](#assert-dont-see-text)

Assert that the given string is not contained within the response text. This assertion will automatically escape the given string unless you pass a second argument of `false`. This method will pass the response content to the `strip_tags` PHP function before making the assertion:

```
$response->assertDontSeeText($value, $escape = true);
```

#### [assertDownload](#assert-download)

Assert that the response is a "download". Typically, this means the invoked route that returned the response returned a `Response::download` response, `BinaryFileResponse`, or `Storage::download` response:

```
$response->assertDownload();
```

If you wish, you may assert that the downloadable file was assigned a given file name:

```
$response->assertDownload('image.jpg');
```

#### [assertExactJson](#assert-exact-json)

Assert that the response contains an exact match of the given JSON data:

```
$response->assertExactJson(array $data);
```

#### [assertExactJsonStructure](#assert-exact-json-structure)

Assert that the response contains an exact match of the given JSON structure:

```
$response->assertExactJsonStructure(array $data);
```

This method is a more strict variant of [assertJsonStructure](#assert-json-structure). In contrast with `assertJsonStructure`, this method will fail if the response contains any keys that aren't explicitly included in the expected JSON structure.

#### [assertFailedDependency](#assert-failed-dependency)

Assert that the response has a failed dependency (424) HTTP status code:

```
$response->assertFailedDependency();
```

#### [assertForbidden](#assert-forbidden)

Assert that the response has a forbidden (403) HTTP status code:

```
$response->assertForbidden();
```

#### [assertFound](#assert-found)

Assert that the response has a found (302) HTTP status code:

```
$response->assertFound();
```

#### [assertGone](#assert-gone)

Assert that the response has a gone (410) HTTP status code:

```
$response->assertGone();
```

#### [assertHeader](#assert-header)

Assert that the given header and value is present on the response:

```
$response->assertHeader($headerName, $value = null);
```

#### [assertHeaderContains](#assert-header-contains)

Assert that the given header contains a given substring value:

```
$response->assertHeaderContains($headerName, $value);
```

#### [assertHeaderMissing](#assert-header-missing)

Assert that the given header is not present on the response:

```
$response->assertHeaderMissing($headerName);
```

#### [assertInternalServerError](#assert-internal-server-error)

Assert that the response has an "Internal Server Error" (500) HTTP status code:

```
$response->assertInternalServerError();
```

#### [assertJson](#assert-json)

Assert that the response contains the given JSON data:

```
$response->assertJson(array $data, $strict = false);
```

The `assertJson` method converts the response to an array to verify that the given array exists within the JSON response returned by the application. So, if there are other properties in the JSON response, this test will still pass as long as the given fragment is present.

#### [assertJsonCount](#assert-json-count)

Assert that the response JSON has an array with the expected number of items at the given key:

```
$response->assertJsonCount($count, $key = null);
```

#### [assertJsonFragment](#assert-json-fragment)

Assert that the response contains the given JSON data anywhere in the response:

```
Route::get('/users', function () {
    return [
        'users' => [
            [
                'name' => 'Taylor Otwell',
            ],
        ],
    ];
});

$response->assertJsonFragment(['name' => 'Taylor Otwell']);
```

#### [assertJsonIsArray](#assert-json-is-array)

Assert that the response JSON is an array:

```
$response->assertJsonIsArray();
```

#### [assertJsonIsObject](#assert-json-is-object)

Assert that the response JSON is an object:

```
$response->assertJsonIsObject();
```

#### [assertJsonMissing](#assert-json-missing)

Assert that the response does not contain the given JSON data:

```
$response->assertJsonMissing(array $data);
```

#### [assertJsonMissingExact](#assert-json-missing-exact)

Assert that the response does not contain the exact JSON data:

```
$response->assertJsonMissingExact(array $data);
```

#### [assertJsonMissingValidationErrors](#assert-json-missing-validation-errors)

Assert that the response has no JSON validation errors for the given keys:

```
$response->assertJsonMissingValidationErrors($keys);
```

The more generic [assertValid](#assert-valid) method may be used to assert that a response does not have validation errors that were returned as JSON **and** that no errors were flashed to session storage.

#### [assertJsonPath](#assert-json-path)

Assert that the response contains the given data at the specified path:

```
$response->assertJsonPath($path, $expectedValue);
```

For example, if the following JSON response is returned by your application:

```
{
    "user": {
        "name": "Steve Schoger"
    }
}
```

You may assert that the `name` property of the `user` object matches a given value like so:

```
$response->assertJsonPath('user.name', 'Steve Schoger');
```

#### [assertJsonPaths](#assert-json-paths)

Assert that the response contains the given data at the specified paths:

```
$response->assertJsonPaths(array $paths);
```

For example, you may assert multiple values within the response at once:

```
$response->assertJsonPaths([
    'user.name' => 'Steve Schoger',
    'user.email' => fn (string $email) => str($email)->endsWith('@laravel.com'),
]);
```

#### [assertJsonMissingPath](#assert-json-missing-path)

Assert that the response does not contain the given path:

```
$response->assertJsonMissingPath($path);
```

For example, if the following JSON response is returned by your application:

```
{
    "user": {
        "name": "Steve Schoger"
    }
}
```

You may assert that it does not contain the `email` property of the `user` object:

```
$response->assertJsonMissingPath('user.email');
```

#### [assertJsonMissingPaths](#assert-json-missing-paths)

Assert that the response does not contain the given paths:

```
$response->assertJsonMissingPaths($paths);
```

For example, you may assert that multiple paths are missing from the response:

```
$response->assertJsonMissingPaths([
    'user.email',
    'user.password',
]);
```

#### [assertJsonStructure](#assert-json-structure)

Assert that the response has a given JSON structure:

```
$response->assertJsonStructure(array $structure);
```

For example, if the JSON response returned by your application contains the following data:

```
{
    "user": {
        "name": "Steve Schoger"
    }
}
```

You may assert that the JSON structure matches your expectations like so:

```
$response->assertJsonStructure([
    'user' => [
        'name',
    ]
]);
```

Sometimes, JSON responses returned by your application may contain arrays of objects:

```
{
    "user": [
        {
            "name": "Steve Schoger",
            "age": 55,
            "location": "Earth"
        },
        {
            "name": "Mary Schoger",
            "age": 60,
            "location": "Earth"
        }
    ]
}
```

In this situation, you may use the `*` character to assert against the structure of all of the objects in the array:

```
$response->assertJsonStructure([
    'user' => [
        '*' => [
             'name',
             'age',
             'location'
        ]
    ]
]);
```

#### [assertJsonValidationErrors](#assert-json-validation-errors)

Assert that the response has the given JSON validation errors for the given keys. This method should be used when asserting against responses where the validation errors are returned as a JSON structure instead of being flashed to the session:

```
$response->assertJsonValidationErrors(array $data, $responseKey = 'errors');
```

The more generic [assertInvalid](#assert-invalid) method may be used to assert that a response has validation errors returned as JSON **or** that errors were flashed to session storage.

#### [assertJsonValidationErrorFor](#assert-json-validation-error-for)

Assert the response has any JSON validation errors for the given key:

```
$response->assertJsonValidationErrorFor(string $key, $responseKey = 'errors');
```

#### [assertMethodNotAllowed](#assert-method-not-allowed)

Assert that the response has a method not allowed (405) HTTP status code:

```
$response->assertMethodNotAllowed();
```

#### [assertMovedPermanently](#assert-moved-permanently)

Assert that the response has a moved permanently (301) HTTP status code:

```
$response->assertMovedPermanently();
```

#### [assertLocation](#assert-location)

Assert that the response has the given URI value in the `Location` header:

```
$response->assertLocation($uri);
```

#### [assertContent](#assert-content)

Assert that the given string matches the response content:

```
$response->assertContent($value);
```

#### [assertNoContent](#assert-no-content)

Assert that the response has the given HTTP status code and no content:

```
$response->assertNoContent($status = 204);
```

#### [assertStreamed](#assert-streamed)

Assert that the response was a streamed response:

```
$response->assertStreamed();
```

#### [assertStreamedContent](#assert-streamed-content)

Assert that the given string matches the streamed response content:

```
$response->assertStreamedContent($value);
```

#### [assertNotFound](#assert-not-found)

Assert that the response has a not found (404) HTTP status code:

```
$response->assertNotFound();
```

#### [assertOk](#assert-ok)

Assert that the response has a 200 HTTP status code:

```
$response->assertOk();
```

#### [assertPaymentRequired](#assert-payment-required)

Assert that the response has a payment required (402) HTTP status code:

```
$response->assertPaymentRequired();
```

#### [assertPlainCookie](#assert-plain-cookie)

Assert that the response contains the given unencrypted cookie:

```
$response->assertPlainCookie($cookieName, $value = null);
```

#### [assertRedirect](#assert-redirect)

Assert that the response is a redirect to the given URI:

```
$response->assertRedirect($uri = null);
```

#### [assertRedirectBack](#assert-redirect-back)

Assert whether the response is redirecting back to the previous page:

```
$response->assertRedirectBack();
```

#### [assertRedirectBackWithErrors](#assert-redirect-back-with-errors)

Assert whether the response is redirecting back to the previous page and the [session has the given errors](#assert-session-has-errors):

```
$response->assertRedirectBackWithErrors(
    array $keys = [], $format = null, $errorBag = 'default'
);
```

#### [assertRedirectBackWithoutErrors](#assert-redirect-back-without-errors)

Assert whether the response is redirecting back to the previous page and the session does not contain any error messages:

```
$response->assertRedirectBackWithoutErrors();
```

#### [assertRedirectContains](#assert-redirect-contains)

Assert whether the response is redirecting to a URI that contains the given string:

```
$response->assertRedirectContains($string);
```

#### [assertRedirectToRoute](#assert-redirect-to-route)

Assert that the response is a redirect to the given [named route](../the-basics/routing.md#named-routes):

```
$response->assertRedirectToRoute($name, $parameters = []);
```

#### [assertRedirectToSignedRoute](#assert-redirect-to-signed-route)

Assert that the response is a redirect to the given [signed route](../the-basics/urls.md#signed-urls):

```
$response->assertRedirectToSignedRoute($name = null, $parameters = []);
```

#### [assertRequestTimeout](#assert-request-timeout)

Assert that the response has a request timeout (408) HTTP status code:

```
$response->assertRequestTimeout();
```

#### [assertSee](#assert-see)

Assert that the given string is contained within the response. This assertion will automatically escape the given string unless you pass a second argument of `false`:

```
$response->assertSee($value, $escape = true);
```

#### [assertSeeInOrder](#assert-see-in-order)

Assert that the given strings are contained in order within the response. This assertion will automatically escape the given strings unless you pass a second argument of `false`:

```
$response->assertSeeInOrder(array $values, $escape = true);
```

#### [assertSeeText](#assert-see-text)

Assert that the given string is contained within the response text. This assertion will automatically escape the given string unless you pass a second argument of `false`. The response content will be passed to the `strip_tags` PHP function before the assertion is made:

```
$response->assertSeeText($value, $escape = true);
```

#### [assertSeeTextInOrder](#assert-see-text-in-order)

Assert that the given strings are contained in order within the response text. This assertion will automatically escape the given strings unless you pass a second argument of `false`. The response content will be passed to the `strip_tags` PHP function before the assertion is made:

```
$response->assertSeeTextInOrder(array $values, $escape = true);
```

#### [assertServerError](#assert-server-error)

Assert that the response has a server error (>= 500 , < 600) HTTP status code:

```
$response->assertServerError();
```

#### [assertServiceUnavailable](#assert-service-unavailable)

Assert that the response has a "Service Unavailable" (503) HTTP status code:

```
$response->assertServiceUnavailable();
```

#### [assertSessionHas](#assert-session-has)

Assert that the session contains the given piece of data:

```
$response->assertSessionHas($key, $value = null);
```

If needed, a closure can be provided as the second argument to the `assertSessionHas` method. The assertion will pass if the closure returns `true`:

```
$response->assertSessionHas($key, function (User $value) {
    return $value->name === 'Taylor Otwell';
});
```

#### [assertSessionHasInput](#assert-session-has-input)

Assert that the session has a given value in the [flashed input array](../the-basics/responses.md#redirecting-with-flashed-session-data):

```
$response->assertSessionHasInput($key, $value = null);
```

If needed, a closure can be provided as the second argument to the `assertSessionHasInput` method. The assertion will pass if the closure returns `true`:

```
use Illuminate\Support\Facades\Crypt;

$response->assertSessionHasInput($key, function (string $value) {
    return Crypt::decryptString($value) === 'secret';
});
```

#### [assertSessionHasAll](#assert-session-has-all)

Assert that the session contains a given array of key / value pairs:

```
$response->assertSessionHasAll(array $data);
```

For example, if your application's session contains `name` and `status` keys, you may assert that both exist and have the specified values like so:

```
$response->assertSessionHasAll([
    'name' => 'Taylor Otwell',
    'status' => 'active',
]);
```

#### [assertSessionHasErrors](#assert-session-has-errors)

Assert that the session contains an error for the given `$keys`. If `$keys` is an associative array, assert that the session contains a specific error message (value) for each field (key). This method should be used when testing routes that flash validation errors to the session instead of returning them as a JSON structure:

```
$response->assertSessionHasErrors(
    array $keys = [], $format = null, $errorBag = 'default'
);
```

For example, to assert that the `name` and `email` fields have validation error messages that were flashed to the session, you may invoke the `assertSessionHasErrors` method like so:

```
$response->assertSessionHasErrors(['name', 'email']);
```

Or, you may assert that a given field has a particular validation error message:

```
$response->assertSessionHasErrors([
    'name' => 'The given name was invalid.'
]);
```

The more generic [assertInvalid](#assert-invalid) method may be used to assert that a response has validation errors returned as JSON **or** that errors were flashed to session storage.

#### [assertSessionHasErrorsIn](#assert-session-has-errors-in)

Assert that the session contains an error for the given `$keys` within a specific [error bag](../the-basics/validation.md#named-error-bags). If `$keys` is an associative array, assert that the session contains a specific error message (value) for each field (key), within the error bag:

```
$response->assertSessionHasErrorsIn($errorBag, $keys = [], $format = null);
```

#### [assertSessionHasNoErrors](#assert-session-has-no-errors)

Assert that the session has no validation errors:

```
$response->assertSessionHasNoErrors();
```

#### [assertSessionDoesntHaveErrors](#assert-session-doesnt-have-errors)

Assert that the session has no validation errors for the given keys:

```
$response->assertSessionDoesntHaveErrors($keys = [], $format = null, $errorBag = 'default');
```

The more generic [assertValid](#assert-valid) method may be used to assert that a response does not have validation errors that were returned as JSON **and** that no errors were flashed to session storage.

#### [assertSessionMissing](#assert-session-missing)

Assert that the session does not contain the given key:

```
$response->assertSessionMissing($key);
```

#### [assertSessionMissingInput](#assert-session-missing-input)

Assert that the session is missing the given input key in the flashed input array:

```
$response->assertSessionMissingInput($key);
```

#### [assertStatus](#assert-status)

Assert that the response has a given HTTP status code:

```
$response->assertStatus($code);
```

#### [assertSuccessful](#assert-successful)

Assert that the response has a successful (>= 200 and < 300) HTTP status code:

```
$response->assertSuccessful();
```

#### [assertTooManyRequests](#assert-too-many-requests)

Assert that the response has a too many requests (429) HTTP status code:

```
$response->assertTooManyRequests();
```

#### [assertUnauthorized](#assert-unauthorized)

Assert that the response has an unauthorized (401) HTTP status code:

```
$response->assertUnauthorized();
```

#### [assertUnprocessable](#assert-unprocessable)

Assert that the response has an unprocessable entity (422) HTTP status code:

```
$response->assertUnprocessable();
```

#### [assertUnsupportedMediaType](#assert-unsupported-media-type)

Assert that the response has an unsupported media type (415) HTTP status code:

```
$response->assertUnsupportedMediaType();
```

#### [assertValid](#assert-valid)

Assert that the response has no validation errors for the given keys. This method may be used for asserting against responses where the validation errors are returned as a JSON structure or where the validation errors have been flashed to the session:

```
// Assert that no validation errors are present...
$response->assertValid();

// Assert that the given keys do not have validation errors...
$response->assertValid(['name', 'email']);
```

#### [assertInvalid](#assert-invalid)

Assert that the response has validation errors for the given keys. This method may be used for asserting against responses where the validation errors are returned as a JSON structure or where the validation errors have been flashed to the session:

```
$response->assertInvalid(['name', 'email']);
```

You may also assert that a given key has a particular validation error message. When doing so, you may provide the entire message or only a small portion of the message:

```
$response->assertInvalid([
    'name' => 'The name field is required.',
    'email' => 'valid email address',
]);
```

If you would like to assert that the given fields are the only fields with validation errors, you may use the `assertOnlyInvalid` method:

```
$response->assertOnlyInvalid(['name', 'email']);
```

#### [assertViewHas](#assert-view-has)

Assert that the response view contains a given piece of data:

```
$response->assertViewHas($key, $value = null);
```

Passing a closure as the second argument to the `assertViewHas` method will allow you to inspect and make assertions against a particular piece of view data:

```
$response->assertViewHas('user', function (User $user) {
    return $user->name === 'Taylor';
});
```

In addition, view data may be accessed as array variables on the response, allowing you to conveniently inspect it:

Pest
PHPUnit

```
expect($response['name'])->toBe('Taylor');
```

```
$this->assertEquals('Taylor', $response['name']);
```

#### [assertViewHasAll](#assert-view-has-all)

Assert that the response view has a given list of data:

```
$response->assertViewHasAll(array $data);
```

This method may be used to assert that the view simply contains data matching the given keys:

```
$response->assertViewHasAll([
    'name',
    'email',
]);
```

Or, you may assert that the view data is present and has specific values:

```
$response->assertViewHasAll([
    'name' => 'Taylor Otwell',
    'email' => '[email protected],',
]);
```

#### [assertViewIs](#assert-view-is)

Assert that the given view was returned by the route:

```
$response->assertViewIs($value);
```

#### [assertViewMissing](#assert-view-missing)

Assert that the given data key was not made available to the view returned in the application's response:

```
$response->assertViewMissing($key);
```

### [Authentication Assertions](#authentication-assertions)

Laravel also provides a variety of authentication related assertions that you may utilize within your application's feature tests. Note that these methods are invoked on the test class itself and not the `Illuminate\Testing\TestResponse` instance returned by methods such as `get` and `post`.

#### [assertAuthenticated](#assert-authenticated)

Assert that a user is authenticated:

```
$this->assertAuthenticated($guard = null);
```

#### [assertGuest](#assert-guest)

Assert that a user is not authenticated:

```
$this->assertGuest($guard = null);
```

#### [assertAuthenticatedAs](#assert-authenticated-as)

Assert that a specific user is authenticated:

```
$this->assertAuthenticatedAs($user, $guard = null);
```

## [Validation Assertions](#validation-assertions)

Laravel provides two primary validation related assertions that you may use to ensure the data provided in your request was either valid or invalid.

#### [assertValid](#validation-assert-valid)

Assert that the response has no validation errors for the given keys. This method may be used for asserting against responses where the validation errors are returned as a JSON structure or where the validation errors have been flashed to the session:

```
// Assert that no validation errors are present...
$response->assertValid();

// Assert that the given keys do not have validation errors...
$response->assertValid(['name', 'email']);
```

#### [assertInvalid](#validation-assert-invalid)

Assert that the response has validation errors for the given keys. This method may be used for asserting against responses where the validation errors are returned as a JSON structure or where the validation errors have been flashed to the session:

```
$response->assertInvalid(['name', 'email']);
```

You may also assert that a given key has a particular validation error message. When doing so, you may provide the entire message or only a small portion of the message:

```
$response->assertInvalid([
    'name' => 'The name field is required.',
    'email' => 'valid email address',
]);
```