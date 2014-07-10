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
  /**
   * Interpolates values from $data into the $tmpl string
   *
   * @param string $tmpl The string to interpolate values into
   * @param array $data The values to use for the interpolation
   * @return string
   * @throws InvalidArgumentException
   * @throws UnexpectedValueException
   */
  public function interpolate($tmpl, array $data)
  {
    if (!$this->isString($tmpl)) {
      throw new \InvalidArgumentException('Template could not be converted to string');
    }
    $tmpl = (string) $tmpl;
    return preg_replace_callback('/{(.+?)}/', function($matches) use ($data) {
      $keys = explode('.', $matches[1]);
      $val = $this->followPath($data, $keys);
      if (is_callable($val)) {
        $val = call_user_func($val, $data);
      }
      if (!$this->isString($val)) {
        throw new \UnexpectedValueException(sprintf('Value corresponding to the key "%s" could not be converted to string', trim($matches[1])));
      }
      return (string) $val;
    }, $tmpl);
  }

  /**
   * Get a value from $data by following the path specified by the series of keys in $keys
   *
   * @example
   * $data = array('foo' => array('bar' => 'baz');
   * $keys = array('foo', 'bar');
   * followPath($data, $keys); #=> 'baz'
   *
   * @param array $data Array to get the value from
   * @param array $keys Series of keys to follow
   * @return mixed
   */
  private function followPath($data, array $keys)
  {
    if (empty($keys)) {
      return $data;
    }
    $key = trim(array_shift($keys)); # get first key
    if (!isset($data[$key])) { # key doesn't exist
      return null;
    }
    return $this->followPath($data[$key], $keys);
  }

  /**
   * Returns true if $obj can be cast to string
   *
   * @param mixed $obj
   * @return boolean
   */
  private function isString($obj)
  {
    return is_scalar($obj) || is_null($obj) || (is_object($obj) && method_exists($obj, '__toString'));
  }

}
