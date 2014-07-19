# Interpolate.php [![Packagist Version](http://img.shields.io/packagist/v/yuanqing/interpolate.svg)](https://packagist.org/packages/yuanqing/interpolate) [![Build Status](https://img.shields.io/travis/yuanqing/interpolate.svg)](https://travis-ci.org/yuanqing/interpolate) [![Coverage Status](https://img.shields.io/coveralls/yuanqing/interpolate.svg)](https://coveralls.io/r/yuanqing/interpolate)

A small PHP package for interpolating values from an array into a template string.

Think of it as a lightweight alternative to [Mustache](https://github.com/bobthecow/mustache.php):

```php
use yuanqing\Interpolate\Interpolate;

$i = new Interpolate;
$tmpl = '{{ foo.bar }}, {{ foo.baz }}!';
$data = array(
  'foo' => array(
    'bar' => 'Hello',
    'baz' => 'World'
  )
);
$i->render($tmpl, $data); #=> 'Hello, World!'
```

## Usage

1. Tags are enclosed in double braces.

2. Straight-up substitution; there are no conditional blocks, sections and so forth.

3. Tags can reference nested values in the multidimensional array (as in the example above).

4. A value to be interpolated can be a [scalar](http://php.net/manual/en/function.is-scalar.php), an object that implements `__toString()`, or a callback that returns a string:

    ```php
    $i = new Interpolate;
    $tmpl = '{{ baz }}';
    $data = array(
      'foo' => 'Hello',
      'bar' => 'World',
      'baz' => function($data) {
        return sprintf('%s, %s!', $data['foo'], $data['bar']);
      }
    );
    $i->render($tmpl, $data); #=> 'Hello, World!'
    ```

    Note that the first argument of the callback is the `$data` array.

5. If a value for a tag is not found, the tag will be replaced with an empty string.

The two examples in this README may be found in [the examples.php file](https://github.com/yuanqing/interpolate/blob/master/examples.php).

## Requirements

Interpolate.php requires at least **PHP 5.3**, or **HHVM**.

## Installation

### Install with Composer

1. Install [Composer](http://getcomposer.org/).

2. Install [the Interpolate.php Composer package](https://packagist.org/packages/yuanqing/interpolate):

    ```
    $ composer require yuanqing/interpolate ~1.2
    ```

3. In your PHP, require the Composer autoloader:

    ```php
    require_once __DIR__ . '/vendor/autoload.php';
    ```

### Install manually

1. Clone this repository:

    ```
    $ git clone https://github.com/yuanqing/interpolate
    ```

    Or just [grab the zip](https://github.com/yuanqing/interpolate/archive/master.zip).

2. In your PHP, require [Interpolate.php](https://github.com/yuanqing/interpolate/blob/master/src/Interpolate.php):

    ```php
    require_once __DIR__ . '/src/Interpolate.php';
    ```

## Testing

You need [PHPUnit](http://phpunit.de/) to run the tests:

```
$ git clone https://github.com/yuanqing/interpolate
$ cd interpolate
$ phpunit
```

## License

MIT license
