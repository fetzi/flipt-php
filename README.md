<p align="center">
    <a href="https://github.com/fetzi/flipt-php/actions"><img alt="GitHub Workflow Status (main)" src="https://img.shields.io/github/workflow/status/fetzi/flipt-php/Tests/main"></a>
    <a href="https://packagist.org/packages/fetzi/flipt-php"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/fetzi/flipt-php"></a>
    <a href="https://packagist.org/packages/fetzi/flipt-php"><img alt="License" src="https://img.shields.io/packagist/l/fetzi/flipt-php"></a>
</p>

------
**flipt-php** is a small wrapper package for the Flipt REST API to be able to easily integrate Flipt into your PHP applications.

## Installation

```
composer require fetzi/flipt-php
```

## Usage
The `Flipt` class uses [HTTPlug](http://httplug.io/) a HTTP Client abstraction to make the API requests. You have to pass a base URL to the static `create` function. Everything else (`HttpClient`, `RequestFactory` & `StreamFactory`) is automatically determined.

```php
$flipt = Flipt::create('http://localhost:8080');
```

By calling the `evaluate` method you can check if a certain user (entity) should get a certain feature or not. To perform a evaluation you need to create a `EvaluateRequest` that contains the data to evaluate.

```php
$evaluateRequest = new EvaluateRequest('sample-flag', 'user-id', ['foo' => 'bar'])
$evaluateResponse = $flipt->evaluate($evaluateRequest);

if ($evaluateResponse->isMatch()) {
    // awesome new feature
} else {
    // old boring feature
}
```

### Variants
To be able to determine which variant of a feature-flag should be displayed you need to use the `getVariant()` method on the response.

The `EvaluateResponse` also provides a variant that can be accessed via the `getValue()` or `getVariant()` method:

```php
$evaluateRequest = new EvaluateRequest('sample-flag', 'user-id', ['foo' => 'bar'])
$evaluateResponse = $flipt->evaluate($evaluateRequest);

if ($evaluateResponse->isMatch()) {
    switch ($evaluateResponse->getVariant()) {
        case 'a':
            // show A variant
            break;
        case 'b':
            // show B variant
            break;
    }
}
```

**flipt-php** was created by **[Johannes Pichler](https://twitter.com/fetzi_io)** under the **[MIT license](https://opensource.org/licenses/MIT)**.
