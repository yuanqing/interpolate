# interpolate.php [![Build Status](https://travis-ci.org/yuanqing/interpolate.svg)](https://travis-ci.org/yuanqing/interpolate) [![Coverage Status](https://img.shields.io/coveralls/yuanqing/interpolate.svg?branch=master)](https://coveralls.io/r/yuanqing/interpolate?branch=master)

A small PHP package for interpolating values from an array into a template string.

Think of it as [Mustache](http://mustache.github.io/)-lite:

```php
use \yuanqing\Interpolate\Interpolate;

$i = new Interpolate;
$tmpl = '{foo.bar}, {foo.baz}!';
$data = array(
  'foo' => array(
    'bar' => 'Hello',
    'baz' => 'World'
  )
);
echo $i->interpolate($tmpl, $data); #=> 'Hello, World!'
```

## Usage

1. Tags are enclosed in single braces.

2. Straight-up substitution; no conditional blocks, sections and so forth. Tags can reference nested values in the array (as in the above example).

3. A value to be interpolated may be a function (that returns a string), or an object (that implements `__toString()`):

```php
use \yuanqing\Interpolate\Interpolate;

$i = new Interpolate;
$tmpl = '{baz}';
$data = array(
  'foo' => 'Hello',
  'bar' => 'World',
  'baz' => function($data) {
    return sprintf('%s, %s!', $data['foo'], $data['bar']);
  }
);
echo $i->interpolate($tmpl, $data); #=> 'Hello, World!'
```

## Installation

### Install with Composer

1. Install [Composer](http://getcomposer.org/).

2. Add `yuanqing/interpolate` to your `composer.json`:

    ```
    {
      "require": {
        "yuanqing/interpolate": "~1.0"
      }
    }
    ```

3. Require the Composer autoloader:

    ```php
    require_once(__DIR__ . '/vendor/autoload.php');
    ```

### Install manually

1. Clone this repository:

    ```
    $ git clone https://github.com/yuanqing/interpolate
    ```

    (Or [grab a zip of the latest release](https://github.com/yuanqing/interpolate/releases).)

2. Require `Interpolate.php`:

    ```php
    require_once(__DIR__ . '/src/Interpolate.php');
    ```

## Testing

1. Install [PHPUnit](http://phpunit.de/).

2. Clone this repository, and run `phpunit`:

    ```
    $ git clone https://github.com/yuanqing/interpolate
    $ cd interpolate
    $ phpunit
    ```
