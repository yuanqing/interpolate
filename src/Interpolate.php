<?php
/**
 * Interpolate.php - Simple string interpolation in PHP.
 *
 * @author Lim Yuan Qing <hello@yuanqing.sg>
 * @license MIT
 * @link https://github.com/yuanqing/interpolate
 */

namespace yuanqing\Interpolate;

class Interpolate
{
  private $data;

  /**
   * Interpolates values from $data into the $tmpl string
   *
   * @param string $tmpl The string to interpolate values into
   * @param array $data Contains the values to use for interpolation
   * @return string
   * @throws InvalidArgumentException
   */
  public function render($tmpl, array $data)
  {
    if (!is_string($tmpl)) {
      throw new \InvalidArgumentException('Template must be a string');
    }
    $tmpl = (string) $tmpl;
    $this->data = $data;
    return preg_replace_callback('/{{(.+?)}}/', array($this, 'callback'), $tmpl);
  }

  /**
   * The callback for preg_replace_callback in the render method
   *
   * @param array $matches
   * @return string
   * @throws UnexpectedValueException
   */
  private function callback($matches)
  {
    $keys = explode('.', $matches[1]);
    $val = $this->followPath($this->data, $keys);
    if (is_callable($val)) {
      $val = call_user_func($val, $this->data);
    }
    if (!$this->canCastToString($val)) {
      throw new \UnexpectedValueException(sprintf('Value corresponding to the key \'%s\' could not
        be cast to string', trim($matches[1])));
    }
    return (string) $val;
  }

  /**
   * Gets a value from the $data array by following the path specified by the series of keys
   * in $keys
   *
   * @example
   * $data = array('foo' => array('bar' => 'baz');
   * $keys = array('foo', 'bar');
   * var_dump($this->followPath($data, $keys)); #=> 'baz'
   * @param array $data Array to get the value from
   * @param array $keys Series of keys to follow
   * @return mixed|null The value from $data corresponding to the path, or null if the path
   *    doesn't exist
   */
  private function followPath(array $data, array $keys)
  {
    foreach ($keys as $key) {
      $key = trim($key);
      if (!isset($data[$key])) {
        return null;
      }
      $data = $data[$key];
    }
    return $data;
  }

  /**
   * Returns true if $obj can be cast to string
   *
   * @param mixed $obj
   * @return boolean
   */
  private function canCastToString($obj)
  {
    if (is_scalar($obj) || is_null($obj)) {
      return true;
    }
    return is_object($obj) && method_exists($obj, '__toString');
  }

}
