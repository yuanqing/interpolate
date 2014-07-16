<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/src/Interpolate.php';

use yuanqing\Interpolate\Interpolate;

$i = new Interpolate;

$tmpl = '{foo.bar}, {foo.baz}!';
$data = array(
  'foo' => array(
    'bar' => 'Hello',
    'baz' => 'World'
  )
);
var_dump($i->render($tmpl, $data)); #=> 'Hello, World!'

$tmpl = '{baz}';
$data = array(
  'foo' => 'Hello',
  'bar' => 'World',
  'baz' => function($data) {
    return sprintf('%s, %s!', $data['foo'], $data['bar']);
  }
);
var_dump($i->render($tmpl, $data)); #=> 'Hello, World!'
