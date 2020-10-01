<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{InstanceExpression, InvokeExpression, Literal, Variable};
use unittest\{Assert, Test};

/**
 * Invocation expressions
 *
 * @see  https://wiki.php.net/rfc/trailing-comma-function-calls
 */
class InvokeTest extends ParseTest {

  #[Test]
  public function invoke_function() {
    $this->assertParsed(
      [new InvokeExpression(new Literal('test', self::LINE), [], self::LINE)],
      'test();'
    );
  }

  #[Test]
  public function invoke_method() {
    $instance= new InstanceExpression(new Variable('this', self::LINE), new Literal('test', self::LINE), self::LINE);
    $this->assertParsed(
      [new InvokeExpression($instance, [], self::LINE)],
      '$this->test();'
    );
  }

  #[Test]
  public function invoke_function_with_argument() {
    $arguments= [new Literal('1', self::LINE)];
    $this->assertParsed(
      [new InvokeExpression(new Literal('test', self::LINE), $arguments, self::LINE)],
      'test(1);'
    );
  }

  #[Test]
  public function invoke_function_with_arguments() {
    $arguments= [new Literal('1', self::LINE), new Literal('2', self::LINE)];
    $this->assertParsed(
      [new InvokeExpression(new Literal('test', self::LINE), $arguments, self::LINE)],
      'test(1, 2);'
    );
  }

  #[Test]
  public function invoke_function_with_dangling_comma() {
    $arguments= [new Literal('1', self::LINE), new Literal('2', self::LINE)];
    $this->assertParsed(
      [new InvokeExpression(new Literal('test', self::LINE), $arguments, self::LINE)],
      'test(1, 2, );'
    );
  }

  #[Test]
  public function invoke_function_with_positional_and_named_arguments() {
    $arguments= [0 => new Literal('1', self::LINE), 'named' => new Literal('2', self::LINE)];
    $this->assertParsed(
      [new InvokeExpression(new Literal('test', self::LINE), $arguments, self::LINE)],
      'test(1, named: 2);'
    );
  }
}