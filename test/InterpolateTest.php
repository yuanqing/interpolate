<?php
/**
 * Interpolate.php - Simple string interpolation in PHP.
 *
 * @author Lim Yuan Qing <hello@yuanqing.sg>
 * @license MIT
 * @link https://github.com/yuanqing/render
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
    $i->render($tmpl, $data);
  }

  /**
   * @expectedException PHPUnit_Framework_Error
   */
  public function testInvalidDataArgument()
  {
    $i = new Interpolate;
    $tmpl = '{{ foo }}';
    $data = null;
    $i->render('{{ foo }}', $data);
  }

  public function testEmptyTemplate()
  {
    $i = new Interpolate;
    $data = array();
    $this->assertEquals($i->render(null, $data), '');
    $this->assertEquals($i->render('', $data), '');
  }

  public function testEmptyData()
  {
    $i = new Interpolate;
    $tmpl = '{{ foo }}';
    $this->assertEquals($i->render($tmpl, array()), '');
    $this->assertEquals($i->render($tmpl, array('foo' => null)), '');
  }

  public function testStringTagName()
  {
    $i = new Interpolate;
    $data = array(
      'foo' => 'bar'
    );
    $this->assertEquals($i->render('{{ foo }}', $data), 'bar');
    $this->assertEquals($i->render('{{foo}}', $data), 'bar');
  }

  public function testWhitespaceWithinTagName()
  {
    $i = new Interpolate;
    $data = array(
      'foo bar' => 'baz'
    );
    $this->assertEquals($i->render('{{ foo bar }}', $data), 'baz');
    $this->assertEquals($i->render('{{foo bar}}', $data), 'baz');
  }

  public function testNumericTagName()
  {
    $i = new Interpolate;
    $tmpl = '{{ 0 }}';
    $data = array(
      0 => 'foo'
    );
    $this->assertEquals($i->render($tmpl, $data), 'foo');
  }

  public function testGlobalReplace()
  {
    $i = new Interpolate;
    $tmpl = '{{ foo }} {{ foo }}';
    $data = array(
      'foo' => 'bar',
    );
    $this->assertEquals($i->render($tmpl, $data), 'bar bar');
  }

  public function testNumericValue()
  {
    $i = new Interpolate;
    $tmpl = '{{ foo }}{{ bar }}';
    $data = array(
      'foo' => 3.14,
      'bar' => 159
    );
    $this->assertEquals($i->render($tmpl, $data), '3.14159');
  }

  public function testObjectValueWithToString()
  {
    $i = new Interpolate;
    $tmpl = '{{ foo }}';
    $data = array(
      'foo' => new SplFileInfo('bar') # SplFileInfo implements __toString()
    );
    $this->assertEquals($i->render($tmpl, $data), 'bar');
  }

  /**
   * @expectedException UnexpectedValueException
   */
  public function testObjectValueWithoutToString()
  {
    $i = new Interpolate;
    $tmpl = '{{ foo }}';
    $data = array(
      'foo' => new StdClass # StdClass does not implement __toString()
    );
    $i->render($tmpl, $data);
  }

  /**
   * @expectedException UnexpectedValueException
   */
  public function testArrayValue()
  {
    $i = new Interpolate;
    $tmpl = '{{ foo }}';
    $data = array(
      'foo' => array(
        'bar' => 'baz'
      )
    );
    $i->render($tmpl, $data);
  }

  public function testCallbackValue()
  {
    $i = new Interpolate;
    $data = array(
      'foo' => 'qux',
      'bar' => 'quux',
      'baz' => function($data) {
        return sprintf('%s %s', $data['foo'], $data['bar']);
      }
    );
    $tmpl = '{{ baz }}';
    $this->assertEquals($i->render($tmpl, $data), 'qux quux');
  }

  public function testNestedKeys()
  {
    $i = new Interpolate;
    $data = array(
      'foo' => array(
        'bar baz' => array(
          'qux' => 'quux'
        )
      )
    );
    $this->assertEquals($i->render('{{ foo.bar baz.qux }}', $data), 'quux');
    $this->assertEquals($i->render('{{foo . bar baz . qux}}', $data), 'quux');
    $this->assertEquals($i->render('{{ foo. bar baz .qux }}', $data), 'quux');
  }

}
