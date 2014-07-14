<?php
/**
 * Interpolate.php - Simple string interpolation in PHP.
 *
 * @author Lim Yuan Qing <hello@yuanqing.sg>
 * @license MIT
 * @link https://github.com/yuanqing/interpolate
 */

require_once dirname(__DIR__) . '/src/Interpolate.php';

use yuanqing\Interpolate\Interpolate;

class InterpolateTest extends PHPUnit_Framework_TestCase
{
  /**
   * @expectedException InvalidArgumentException
   */
  public function testInvalidTemplateArgument()
  {
    $i = new Interpolate;
    $tmpl = array();
    $data = array(
      'foo' => 'bar'
    );
    $i->interpolate($tmpl, $data);
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testInvalidDataArgument()
  {
    $i = new Interpolate;
    $tmpl = '{ foo }';
    $data = null;
    $i->interpolate('{ foo }', $data);
  }

  public function testEmptyTemplate()
  {
    $i = new Interpolate;
    $data = array();
    $this->assertEquals($i->interpolate(null, $data), '');
    $this->assertEquals($i->interpolate('', $data), '');
  }

  public function testEmptyData()
  {
    $i = new Interpolate;
    $tmpl = '{ foo }';
    $this->assertEquals($i->interpolate($tmpl, array()), '');
    $this->assertEquals($i->interpolate($tmpl, array('foo' => null)), '');
  }

  public function testGlobalReplace()
  {
    $i = new Interpolate;
    $tmpl = '{ foo } { foo }';
    $data = array(
      'foo' => 'bar',
    );
    $this->assertEquals($i->interpolate($tmpl, $data), 'bar bar');
  }

  public function testDoubleBrace()
  {
    $i = new Interpolate(true);
    $tmpl = '{{ foo }} {{ foo }}';
    $data = array(
      'foo' => 'bar',
    );
    $this->assertEquals($i->interpolate($tmpl, $data), 'bar bar');
  }

  public function testObjectValueWithToString()
  {
    $i = new Interpolate;
    $tmpl = '{ foo }';
    $data = array(
      'foo' => new SplFileInfo('bar') # SplFileInfo implements __toString()
    );
    $this->assertEquals($i->interpolate($tmpl, $data), 'bar');
  }

  /**
   * @expectedException UnexpectedValueException
   */
  public function testObjectValueWithoutToString()
  {
    $i = new Interpolate;
    $tmpl = '{ foo }';
    $data = array(
      'foo' => new StdClass # StdClass does not implement __toString()
    );
    $i->interpolate($tmpl, $data);
  }

  /**
   * @expectedException UnexpectedValueException
   */
  public function testArrayValue()
  {
    $i = new Interpolate;
    $tmpl = '{ foo }';
    $data = array(
      'foo' => array(
        'bar' => 'baz'
      )
    );
    $i->interpolate($tmpl, $data);
  }

  public function testCallbackValue()
  {
    $i = new Interpolate;
    $data = array(
      'foo' => 'bang',
      'bar' => 'boom',
      'baz' => function($data) {
        return sprintf('%s %s', $data['foo'], $data['bar']);
      }
    );
    $tmpl = <<<EOF
{ foo } { bar }
{ baz }
EOF;
    $expected = <<<EOF
bang boom
bang boom
EOF;
    $this->assertEquals($i->interpolate($tmpl, $data), $expected);
  }

  public function testWhitespaceInKey()
  {
    $i = new Interpolate;
    $tmpl = '{ foo bar }';
    $data = array(
      'foo bar' => 'baz'
    );
    $this->assertEquals($i->interpolate($tmpl, $data), 'baz');
  }

  public function testDeepProperty()
  {
    $i = new Interpolate;
    $tmpl = '{ foo.bar.baz }';
    $data = array(
      'foo' => array(
        'bar' => array(
          'baz' => 'qux'
        )
      )
    );
    $this->assertEquals($i->interpolate($tmpl, $data), 'qux');
  }

  public function testDeepPropertyWithWhitespaceInKey()
  {
    $i = new Interpolate;
    $tmpl = '{ foo.bar baz.qux }';
    $data = array(
      'foo' => array(
        'bar baz' => array(
          'qux' => 'quux'
        )
      )
    );
    $this->assertEquals($i->interpolate($tmpl, $data), 'quux');
  }

  public function testInconsistentWhitespace()
  {
    $i = new Interpolate;
    $tmpl = <<<EOF
{foo}
{ foo}
{foo  }
{bar.baz qux}
{bar  .baz qux }
{  bar. baz qux}
EOF;
    $data = array(
      'foo' => 'bang',
      'bar' => array(
        'baz qux' => 'boom'
      )
    );
    $expected = <<<EOF
bang
bang
bang
boom
boom
boom
EOF;
    $this->assertEquals($i->interpolate($tmpl, $data), $expected);
  }

}
