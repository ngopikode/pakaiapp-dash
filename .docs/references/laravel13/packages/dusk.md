# Laravel Dusk

* [Introduction](#introduction)
* [Installation](#installation)
  + [Managing ChromeDriver Installations](#managing-chromedriver-installations)
  + [Using Other Browsers](#using-other-browsers)
* [Getting Started](#getting-started)
  + [Generating Tests](#generating-tests)
  + [Resetting the Database After Each Test](#resetting-the-database-after-each-test)
  + [Running Tests](#running-tests)
  + [Environment Handling](#environment-handling)
* [Browser Basics](#browser-basics)
  + [Creating Browsers](#creating-browsers)
  + [Navigation](#navigation)
  + [Resizing Browser Windows](#resizing-browser-windows)
  + [Browser Macros](#browser-macros)
  + [Authentication](#authentication)
  + [Cookies](#cookies)
  + [Executing JavaScript](#executing-javascript)
  + [Taking a Screenshot](#taking-a-screenshot)
  + [Storing Console Output to Disk](#storing-console-output-to-disk)
  + [Storing Page Source to Disk](#storing-page-source-to-disk)
* [Interacting With Elements](#interacting-with-elements)
  + [Dusk Selectors](#dusk-selectors)
  + [Text, Values, and Attributes](#text-values-and-attributes)
  + [Interacting With Forms](#interacting-with-forms)
  + [Attaching Files](#attaching-files)
  + [Pressing Buttons](#pressing-buttons)
  + [Clicking Links](#clicking-links)
  + [Using the Keyboard](#using-the-keyboard)
  + [Using the Mouse](#using-the-mouse)
  + [JavaScript Dialogs](#javascript-dialogs)
  + [Interacting With Inline Frames](#interacting-with-iframes)
  + [Scoping Selectors](#scoping-selectors)
  + [Waiting for Elements](#waiting-for-elements)
  + [Scrolling an Element Into View](#scrolling-an-element-into-view)
* [Available Assertions](#available-assertions)
* [Pages](#pages)
  + [Generating Pages](#generating-pages)
  + [Configuring Pages](#configuring-pages)
  + [Navigating to Pages](#navigating-to-pages)
  + [Shorthand Selectors](#shorthand-selectors)
  + [Page Methods](#page-methods)
* [Components](#components)
  + [Generating Components](#generating-components)
  + [Using Components](#using-components)
* [Continuous Integration](#continuous-integration)
  + [Heroku CI](#running-tests-on-heroku-ci)
  + [Travis CI](#running-tests-on-travis-ci)
  + [GitHub Actions](#running-tests-on-github-actions)
  + [Chipper CI](#running-tests-on-chipper-ci)

## [Introduction](#introduction)

[Pest 4](https://pestphp.com/) now includes automated browser testing which offers significant performance and usability improvements compared to Laravel Dusk. For new projects, we recommend using Pest for browser testing.

[Laravel Dusk](https://github.com/laravel/dusk) provides an expressive, easy-to-use browser automation and testing API. By default, Dusk does not require you to install JDK or Selenium on your local computer. Instead, Dusk uses a standalone [ChromeDriver](https://sites.google.com/chromium.org/driver) installation. However, you are free to utilize any other Selenium compatible driver you wish.

## [Installation](#installation)

To get started, you should install [Google Chrome](https://www.google.com/chrome) and add the `laravel/dusk` Composer dependency to your project:

```
composer require laravel/dusk --dev
```

If you are manually registering Dusk's service provider, you should **never** register it in your production environment, as doing so could lead to arbitrary users being able to authenticate with your application.

After installing the Dusk package, execute the `dusk:install` Artisan command. The `dusk:install` command will create a `tests/Browser` directory, an example Dusk test, and install the Chrome Driver binary for your operating system:

```
php artisan dusk:install
```

Next, set the `APP_URL` environment variable in your application's `.env` file. This value should match the URL you use to access your application in a browser.

If you are using [Laravel Sail](sail.md) to manage your local development environment, please also consult the Sail documentation on [configuring and running Dusk tests](sail.md#laravel-dusk).

### [Managing ChromeDriver Installations](#managing-chromedriver-installations)

If you would like to install a different version of ChromeDriver than what is installed by Laravel Dusk via the `dusk:install` command, you may use the `dusk:chrome-driver` command:

```
# Install the latest version of ChromeDriver for your OS...
php artisan dusk:chrome-driver

# Install a given version of ChromeDriver for your OS...
php artisan dusk:chrome-driver 86

# Install a given version of ChromeDriver for all supported OSs...
php artisan dusk:chrome-driver --all

# Install the version of ChromeDriver that matches the detected version of Chrome / Chromium for your OS...
php artisan dusk:chrome-driver --detect
```

Dusk requires the `chromedriver` binaries to be executable. If you're having problems running Dusk, you should ensure the binaries are executable using the following command: `chmod -R 0755 vendor/laravel/dusk/bin/`.

### [Using Other Browsers](#using-other-browsers)

By default, Dusk uses Google Chrome and a standalone [ChromeDriver](https://sites.google.com/chromium.org/driver) installation to run your browser tests. However, you may start your own Selenium server and run your tests against any browser you wish.

To get started, open your `tests/DuskTestCase.php` file, which is the base Dusk test case for your application. Within this file, you can remove the call to the `startChromeDriver` method. This will stop Dusk from automatically starting the ChromeDriver:

```
/**
 * Prepare for Dusk test execution.
 *
 * @beforeClass
 */
public static function prepare(): void
{
    // static::startChromeDriver();
}
```

Next, you may modify the `driver` method to connect to the URL and port of your choice. In addition, you may modify the "desired capabilities" that should be passed to the WebDriver:

```
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Create the RemoteWebDriver instance.
 */
protected function driver(): RemoteWebDriver
{
    return RemoteWebDriver::create(
        'http://localhost:4444/wd/hub', DesiredCapabilities::phantomjs()
    );
}
```

## [Getting Started](#getting-started)

### [Generating Tests](#generating-tests)

To generate a Dusk test, use the `dusk:make` Artisan command. The generated test will be placed in the `tests/Browser` directory:

```
php artisan dusk:make LoginTest
```

### [Resetting the Database After Each Test](#resetting-the-database-after-each-test)

Most of the tests you write will interact with pages that retrieve data from your application's database; however, your Dusk tests should never use the `RefreshDatabase` trait. The `RefreshDatabase` trait leverages database transactions which will not be applicable or available across HTTP requests. Instead, you have two options: the `DatabaseMigrations` trait and the `DatabaseTruncation` trait.

#### [Using Database Migrations](#reset-migrations)

The `DatabaseMigrations` trait will run your database migrations before each test. However, dropping and re-creating your database tables for each test is typically slower than truncating the tables:

Pest
PHPUnit

```
<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

pest()->use(DatabaseMigrations::class);

//
```

```
<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    //
}
```

SQLite in-memory databases may not be used when executing Dusk tests. Since the browser executes within its own process, it will not be able to access the in-memory databases of other processes.

#### [Using Database Truncation](#reset-truncation)

The `DatabaseTruncation` trait will migrate your database on the first test in order to ensure your database tables have been properly created. However, on subsequent tests, the database's tables will simply be truncated - providing a speed boost over re-running all of your database migrations:

Pest
PHPUnit

```
<?php

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;

pest()->use(DatabaseTruncation::class);

//
```

```
<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseTruncation;

    //
}
```

By default, this trait will truncate all tables except the `migrations` table. If you would like to customize the tables that should be truncated, you may define a `$tablesToTruncate` property on your test class:

If you are using Pest, you should define properties or methods on the base `DuskTestCase` class or on any class your test file extends.

```
/**
 * Indicates which tables should be truncated.
 *
 * @var array
 */
protected $tablesToTruncate = ['users'];
```

Alternatively, you may define an `$exceptTables` property on your test class to specify which tables should be excluded from truncation:

```
/**
 * Indicates which tables should be excluded from truncation.
 *
 * @var array
 */
protected $exceptTables = ['users'];
```

To specify the database connections that should have their tables truncated, you may define a `$connectionsToTruncate` property on your test class:

```
/**
 * Indicates which connections should have their tables truncated.
 *
 * @var array
 */
protected $connectionsToTruncate = ['mysql'];
```

If you would like to execute code before or after database truncation is performed, you may define `beforeTruncatingDatabase` or `afterTruncatingDatabase` methods on your test class:

```
/**
 * Perform any work that should take place before the database has started truncating.
 */
protected function beforeTruncatingDatabase(): void
{
    //
}

/**
 * Perform any work that should take place after the database has finished truncating.
 */
protected function afterTruncatingDatabase(): void
{
    //
}
```

### [Running Tests](#running-tests)

To run your browser tests, execute the `dusk` Artisan command:

```
php artisan dusk
```

If you had test failures the last time you ran the `dusk` command, you may save time by re-running the failing tests first using the `dusk:fails` command:

```
php artisan dusk:fails
```

The `dusk` command accepts any argument that is normally accepted by the Pest / PHPUnit test runner, such as allowing you to only run the tests for a given [group](https://docs.phpunit.de/en/10.5/annotations.html#group):

```
php artisan dusk --group=foo
```

If you are using [Laravel Sail](sail.md) to manage your local development environment, please consult the Sail documentation on [configuring and running Dusk tests](sail.md#laravel-dusk).

#### [Manually Starting ChromeDriver](#manually-starting-chromedriver)

By default, Dusk will automatically attempt to start ChromeDriver. If this does not work for your particular system, you may manually start ChromeDriver before running the `dusk` command. If you choose to start ChromeDriver manually, you should comment out the following line of your `tests/DuskTestCase.php` file:

```
/**
 * Prepare for Dusk test execution.
 *
 * @beforeClass
 */
public static function prepare(): void
{
    // static::startChromeDriver();
}
```

In addition, if you start ChromeDriver on a port other than 9515, you should modify the `driver` method of the same class to reflect the correct port:

```
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Create the RemoteWebDriver instance.
 */
protected function driver(): RemoteWebDriver
{
    return RemoteWebDriver::create(
        'http://localhost:9515', DesiredCapabilities::chrome()
    );
}
```

### [Environment Handling](#environment-handling)

To force Dusk to use its own environment file when running tests, create a `.env.dusk.{environment}` file in the root of your project. For example, if you will be initiating the `dusk` command from your `local` environment, you should create a `.env.dusk.local` file.

When running tests, Dusk will back-up your `.env` file and rename your Dusk environment to `.env`. Once the tests have completed, your `.env` file will be restored.

## [Browser Basics](#browser-basics)

### [Creating Browsers](#creating-browsers)

To get started, let's write a test that verifies we can log into our application. After generating a test, we can modify it to navigate to the login page, enter some credentials, and click the "Login" button. To create a browser instance, you may call the `browse` method from within your Dusk test:

Pest
PHPUnit

```
<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;

pest()->use(DatabaseMigrations::class);

test('basic example', function () {
    $user = User::factory()->create([
        'email' => '[email protected]',
    ]);

    $this->browse(function (Browser $browser) use ($user) {
        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Login')
            ->assertPathIs('/home');
    });
});
```

```
<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A basic browser test example.
     */
    public function test_basic_example(): void
    {
        $user = User::factory()->create([
            'email' => '[email protected]',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Login')
                ->assertPathIs('/home');
        });
    }
}
```

As you can see in the example above, the `browse` method accepts a closure. A browser instance will automatically be passed to this closure by Dusk and is the main object used to interact with and make assertions against your application.

#### [Creating Multiple Browsers](#creating-multiple-browsers)

Sometimes you may need multiple browsers in order to properly carry out a test. For example, multiple browsers may be needed to test a chat screen that interacts with websockets. To create multiple browsers, simply add more browser arguments to the signature of the closure given to the `browse` method:

```
$this->browse(function (Browser $first, Browser $second) {
    $first->loginAs(User::find(1))
        ->visit('/home')
        ->waitForText('Message');

    $second->loginAs(User::find(2))
        ->visit('/home')
        ->waitForText('Message')
        ->type('message', 'Hey Taylor')
        ->press('Send');

    $first->waitForText('Hey Taylor')
        ->assertSee('Jeffrey Way');
});
```

### [Navigation](#navigation)

The `visit` method may be used to navigate to a given URI within your application:

```
$browser->visit('/login');
```

You may use the `visitRoute` method to navigate to a [named route](../the-basics/routing.md#named-routes):

```
$browser->visitRoute($routeName, $parameters);
```

You may navigate "back" and "forward" using the `back` and `forward` methods:

```
$browser->back();

$browser->forward();
```

You may use the `refresh` method to refresh the page:

```
$browser->refresh();
```

### [Resizing Browser Windows](#resizing-browser-windows)

You may use the `resize` method to adjust the size of the browser window:

```
$browser->resize(1920, 1080);
```

The `maximize` method may be used to maximize the browser window:

```
$browser->maximize();
```

The `fitContent` method will resize the browser window to match the size of its content:

```
$browser->fitContent();
```

When a test fails, Dusk will automatically resize the browser to fit the content prior to taking a screenshot. You may disable this feature by calling the `disableFitOnFailure` method within your test:

```
$browser->disableFitOnFailure();
```

You may use the `move` method to move the browser window to a different position on your screen:

```
$browser->move($x = 100, $y = 100);
```

### [Browser Macros](#browser-macros)

If you would like to define a custom browser method that you can re-use in a variety of your tests, you may use the `macro` method on the `Browser` class. Typically, you should call this method from a [service provider's](../architecture-concepts/providers.md) `boot` method:

```
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register Dusk's browser macros.
     */
    public function boot(): void
    {
        Browser::macro('scrollToElement', function (string $element = null) {
            $this->script("$('html, body').animate({ scrollTop: $('$element').offset().top }, 0);");

            return $this;
        });
    }
}
```

The `macro` function accepts a name as its first argument, and a closure as its second. The macro's closure will be executed when calling the macro as a method on a `Browser` instance:

```
$this->browse(function (Browser $browser) use ($user) {
    $browser->visit('/pay')
        ->scrollToElement('#credit-card-details')
        ->assertSee('Enter Credit Card Details');
});
```

### [Authentication](#authentication)

Often, you will be testing pages that require authentication. You can use Dusk's `loginAs` method in order to avoid interacting with your application's login screen during every test. The `loginAs` method accepts a primary key associated with your authenticatable model or an authenticatable model instance:

```
use App\Models\User;
use Laravel\Dusk\Browser;

$this->browse(function (Browser $browser) {
    $browser->loginAs(User::find(1))
        ->visit('/home');
});
```

After using the `loginAs` method, the user session will be maintained for all tests within the file.

### [Cookies](#cookies)

You may use the `cookie` method to get or set an encrypted cookie's value. By default, all of the cookies created by Laravel are encrypted:

```
$browser->cookie('name');

$browser->cookie('name', 'Taylor');
```

You may use the `plainCookie` method to get or set an unencrypted cookie's value:

```
$browser->plainCookie('name');

$browser->plainCookie('name', 'Taylor');
```

You may use the `deleteCookie` method to delete the given cookie:

```
$browser->deleteCookie('name');
```

### [Executing JavaScript](#executing-javascript)

You may use the `script` method to execute arbitrary JavaScript statements within the browser:

```
$browser->script('document.documentElement.scrollTop = 0');

$browser->script([
    'document.body.scrollTop = 0',
    'document.documentElement.scrollTop = 0',
]);

$output = $browser->script('return window.location.pathname');
```

### [Taking a Screenshot](#taking-a-screenshot)

You may use the `screenshot` method to take a screenshot and store it with the given filename. All screenshots will be stored within the `tests/Browser/screenshots` directory:

```
$browser->screenshot('filename');
```

The `responsiveScreenshots` method may be used to take a series of screenshots at various breakpoints:

```
$browser->responsiveScreenshots('filename');
```

The `screenshotElement` method may be used to take a screenshot of a specific element on the page:

```
$browser->screenshotElement('#selector', 'filename');
```

### [Storing Console Output to Disk](#storing-console-output-to-disk)

You may use the `storeConsoleLog` method to write the current browser's console output to disk with the given filename. Console output will be stored within the `tests/Browser/console` directory:

```
$browser->storeConsoleLog('filename');
```

### [Storing Page Source to Disk](#storing-page-source-to-disk)

You may use the `storeSource` method to write the current page's source to disk with the given filename. The page source will be stored within the `tests/Browser/source` directory:

```
$browser->storeSource('filename');
```

## [Interacting With Elements](#interacting-with-elements)

### [Dusk Selectors](#dusk-selectors)

Choosing good CSS selectors for interacting with elements is one of the hardest parts of writing Dusk tests. Over time, frontend changes can cause CSS selectors like the following to break your tests:

```
// HTML...

<button>Login</button>
```

```
// Test...

$browser->click('.login-page .container div > button');
```

Dusk selectors allow you to focus on writing effective tests rather than remembering CSS selectors. To define a selector, add a `dusk` attribute to your HTML element. Then, when interacting with a Dusk browser, prefix the selector with `@` to manipulate the attached element within your test:

```
// HTML...

<button dusk="login-button">Login</button>
```

```
// Test...

$browser->click('@login-button');
```

If desired, you may customize the HTML attribute that the Dusk selector utilizes via the `selectorHtmlAttribute` method. Typically, this method should be called from the `boot` method of your application's `AppServiceProvider`:

```
use Laravel\Dusk\Dusk;

Dusk::selectorHtmlAttribute('data-dusk');
```

### [Text, Values, and Attributes](#text-values-and-attributes)

#### [Retrieving and Setting Values](#retrieving-setting-values)

Dusk provides several methods for interacting with the current value, display text, and attributes of elements on the page. For example, to get the "value" of an element that matches a given CSS or Dusk selector, use the `value` method:

```
// Retrieve the value...
$value = $browser->value('selector');

// Set the value...
$browser->value('selector', 'value');
```

You may use the `inputValue` method to get the "value" of an input element that has a given field name:

```
$value = $browser->inputValue('field');
```

#### [Retrieving Text](#retrieving-text)

The `text` method may be used to retrieve the display text of an element that matches the given selector:

```
$text = $browser->text('selector');
```

#### [Retrieving Attributes](#retrieving-attributes)

Finally, the `attribute` method may be used to retrieve the value of an attribute of an element matching the given selector:

```
$attribute = $browser->attribute('selector', 'value');
```

### [Interacting With Forms](#interacting-with-forms)

#### [Typing Values](#typing-values)

Dusk provides a variety of methods for interacting with forms and input elements. First, let's take a look at an example of typing text into an input field:

```
$browser->type('email', '[email protected]');
```

Note that, although the method accepts one if necessary, we are not required to pass a CSS selector into the `type` method. If a CSS selector is not provided, Dusk will search for an `input` or `textarea` field with the given `name` attribute.

To append text to a field without clearing its content, you may use the `append` method:

```
$browser->type('tags', 'foo')
    ->append('tags', ', bar, baz');
```

You may clear the value of an input using the `clear` method:

```
$browser->clear('email');
```

You can instruct Dusk to type slowly using the `typeSlowly` method. By default, Dusk will pause for 100 milliseconds between key presses. To customize the amount of time between key presses, you may pass the appropriate number of milliseconds as the third argument to the method:

```
$browser->typeSlowly('mobile', '+1 (202) 555-5555');

$browser->typeSlowly('mobile', '+1 (202) 555-5555', 300);
```

You may use the `appendSlowly` method to append text slowly:

```
$browser->type('tags', 'foo')
    ->appendSlowly('tags', ', bar, baz');
```

#### [Dropdowns](#dropdowns)

To select a value available on a `select` element, you may use the `select` method. Like the `type` method, the `select` method does not require a full CSS selector. When passing a value to the `select` method, you should pass the underlying option value instead of the display text:

```
$browser->select('size', 'Large');
```

You may select a random option by omitting the second argument:

```
$browser->select('size');
```

By providing an array as the second argument to the `select` method, you can instruct the method to select multiple options:

```
$browser->select('categories', ['Art', 'Music']);
```

#### [Checkboxes](#checkboxes)

To "check" a checkbox input, you may use the `check` method. Like many other input related methods, a full CSS selector is not required. If a CSS selector match can't be found, Dusk will search for a checkbox with a matching `name` attribute:

```
$browser->check('terms');
```

The `uncheck` method may be used to "uncheck" a checkbox input:

```
$browser->uncheck('terms');
```

#### [Radio Buttons](#radio-buttons)

To "select" a `radio` input option, you may use the `radio` method. Like many other input related methods, a full CSS selector is not required. If a CSS selector match can't be found, Dusk will search for a `radio` input with matching `name` and `value` attributes:

```
$browser->radio('size', 'large');
```

### [Attaching Files](#attaching-files)

The `attach` method may be used to attach a file to a `file` input element. Like many other input related methods, a full CSS selector is not required. If a CSS selector match can't be found, Dusk will search for a `file` input with a matching `name` attribute:

```
$browser->attach('photo', __DIR__.'/photos/mountains.png');
```

The attach function requires the `Zip` PHP extension to be installed and enabled on your server.

### [Pressing Buttons](#pressing-buttons)

The `press` method may be used to click a button element on the page. The argument given to the `press` method may be either the display text of the button or a CSS / Dusk selector:

```
$browser->press('Login');
```

When submitting forms, many applications disable the form's submission button after it is pressed and then re-enable the button when the form submission's HTTP request is complete. To press a button and wait for the button to be re-enabled, you may use the `pressAndWaitFor` method:

```
// Press the button and wait a maximum of 5 seconds for it to be enabled...
$browser->pressAndWaitFor('Save');

// Press the button and wait a maximum of 1 second for it to be enabled...
$browser->pressAndWaitFor('Save', 1);
```

### [Clicking Links](#clicking-links)

To click a link, you may use the `clickLink` method on the browser instance. The `clickLink` method will click the link that has the given display text:

```
$browser->clickLink($linkText);
```

You may use the `seeLink` method to determine if a link with the given display text is visible on the page:

```
if ($browser->seeLink($linkText)) {
    // ...
}
```

These methods interact with jQuery. If jQuery is not available on the page, Dusk will automatically inject it into the page so it is available for the test's duration.

### [Using the Keyboard](#using-the-keyboard)

The `keys` method allows you to provide more complex input sequences to a given element than normally allowed by the `type` method. For example, you may instruct Dusk to hold modifier keys while entering values. In this example, the `shift` key will be held while `taylor` is entered into the element matching the given selector. After `taylor` is typed, `swift` will be typed without any modifier keys:

```
$browser->keys('selector', ['{shift}', 'taylor'], 'swift');
```

Another valuable use case for the `keys` method is sending a "keyboard shortcut" combination to the primary CSS selector for your application:

```
$browser->keys('.app', ['{command}', 'j']);
```

All modifier keys such as `{command}` are wrapped in `{}` characters, and match the constants defined in the `Facebook\WebDriver\WebDriverKeys` class, which can be [found on GitHub](https://github.com/php-webdriver/php-webdriver/blob/master/lib/WebDriverKeys.php).

#### [Fluent Keyboard Interactions](#fluent-keyboard-interactions)

Dusk also provides a `withKeyboard` method, allowing you to fluently perform complex keyboard interactions via the `Laravel\Dusk\Keyboard` class. The `Keyboard` class provides `press`, `release`, `type`, and `pause` methods:

```
use Laravel\Dusk\Keyboard;

$browser->withKeyboard(function (Keyboard $keyboard) {
    $keyboard->press('c')
        ->pause(1000)
        ->release('c')
        ->type(['c', 'e', 'o']);
});
```

#### [Keyboard Macros](#keyboard-macros)

If you would like to define custom keyboard interactions that you can easily re-use throughout your test suite, you may use the `macro` method provided by the `Keyboard` class. Typically, you should call this method from a [service provider's](../architecture-concepts/providers.md) `boot` method:

```
<?php

namespace App\Providers;

use Facebook\WebDriver\WebDriverKeys;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Keyboard;
use Laravel\Dusk\OperatingSystem;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register Dusk's browser macros.
     */
    public function boot(): void
    {
        Keyboard::macro('copy', function (string $element = null) {
            $this->type([
                OperatingSystem::onMac() ? WebDriverKeys::META : WebDriverKeys::CONTROL, 'c',
            ]);

            return $this;
        });

        Keyboard::macro('paste', function (string $element = null) {
            $this->type([
                OperatingSystem::onMac() ? WebDriverKeys::META : WebDriverKeys::CONTROL, 'v',
            ]);

            return $this;
        });
    }
}
```

The `macro` function accepts a name as its first argument and a closure as its second. The macro's closure will be executed when calling the macro as a method on a `Keyboard` instance:

```
$browser->click('@textarea')
    ->withKeyboard(fn (Keyboard $keyboard) => $keyboard->copy())
    ->click('@another-textarea')
    ->withKeyboard(fn (Keyboard $keyboard) => $keyboard->paste());
```

### [Using the Mouse](#using-the-mouse)

#### [Clicking on Elements](#clicking-on-elements)

The `click` method may be used to click on an element matching the given CSS or Dusk selector:

```
$browser->click('.selector');
```

The `clickAtXPath` method may be used to click on an element matching the given XPath expression:

```
$browser->clickAtXPath('//div[@class = "selector"]');
```

The `clickAtPoint` method may be used to click on the topmost element at a given pair of coordinates relative to the viewable area of the browser:

```
$browser->clickAtPoint($x = 0, $y = 0);
```

The `doubleClick` method may be used to simulate the double click of a mouse:

```
$browser->doubleClick();

$browser->doubleClick('.selector');
```

The `rightClick` method may be used to simulate the right click of a mouse:

```
$browser->rightClick();

$browser->rightClick('.selector');
```

The `clickAndHold` method may be used to simulate a mouse button being clicked and held down. A subsequent call to the `releaseMouse` method will undo this behavior and release the mouse button:

```
$browser->clickAndHold('.selector');

$browser->clickAndHold()
    ->pause(1000)
    ->releaseMouse();
```

The `controlClick` method may be used to simulate the `ctrl+click` event within the browser:

```
$browser->controlClick();

$browser->controlClick('.selector');
```

The `clickWhenVisible` or `clickWhenEnabled` method may be used to wait for an element to be ready before clicking it exactly once:

```
$browser->clickWhenVisible('@save-button');
$browser->clickWhenEnabled('@submit-button');
```

#### [Mouseover](#mouseover)

The `mouseover` method may be used when you need to move the mouse over an element matching the given CSS or Dusk selector:

```
$browser->mouseover('.selector');
```

#### [Drag and Drop](#drag-drop)

The `drag` method may be used to drag an element matching the given selector to another element:

```
$browser->drag('.from-selector', '.to-selector');
```

Or, you may drag an element in a single direction:

```
$browser->dragLeft('.selector', $pixels = 10);
$browser->dragRight('.selector', $pixels = 10);
$browser->dragUp('.selector', $pixels = 10);
$browser->dragDown('.selector', $pixels = 10);
```

Finally, you may drag an element by a given offset:

```
$browser->dragOffset('.selector', $x = 10, $y = 10);
```

### [JavaScript Dialogs](#javascript-dialogs)

Dusk provides various methods to interact with JavaScript Dialogs. For example, you may use the `waitForDialog` method to wait for a JavaScript dialog to appear. This method accepts an optional argument indicating how many seconds to wait for the dialog to appear:

```
$browser->waitForDialog($seconds = null);
```

The `assertDialogOpened` method may be used to assert that a dialog has been displayed and contains the given message:

```
$browser->assertDialogOpened('Dialog message');
```

If the JavaScript dialog contains a prompt, you may use the `typeInDialog` method to type a value into the prompt:

```
$browser->typeInDialog('Hello World');
```

To close an open JavaScript dialog by clicking the "OK" button, you may invoke the `acceptDialog` method:

```
$browser->acceptDialog();
```

To close an open JavaScript dialog by clicking the "Cancel" button, you may invoke the `dismissDialog` method:

```
$browser->dismissDialog();
```

### [Interacting With Inline Frames](#interacting-with-iframes)

If you need to interact with elements within an iframe, you may use the `withinFrame` method. All element interactions that take place within the closure provided to the `withinFrame` method will be scoped to the context of the specified iframe:

```
$browser->withinFrame('#credit-card-details', function ($browser) {
    $browser->type('input[name="cardnumber"]', '4242424242424242')
        ->type('input[name="exp-date"]', '1224')
        ->type('input[name="cvc"]', '123')
        ->press('Pay');
});
```

### [Scoping Selectors](#scoping-selectors)

Sometimes you may wish to perform several operations while scoping all of the operations within a given selector. For example, you may wish to assert that some text exists only within a table and then click a button within that table. You may use the `with` method to accomplish this. All operations performed within the closure given to the `with` method will be scoped to the original selector:

```
$browser->with('.table', function (Browser $table) {
    $table->assertSee('Hello World')
        ->clickLink('Delete');
});
```

You may occasionally need to execute assertions outside of the current scope. You may use the `elsewhere` and `elsewhereWhenAvailable` methods to accomplish this:

```
$browser->with('.table', function (Browser $table) {
    // Current scope is `body .table`...

    $browser->elsewhere('.page-title', function (Browser $title) {
        // Current scope is `body .page-title`...
        $title->assertSee('Hello World');
    });

    $browser->elsewhereWhenAvailable('.page-title', function (Browser $title) {
        // Current scope is `body .page-title`...
        $title->assertSee('Hello World');
    });
});
```

### [Waiting for Elements](#waiting-for-elements)

When testing applications that use JavaScript extensively, it often becomes necessary to "wait" for certain elements or data to be available before proceeding with a test. Dusk makes this a cinch. Using a variety of methods, you may wait for elements to become visible on the page or even wait until a given JavaScript expression evaluates to `true`.

#### [Waiting](#waiting)

If you just need to pause the test for a given number of milliseconds, use the `pause` method:

```
$browser->pause(1000);
```

If you need to pause the test only if a given condition is `true`, use the `pauseIf` method:

```
$browser->pauseIf(App::environment('production'), 1000);
```

Likewise, if you need to pause the test unless a given condition is `true`, you may use the `pauseUnless` method:

```
$browser->pauseUnless(App::environment('testing'), 1000);
```

#### [Waiting for Selectors](#waiting-for-selectors)

The `waitFor` method may be used to pause the execution of the test until the element matching the given CSS or Dusk selector is displayed on the page. By default, this will pause the test for a maximum of five seconds before throwing an exception. If necessary, you may pass a custom timeout threshold as the second argument to the method:

```
// Wait a maximum of five seconds for the selector...
$browser->waitFor('.selector');

// Wait a maximum of one second for the selector...
$browser->waitFor('.selector', 1);
```

You may also wait until the element matching the given selector contains the given text:

```
// Wait a maximum of five seconds for the selector to contain the given text...
$browser->waitForTextIn('.selector', 'Hello World');

// Wait a maximum of one second for the selector to contain the given text...
$browser->waitForTextIn('.selector', 'Hello World', 1);
```

You may also wait until the element matching the given selector is missing from the page:

```
// Wait a maximum of five seconds until the selector is missing...
$browser->waitUntilMissing('.selector');

// Wait a maximum of one second until the selector is missing...
$browser->waitUntilMissing('.selector', 1);
```

Or, you may wait until the element matching the given selector is enabled or disabled:

```
// Wait a maximum of five seconds until the selector is enabled...
$browser->waitUntilEnabled('.selector');

// Wait a maximum of one second until the selector is enabled...
$browser->waitUntilEnabled('.selector', 1);

// Wait a maximum of five seconds until the selector is disabled...
$browser->waitUntilDisabled('.selector');

// Wait a maximum of one second until the selector is disabled...
$browser->waitUntilDisabled('.selector', 1);
```

#### [Scoping Selectors When Available](#scoping-selectors-when-available)

Occasionally, you may wish to wait for an element to appear that matches a given selector and then interact with the element. For example, you may wish to wait until a modal window is available and then press the "OK" button within the modal. The `whenAvailable` method may be used to accomplish this. All element operations performed within the given closure will be scoped to the original selector:

```
$browser->whenAvailable('.modal', function (Browser $modal) {
    $modal->assertSee('Hello World')
        ->press('OK');
});
```

#### [Waiting for Text](#waiting-for-text)

The `waitForText` method may be used to wait until the given text is displayed on the page:

```
// Wait a maximum of five seconds for the text...
$browser->waitForText('Hello World');

// Wait a maximum of one second for the text...
$browser->waitForText('Hello World', 1);
```

You may use the `waitUntilMissingText` method to wait until the displayed text has been removed from the page:

```
// Wait a maximum of five seconds for the text to be removed...
$browser->waitUntilMissingText('Hello World');

// Wait a maximum of one second for the text to be removed...
$browser->waitUntilMissingText('Hello World', 1);
```

#### [Waiting for Links](#waiting-for-links)

The `waitForLink` method may be used to wait until the given link text is displayed on the page:

```
// Wait a maximum of five seconds for the link...
$browser->waitForLink('Create');

// Wait a maximum of one second for the link...
$browser->waitForLink('Create', 1);
```

#### [Waiting for Inputs](#waiting-for-inputs)

The `waitForInput` method may be used to wait until the given input field is visible on the page:

```
// Wait a maximum of five seconds for the input...
$browser->waitForInput($field);

// Wait a maximum of one second for the input...
$browser->waitForInput($field, 1);
```

#### [Waiting on the Page Location](#waiting-on-the-page-location)

When making a path assertion such as `$browser->assertPathIs('/home')`, the assertion can fail if `window.location.pathname` is being updated asynchronously. You may use the `waitForLocation` method to wait for the location to be a given value:

```
$browser->waitForLocation('/secret');
```

The `waitForLocation` method can also be used to wait for the current window location to be a fully qualified URL:

```
$browser->waitForLocation('https://example.com/path');
```

You may also wait for a [named route's](../the-basics/routing.md#named-routes) location:

```
$browser->waitForRoute($routeName, $parameters);
```

#### [Waiting for Page Reloads](#waiting-for-page-reloads)

If you need to wait for a page to reload after performing an action, use the `waitForReload` method:

```
use Laravel\Dusk\Browser;

$browser->waitForReload(function (Browser $browser) {
    $browser->press('Submit');
})
->assertSee('Success!');
```

Since the need to wait for the page to reload typically occurs after clicking a button, you may use the `clickAndWaitForReload` method for convenience:

```
$browser->clickAndWaitForReload('.selector')
    ->assertSee('something');
```

#### [Waiting on JavaScript Expressions](#waiting-on-javascript-expressions)

Sometimes you may wish to pause the execution of a test until a given JavaScript expression evaluates to `true`. You may easily accomplish this using the `waitUntil` method. When passing an expression to this method, you do not need to include the `return` keyword or an ending semi-colon:

```
// Wait a maximum of five seconds for the expression to be true...
$browser->waitUntil('App.data.servers.length > 0');

// Wait a maximum of one second for the expression to be true...
$browser->waitUntil('App.data.servers.length > 0', 1);
```

#### [Waiting on Vue Expressions](#waiting-on-vue-expressions)

The `waitUntilVue` and `waitUntilVueIsNot` methods may be used to wait until a [Vue component](https://vuejs.org) attribute has a given value:

```
// Wait until the component attribute contains the given value...
$browser->waitUntilVue('user.name', 'Taylor', '@user');

// Wait until the component attribute doesn't contain the given value...
$browser->waitUntilVueIsNot('user.name', null, '@user');
```

#### [Waiting for JavaScript Events](#waiting-for-javascript-events)

The `waitForEvent` method can be used to pause the execution of a test until a JavaScript event occurs:

```
$browser->waitForEvent('load');
```

The event listener is attached to the current scope, which is the `body` element by default. When using a scoped selector, the event listener will be attached to the matching element:

```
$browser->with('iframe', function (Browser $iframe) {
    // Wait for the iframe's load event...
    $iframe->waitForEvent('load');
});
```

You may also provide a selector as the second argument to the `waitForEvent` method to attach the event listener to a specific element:

```
$browser->waitForEvent('load', '.selector');
```

You may also wait for events on the `document` and `window` objects:

```
// Wait until the document is scrolled...
$browser->waitForEvent('scroll', 'document');

// Wait a maximum of five seconds until the window is resized...
$browser->waitForEvent('resize', 'window', 5);
```

#### [Waiting With a Callback](#waiting-with-a-callback)

Many of the "wait" methods in Dusk rely on the underlying `waitUsing` method. You may use this method directly to wait for a given closure to return `true`. The `waitUsing` method accepts the maximum number of seconds to wait, the interval at which the closure should be evaluated, the closure, and an optional failure message:

```
$browser->waitUsing(10, 1, function () use ($something) {
    return $something->isReady();
}, "Something wasn't ready in time.");
```

### [Scrolling an Element Into View](#scrolling-an-element-into-view)

Sometimes you may not be able to click on an element because it is outside of the viewable area of the browser. The `scrollIntoView` method will scroll the browser window until the element at the given selector is within the view:

```
$browser->scrollIntoView('.selector')
    ->click('.selector');
```

## [Available Assertions](#available-assertions)

Dusk provides a variety of assertions that you may make against your application. All of the available assertions are documented in the list below:

[assertTitle](#assert-title)
[assertTitleContains](#assert-title-contains)
[assertUrlIs](#assert-url-is)
[assertSchemeIs](#assert-scheme-is)
[assertSchemeIsNot](#assert-scheme-is-not)
[assertHostIs](#assert-host-is)
[assertHostIsNot](#assert-host-is-not)
[assertPortIs](#assert-port-is)
[assertPortIsNot](#assert-port-is-not)
[assertPathBeginsWith](#assert-path-begins-with)
[assertPathEndsWith](#assert-path-ends-with)
[assertPathContains](#assert-path-contains)
[assertPathIs](#assert-path-is)
[assertPathIsNot](#assert-path-is-not)
[assertRouteIs](#assert-route-is)
[assertQueryStringHas](#assert-query-string-has)
[assertQueryStringMissing](#assert-query-string-missing)
[assertFragmentIs](#assert-fragment-is)
[assertFragmentBeginsWith](#assert-fragment-begins-with)
[assertFragmentIsNot](#assert-fragment-is-not)
[assertHasCookie](#assert-has-cookie)
[assertHasPlainCookie](#assert-has-plain-cookie)
[assertCookieMissing](#assert-cookie-missing)
[assertPlainCookieMissing](#assert-plain-cookie-missing)
[assertCookieValue](#assert-cookie-value)
[assertPlainCookieValue](#assert-plain-cookie-value)
[assertSee](#assert-see)
[assertDontSee](#assert-dont-see)
[assertSeeIn](#assert-see-in)
[assertDontSeeIn](#assert-dont-see-in)
[assertSeeAnythingIn](#assert-see-anything-in)
[assertSeeNothingIn](#assert-see-nothing-in)
[assertCount](#assert-count)
[assertScript](#assert-script)
[assertSourceHas](#assert-source-has)
[assertSourceMissing](#assert-source-missing)
[assertSeeLink](#assert-see-link)
[assertDontSeeLink](#assert-dont-see-link)
[assertInputValue](#assert-input-value)
[assertInputValueIsNot](#assert-input-value-is-not)
[assertChecked](#assert-checked)
[assertNotChecked](#assert-not-checked)
[assertIndeterminate](#assert-indeterminate)
[assertRadioSelected](#assert-radio-selected)
[assertRadioNotSelected](#assert-radio-not-selected)
[assertSelected](#assert-selected)
[assertNotSelected](#assert-not-selected)
[assertSelectHasOptions](#assert-select-has-options)
[assertSelectMissingOptions](#assert-select-missing-options)
[assertSelectHasOption](#assert-select-has-option)
[assertSelectMissingOption](#assert-select-missing-option)
[assertValue](#assert-value)
[assertValueIsNot](#assert-value-is-not)
[assertAttribute](#assert-attribute)
[assertAttributeMissing](#assert-attribute-missing)
[assertAttributeContains](#assert-attribute-contains)
[assertAttributeDoesntContain](#assert-attribute-doesnt-contain)
[assertAriaAttribute](#assert-aria-attribute)
[assertDataAttribute](#assert-data-attribute)
[assertVisible](#assert-visible)
[assertPresent](#assert-present)
[assertNotPresent](#assert-not-present)
[assertMissing](#assert-missing)
[assertInputPresent](#assert-input-present)
[assertInputMissing](#assert-input-missing)
[assertDialogOpened](#assert-dialog-opened)
[assertEnabled](#assert-enabled)
[assertDisabled](#assert-disabled)
[assertButtonEnabled](#assert-button-enabled)
[assertButtonDisabled](#assert-button-disabled)
[assertFocused](#assert-focused)
[assertNotFocused](#assert-not-focused)
[assertAuthenticated](#assert-authenticated)
[assertGuest](#assert-guest)
[assertAuthenticatedAs](#assert-authenticated-as)
[assertVue](#assert-vue)
[assertVueIsNot](#assert-vue-is-not)
[assertVueContains](#assert-vue-contains)
[assertVueDoesntContain](#assert-vue-doesnt-contain)

#### [assertTitle](#assert-title)

Assert that the page title matches the given text:

```
$browser->assertTitle($title);
```

#### [assertTitleContains](#assert-title-contains)

Assert that the page title contains the given text:

```
$browser->assertTitleContains($title);
```

#### [assertUrlIs](#assert-url-is)

Assert that the current URL (without the query string) matches the given string:

```
$browser->assertUrlIs($url);
```

#### [assertSchemeIs](#assert-scheme-is)

Assert that the current URL scheme matches the given scheme:

```
$browser->assertSchemeIs($scheme);
```

#### [assertSchemeIsNot](#assert-scheme-is-not)

Assert that the current URL scheme does not match the given scheme:

```
$browser->assertSchemeIsNot($scheme);
```

#### [assertHostIs](#assert-host-is)

Assert that the current URL host matches the given host:

```
$browser->assertHostIs($host);
```

#### [assertHostIsNot](#assert-host-is-not)

Assert that the current URL host does not match the given host:

```
$browser->assertHostIsNot($host);
```

#### [assertPortIs](#assert-port-is)

Assert that the current URL port matches the given port:

```
$browser->assertPortIs($port);
```

#### [assertPortIsNot](#assert-port-is-not)

Assert that the current URL port does not match the given port:

```
$browser->assertPortIsNot($port);
```

#### [assertPathBeginsWith](#assert-path-begins-with)

Assert that the current URL path begins with the given path:

```
$browser->assertPathBeginsWith('/home');
```

#### [assertPathEndsWith](#assert-path-ends-with)

Assert that the current URL path ends with the given path:

```
$browser->assertPathEndsWith('/home');
```

#### [assertPathContains](#assert-path-contains)

Assert that the current URL path contains the given path:

```
$browser->assertPathContains('/home');
```

#### [assertPathIs](#assert-path-is)

Assert that the current path matches the given path:

```
$browser->assertPathIs('/home');
```

#### [assertPathIsNot](#assert-path-is-not)

Assert that the current path does not match the given path:

```
$browser->assertPathIsNot('/home');
```

#### [assertRouteIs](#assert-route-is)

Assert that the current URL matches the given [named route's](../the-basics/routing.md#named-routes) URL:

```
$browser->assertRouteIs($name, $parameters);
```

#### [assertQueryStringHas](#assert-query-string-has)

Assert that the given query string parameter is present:

```
$browser->assertQueryStringHas($name);
```

Assert that the given query string parameter is present and has a given value:

```
$browser->assertQueryStringHas($name, $value);
```

#### [assertQueryStringMissing](#assert-query-string-missing)

Assert that the given query string parameter is missing:

```
$browser->assertQueryStringMissing($name);
```

#### [assertFragmentIs](#assert-fragment-is)

Assert that the URL's current hash fragment matches the given fragment:

```
$browser->assertFragmentIs('anchor');
```

#### [assertFragmentBeginsWith](#assert-fragment-begins-with)

Assert that the URL's current hash fragment begins with the given fragment:

```
$browser->assertFragmentBeginsWith('anchor');
```

#### [assertFragmentIsNot](#assert-fragment-is-not)

Assert that the URL's current hash fragment does not match the given fragment:

```
$browser->assertFragmentIsNot('anchor');
```

#### [assertHasCookie](#assert-has-cookie)

Assert that the given encrypted cookie is present:

```
$browser->assertHasCookie($name);
```

#### [assertHasPlainCookie](#assert-has-plain-cookie)

Assert that the given unencrypted cookie is present:

```
$browser->assertHasPlainCookie($name);
```

#### [assertCookieMissing](#assert-cookie-missing)

Assert that the given encrypted cookie is not present:

```
$browser->assertCookieMissing($name);
```

#### [assertPlainCookieMissing](#assert-plain-cookie-missing)

Assert that the given unencrypted cookie is not present:

```
$browser->assertPlainCookieMissing($name);
```

#### [assertCookieValue](#assert-cookie-value)

Assert that an encrypted cookie has a given value:

```
$browser->assertCookieValue($name, $value);
```

#### [assertPlainCookieValue](#assert-plain-cookie-value)

Assert that an unencrypted cookie has a given value:

```
$browser->assertPlainCookieValue($name, $value);
```

#### [assertSee](#assert-see)

Assert that the given text is present on the page:

```
$browser->assertSee($text);
```

#### [assertDontSee](#assert-dont-see)

Assert that the given text is not present on the page:

```
$browser->assertDontSee($text);
```

#### [assertSeeIn](#assert-see-in)

Assert that the given text is present within the selector:

```
$browser->assertSeeIn($selector, $text);
```

#### [assertDontSeeIn](#assert-dont-see-in)

Assert that the given text is not present within the selector:

```
$browser->assertDontSeeIn($selector, $text);
```

#### [assertSeeAnythingIn](#assert-see-anything-in)

Assert that any text is present within the selector:

```
$browser->assertSeeAnythingIn($selector);
```

#### [assertSeeNothingIn](#assert-see-nothing-in)

Assert that no text is present within the selector:

```
$browser->assertSeeNothingIn($selector);
```

#### [assertCount](#assert-count)

Assert that elements matching the given selector appear the specified number of times:

```
$browser->assertCount($selector, $count);
```

#### [assertScript](#assert-script)

Assert that the given JavaScript expression evaluates to the given value:

```
$browser->assertScript('window.isLoaded')
    ->assertScript('document.readyState', 'complete');
```

#### [assertSourceHas](#assert-source-has)

Assert that the given source code is present on the page:

```
$browser->assertSourceHas($code);
```

#### [assertSourceMissing](#assert-source-missing)

Assert that the given source code is not present on the page:

```
$browser->assertSourceMissing($code);
```

#### [assertSeeLink](#assert-see-link)

Assert that the given link is present on the page:

```
$browser->assertSeeLink($linkText);
```

#### [assertDontSeeLink](#assert-dont-see-link)

Assert that the given link is not present on the page:

```
$browser->assertDontSeeLink($linkText);
```

#### [assertInputValue](#assert-input-value)

Assert that the given input field has the given value:

```
$browser->assertInputValue($field, $value);
```

#### [assertInputValueIsNot](#assert-input-value-is-not)

Assert that the given input field does not have the given value:

```
$browser->assertInputValueIsNot($field, $value);
```

#### [assertChecked](#assert-checked)

Assert that the given checkbox is checked:

```
$browser->assertChecked($field);
```

#### [assertNotChecked](#assert-not-checked)

Assert that the given checkbox is not checked:

```
$browser->assertNotChecked($field);
```

#### [assertIndeterminate](#assert-indeterminate)

Assert that the given checkbox is in an indeterminate state:

```
$browser->assertIndeterminate($field);
```

#### [assertRadioSelected](#assert-radio-selected)

Assert that the given radio field is selected:

```
$browser->assertRadioSelected($field, $value);
```

#### [assertRadioNotSelected](#assert-radio-not-selected)

Assert that the given radio field is not selected:

```
$browser->assertRadioNotSelected($field, $value);
```

#### [assertSelected](#assert-selected)

Assert that the given dropdown has the given value selected:

```
$browser->assertSelected($field, $value);
```

#### [assertNotSelected](#assert-not-selected)

Assert that the given dropdown does not have the given value selected:

```
$browser->assertNotSelected($field, $value);
```

#### [assertSelectHasOptions](#assert-select-has-options)

Assert that the given array of values are available to be selected:

```
$browser->assertSelectHasOptions($field, $values);
```

#### [assertSelectMissingOptions](#assert-select-missing-options)

Assert that the given array of values are not available to be selected:

```
$browser->assertSelectMissingOptions($field, $values);
```

#### [assertSelectHasOption](#assert-select-has-option)

Assert that the given value is available to be selected on the given field:

```
$browser->assertSelectHasOption($field, $value);
```

#### [assertSelectMissingOption](#assert-select-missing-option)

Assert that the given value is not available to be selected:

```
$browser->assertSelectMissingOption($field, $value);
```

#### [assertValue](#assert-value)

Assert that the element matching the given selector has the given value:

```
$browser->assertValue($selector, $value);
```

#### [assertValueIsNot](#assert-value-is-not)

Assert that the element matching the given selector does not have the given value:

```
$browser->assertValueIsNot($selector, $value);
```

#### [assertAttribute](#assert-attribute)

Assert that the element matching the given selector has the given value in the provided attribute:

```
$browser->assertAttribute($selector, $attribute, $value);
```

#### [assertAttributeMissing](#assert-attribute-missing)

Assert that the element matching the given selector is missing the provided attribute:

```
$browser->assertAttributeMissing($selector, $attribute);
```

#### [assertAttributeContains](#assert-attribute-contains)

Assert that the element matching the given selector contains the given value in the provided attribute:

```
$browser->assertAttributeContains($selector, $attribute, $value);
```

#### [assertAttributeDoesntContain](#assert-attribute-doesnt-contain)

Assert that the element matching the given selector does not contain the given value in the provided attribute:

```
$browser->assertAttributeDoesntContain($selector, $attribute, $value);
```

#### [assertAriaAttribute](#assert-aria-attribute)

Assert that the element matching the given selector has the given value in the provided aria attribute:

```
$browser->assertAriaAttribute($selector, $attribute, $value);
```

For example, given the markup `<button aria-label="Add"></button>`, you may assert against the `aria-label` attribute like so:

```
$browser->assertAriaAttribute('button', 'label', 'Add')
```

#### [assertDataAttribute](#assert-data-attribute)

Assert that the element matching the given selector has the given value in the provided data attribute:

```
$browser->assertDataAttribute($selector, $attribute, $value);
```

For example, given the markup `<tr id="row-1" data-content="attendees"></tr>`, you may assert against the `data-content` attribute like so:

```
$browser->assertDataAttribute('#row-1', 'content', 'attendees')
```

#### [assertVisible](#assert-visible)

Assert that the element matching the given selector is visible:

```
$browser->assertVisible($selector);
```

#### [assertPresent](#assert-present)

Assert that the element matching the given selector is present in the source:

```
$browser->assertPresent($selector);
```

#### [assertNotPresent](#assert-not-present)

Assert that the element matching the given selector is not present in the source:

```
$browser->assertNotPresent($selector);
```

#### [assertMissing](#assert-missing)

Assert that the element matching the given selector is not visible:

```
$browser->assertMissing($selector);
```

#### [assertInputPresent](#assert-input-present)

Assert that an input with the given name is present:

```
$browser->assertInputPresent($name);
```

#### [assertInputMissing](#assert-input-missing)

Assert that an input with the given name is not present in the source:

```
$browser->assertInputMissing($name);
```

#### [assertDialogOpened](#assert-dialog-opened)

Assert that a JavaScript dialog with the given message has been opened:

```
$browser->assertDialogOpened($message);
```

#### [assertEnabled](#assert-enabled)

Assert that the given field is enabled:

```
$browser->assertEnabled($field);
```

#### [assertDisabled](#assert-disabled)

Assert that the given field is disabled:

```
$browser->assertDisabled($field);
```

#### [assertButtonEnabled](#assert-button-enabled)

Assert that the given button is enabled:

```
$browser->assertButtonEnabled($button);
```

#### [assertButtonDisabled](#assert-button-disabled)

Assert that the given button is disabled:

```
$browser->assertButtonDisabled($button);
```

#### [assertFocused](#assert-focused)

Assert that the given field is focused:

```
$browser->assertFocused($field);
```

#### [assertNotFocused](#assert-not-focused)

Assert that the given field is not focused:

```
$browser->assertNotFocused($field);
```

#### [assertAuthenticated](#assert-authenticated)

Assert that the user is authenticated:

```
$browser->assertAuthenticated();
```

#### [assertGuest](#assert-guest)

Assert that the user is not authenticated:

```
$browser->assertGuest();
```

#### [assertAuthenticatedAs](#assert-authenticated-as)

Assert that the user is authenticated as the given user:

```
$browser->assertAuthenticatedAs($user);
```

#### [assertVue](#assert-vue)

Dusk even allows you to make assertions on the state of [Vue component](https://vuejs.org) data. For example, imagine your application contains the following Vue component:

```
// HTML...

<profile dusk="profile-component"></profile>

// Component Definition...

Vue.component('profile', {
    template: '<div>{{ user.name }}</div>',

    data: function () {
        return {
            user: {
                name: 'Taylor'
            }
        };
    }
});
```

You may assert on the state of the Vue component like so:

Pest
PHPUnit

```
test('vue', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertVue('user.name', 'Taylor', '@profile-component');
    });
});
```

```
/**
 * A basic Vue test example.
 */
public function test_vue(): void
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertVue('user.name', 'Taylor', '@profile-component');
    });
}
```

#### [assertVueIsNot](#assert-vue-is-not)

Assert that a given Vue component data property does not match the given value:

```
$browser->assertVueIsNot($property, $value, $componentSelector = null);
```

#### [assertVueContains](#assert-vue-contains)

Assert that a given Vue component data property is an array and contains the given value:

```
$browser->assertVueContains($property, $value, $componentSelector = null);
```

#### [assertVueDoesntContain](#assert-vue-doesnt-contain)

Assert that a given Vue component data property is an array and does not contain the given value:

```
$browser->assertVueDoesntContain($property, $value, $componentSelector = null);
```

## [Pages](#pages)

Sometimes, tests require several complicated actions to be performed in sequence. This can make your tests harder to read and understand. Dusk Pages allow you to define expressive actions that may then be performed on a given page via a single method. Pages also allow you to define short-cuts to common selectors for your application or for a single page.

### [Generating Pages](#generating-pages)

To generate a page object, execute the `dusk:page` Artisan command. All page objects will be placed in your application's `tests/Browser/Pages` directory:

```
php artisan dusk:page Login
```

### [Configuring Pages](#configuring-pages)

By default, pages have three methods: `url`, `assert`, and `elements`. We will discuss the `url` and `assert` methods now. The `elements` method will be [discussed in more detail below](#shorthand-selectors).

#### [The `url` Method](#the-url-method)

The `url` method should return the path of the URL that represents the page. Dusk will use this URL when navigating to the page in the browser:

```
/**
 * Get the URL for the page.
 */
public function url(): string
{
    return '/login';
}
```

#### [The `assert` Method](#the-assert-method)

The `assert` method may make any assertions necessary to verify that the browser is actually on the given page. It is not actually necessary to place anything within this method; however, you are free to make these assertions if you wish. These assertions will be run automatically when navigating to the page:

```
/**
 * Assert that the browser is on the page.
 */
public function assert(Browser $browser): void
{
    $browser->assertPathIs($this->url());
}
```

### [Navigating to Pages](#navigating-to-pages)

Once a page has been defined, you may navigate to it using the `visit` method:

```
use Tests\Browser\Pages\Login;

$browser->visit(new Login);
```

Sometimes you may already be on a given page and need to "load" the page's selectors and methods into the current test context. This is common when pressing a button and being redirected to a given page without explicitly navigating to it. In this situation, you may use the `on` method to load the page:

```
use Tests\Browser\Pages\CreatePlaylist;

$browser->visit('/dashboard')
    ->clickLink('Create Playlist')
    ->on(new CreatePlaylist)
    ->assertSee('@create');
```

### [Shorthand Selectors](#shorthand-selectors)

The `elements` method within page classes allows you to define quick, easy-to-remember shortcuts for any CSS selector on your page. For example, let's define a shortcut for the "email" input field of the application's login page:

```
/**
 * Get the element shortcuts for the page.
 *
 * @return array<string, string>
 */
public function elements(): array
{
    return [
        '@email' => 'input[name=email]',
    ];
}
```

Once the shortcut has been defined, you may use the shorthand selector anywhere you would typically use a full CSS selector:

```
$browser->type('@email', '[email protected]');
```

#### [Global Shorthand Selectors](#global-shorthand-selectors)

After installing Dusk, a base `Page` class will be placed in your `tests/Browser/Pages` directory. This class contains a `siteElements` method which may be used to define global shorthand selectors that should be available on every page throughout your application:

```
/**
 * Get the global element shortcuts for the site.
 *
 * @return array<string, string>
 */
public static function siteElements(): array
{
    return [
        '@element' => '#selector',
    ];
}
```

### [Page Methods](#page-methods)

In addition to the default methods defined on pages, you may define additional methods which may be used throughout your tests. For example, let's imagine we are building a music management application. A common action for one page of the application might be to create a playlist. Instead of re-writing the logic to create a playlist in each test, you may define a `createPlaylist` method on a page class:

```
<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class Dashboard extends Page
{
    // Other page methods...

    /**
     * Create a new playlist.
     */
    public function createPlaylist(Browser $browser, string $name): void
    {
        $browser->type('name', $name)
            ->check('share')
            ->press('Create Playlist');
    }
}
```

Once the method has been defined, you may use it within any test that utilizes the page. The browser instance will automatically be passed as the first argument to custom page methods:

```
use Tests\Browser\Pages\Dashboard;

$browser->visit(new Dashboard)
    ->createPlaylist('My Playlist')
    ->assertSee('My Playlist');
```

## [Components](#components)

Components are similar to Dusk's "page objects", but are intended for pieces of UI and functionality that are re-used throughout your application, such as a navigation bar or notification window. As such, components are not bound to specific URLs.

### [Generating Components](#generating-components)

To generate a component, execute the `dusk:component` Artisan command. New components are placed in the `tests/Browser/Components` directory:

```
php artisan dusk:component DatePicker
```

As shown above, a "date picker" is an example of a component that might exist throughout your application on a variety of pages. It can become cumbersome to manually write the browser automation logic to select a date in dozens of tests throughout your test suite. Instead, we can define a Dusk component to represent the date picker, allowing us to encapsulate that logic within the component:

```
<?php

namespace Tests\Browser\Components;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component as BaseComponent;

class DatePicker extends BaseComponent
{
    /**
     * Get the root selector for the component.
     */
    public function selector(): string
    {
        return '.date-picker';
    }

    /**
     * Assert that the browser page contains the component.
     */
    public function assert(Browser $browser): void
    {
        $browser->assertVisible($this->selector());
    }

    /**
     * Get the element shortcuts for the component.
     *
     * @return array<string, string>
     */
    public function elements(): array
    {
        return [
            '@date-field' => 'input.datepicker-input',
            '@year-list' => 'div > div.datepicker-years',
            '@month-list' => 'div > div.datepicker-months',
            '@day-list' => 'div > div.datepicker-days',
        ];
    }

    /**
     * Select the given date.
     */
    public function selectDate(Browser $browser, int $year, int $month, int $day): void
    {
        $browser->click('@date-field')
            ->within('@year-list', function (Browser $browser) use ($year) {
                $browser->click($year);
            })
            ->within('@month-list', function (Browser $browser) use ($month) {
                $browser->click($month);
            })
            ->within('@day-list', function (Browser $browser) use ($day) {
                $browser->click($day);
            });
    }
}
```

### [Using Components](#using-components)

Once the component has been defined, we can easily select a date within the date picker from any test. And, if the logic necessary to select a date changes, we only need to update the component:

Pest
PHPUnit

```
<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\DatePicker;

pest()->use(DatabaseMigrations::class);

test('basic example', function () {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
            ->within(new DatePicker, function (Browser $browser) {
                $browser->selectDate(2019, 1, 30);
            })
            ->assertSee('January');
    });
});
```

```
<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Components\DatePicker;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic component test example.
     */
    public function test_basic_example(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->within(new DatePicker, function (Browser $browser) {
                    $browser->selectDate(2019, 1, 30);
                })
                ->assertSee('January');
        });
    }
}
```

The `component` method may be used to retrieve a browser instance scoped to the given component:

```
$datePicker = $browser->component(new DatePickerComponent);

$datePicker->selectDate(2019, 1, 30);

$datePicker->assertSee('January');
```

## [Continuous Integration](#continuous-integration)

Most Dusk continuous integration configurations expect your Laravel application to be served using the built-in PHP development server on port 8000. Therefore, before continuing, you should ensure that your continuous integration environment has an `APP_URL` environment variable value of `http://127.0.0.1:8000`.

### [Heroku CI](#running-tests-on-heroku-ci)

To run Dusk tests on [Heroku CI](https://www.heroku.com/continuous-integration), add the following Google Chrome buildpack and scripts to your Heroku `app.json` file:

```
{
  "environments": {
    "test": {
      "buildpacks": [
        { "url": "heroku/php" },
        { "url": "https://github.com/heroku/heroku-buildpack-chrome-for-testing" }
      ],
      "scripts": {
        "test-setup": "cp .env.testing .env",
        "test": "nohup bash -c './vendor/laravel/dusk/bin/chromedriver-linux --port=9515 > /dev/null 2>&1 &' && nohup bash -c 'php artisan serve --no-reload > /dev/null 2>&1 &' && php artisan dusk"
      }
    }
  }
}
```

### [Travis CI](#running-tests-on-travis-ci)

To run your Dusk tests on [Travis CI](https://travis-ci.org), use the following `.travis.yml` configuration. Since Travis CI is not a graphical environment, we will need to take some extra steps in order to launch a Chrome browser. In addition, we will use `php artisan serve` to launch PHP's built-in web server:

```
language: php

php:
  - 8.2

addons:
  chrome: stable

install:
  - cp .env.testing .env
  - travis_retry composer install --no-interaction --prefer-dist
  - php artisan key:generate
  - php artisan dusk:chrome-driver

before_script:
  - google-chrome-stable --headless --disable-gpu --remote-debugging-port=9222 http://localhost &
  - php artisan serve --no-reload &

script:
  - php artisan dusk
```

### [GitHub Actions](#running-tests-on-github-actions)

If you are using [GitHub Actions](https://github.com/features/actions) to run your Dusk tests, you may use the following configuration file as a starting point. Like TravisCI, we will use the `php artisan serve` command to launch PHP's built-in web server:

```
name: CI
on: [push]
jobs:

  dusk-php:
    runs-on: ubuntu-latest
    env:
      APP_URL: "http://127.0.0.1:8000"
      DB_USERNAME: root
      DB_PASSWORD: root
      MAIL_MAILER: log
    steps:
      - uses: actions/checkout@v5
      - name: Prepare The Environment
        run: cp .env.example .env
      - name: Create Database
        run: |
          sudo systemctl start mysql
          mysql --user="root" --password="root" -e "CREATE DATABASE \`my-database\` character set UTF8mb4 collate utf8mb4_bin;"
      - name: Install Composer Dependencies
        run: composer install --no-progress --prefer-dist --optimize-autoloader
      - name: Generate Application Key
        run: php artisan key:generate
      - name: Upgrade Chrome Driver
        run: php artisan dusk:chrome-driver --detect
      - name: Start Chrome Driver
        run: ./vendor/laravel/dusk/bin/chromedriver-linux --port=9515 &
      - name: Run Laravel Server
        run: php artisan serve --no-reload &
      - name: Run Dusk Tests
        run: php artisan dusk
      - name: Upload Screenshots
        if: failure()
        uses: actions/upload-artifact@v4
        with:
          name: screenshots
          path: tests/Browser/screenshots
      - name: Upload Console Logs
        if: failure()
        uses: actions/upload-artifact@v4
        with:
          name: console
          path: tests/Browser/console
```

### [Chipper CI](#running-tests-on-chipper-ci)

If you are using [Chipper CI](https://chipperci.com) to run your Dusk tests, you may use the following configuration file as a starting point. We will use PHP's built-in server to run Laravel so we can listen for requests:

```
# file .chipperci.yml
version: 1

environment:
  php: 8.2
  node: 16

# Include Chrome in the build environment
services:
  - dusk

# Build all commits
on:
   push:
      branches: .*

pipeline:
  - name: Setup
    cmd: |
      cp -v .env.example .env
      composer install --no-interaction --prefer-dist --optimize-autoloader
      php artisan key:generate

      # Create a dusk env file, ensuring APP_URL uses BUILD_HOST
      cp -v .env .env.dusk.ci
      sed -i "s@APP_URL=.*@APP_URL=http://$BUILD_HOST:8000@g" .env.dusk.ci

  - name: Compile Assets
    cmd: |
      npm ci --no-audit
      npm run build

  - name: Browser Tests
    cmd: |
      php -S [::0]:8000 -t public 2>server.log &
      sleep 2
      php artisan dusk:chrome-driver $CHROME_DRIVER
      php artisan dusk --env=ci
```

To learn more about running Dusk tests on Chipper CI, including how to use databases, consult the [official Chipper CI documentation](https://chipperci.com/docs/testing/laravel-dusk-new/).