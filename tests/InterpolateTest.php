<?php
/**
 * Interpolate.php - Simple string interpolation in PHP.
 *
 * @author Lim Yuan Qing <hello@yuanqing.sg>
 * @license MIT
 * @link https://github.com/yuanqing/interpolate
 */

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
    $expected = '';
    $this->assertEquals($i->interpolate(null, $data), $expected);
    $this->assertEquals($i->interpolate('', $data), $expected);
  }

  public function testEmptyData()
  {
    $i = new Interpolate;
    $tmpl = '{ foo }{ bar.baz }';
    $this->assertEquals($i->interpolate($tmpl, array()), '');
    $this->assertEquals($i->interpolate($tmpl, array('foo' => null)), '');
  }

  public function testGlobalReplace()
  {
    $i = new Interpolate;
    $tmpl = 'foo { foo } { foo } baz';
    $data = array(
      'foo' => 'bar',
    );
    $expected = 'foo bar bar baz';
    $this->assertEquals($i->interpolate($tmpl, $data), $expected);
  }

  public function testObjectWithToString()
  {
    $i = new Interpolate;
    $tmpl = '{ foo }';
    $data = array(
      'foo' => new SplFileInfo('bar') # SplFileInfo implements __toString()
    );
    $expected = 'bar';
    $this->assertEquals($i->interpolate($tmpl, $data), $expected);
  }

  /**
   * @expectedException UnexpectedValueException
   */
  public function testObjectWithoutToString()
  {
    $i = new Interpolate;
    $tmpl = '{ foo }';
    $data = array(
      'foo' => new StdClass
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

  public function testWhitespaceWithinKey()
  {
    $i = new Interpolate;
    $tmpl = '{ foo bar }';
    $data = array(
      'foo bar' => 'baz'
    );
    $expected = 'baz';
    $this->assertEquals($i->interpolate($tmpl, $data), $expected);
  }

  public function testFunctionInData()
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

  public function testDeepPropertyInterpolation()
  {
    $i = new Interpolate;
    $tmpl = '{ foo.bar baz.0 }';
    $data = array(
      'foo' => array(
        'bar baz' => array('bang')
      )
    );
    $expected = 'bang';
    $this->assertEquals($i->interpolate($tmpl, $data), $expected);
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
