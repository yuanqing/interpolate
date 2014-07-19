<?php
/**
 * Interpolate.php - Simple string interpolation in PHP.
 *
 * @author Lim Yuan Qing <hello@yuanqing.sg>
 * @license MIT
 * @link https://github.com/yuanqing/interpolate
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/src/Interpolate.php';

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
