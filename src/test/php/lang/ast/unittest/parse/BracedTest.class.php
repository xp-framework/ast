<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{
  Braced,
  ClosureExpression,
  InvokeExpression,
  Literal,
  NewExpression,
  Parameter,
  ScopeExpression,
  Signature,
  UnpackExpression,
  Variable,
  YieldExpression
};
use lang\ast\types\IsValue;
use test\{Assert, Test};

class BracedTest extends ParseTest {

  #[Test]
  public function varargs_new() {
    $args= new UnpackExpression(new Variable('args', self::LINE), self::LINE);
    $this->assertParsed(
      [new Braced(new NewExpression(new IsValue('\\T'), [$args], self::LINE), self::LINE)],
      '(new T(...$args));'
    );
  }

  #[Test]
  public function varargs_invoke() {
    $args= new UnpackExpression(new Variable('args', self::LINE), self::LINE);
    $this->assertParsed(
      [new Braced(new InvokeExpression(new Literal('func', self::LINE), [$args], self::LINE), self::LINE)],
      '(func(...$args));'
    );
  }

  #[Test]
  public function varargs_scope() {
    $args= new UnpackExpression(new Variable('args', self::LINE), self::LINE);
    $this->assertParsed(
      [new Braced(
        new ScopeExpression(
          'self',
          new InvokeExpression(new Literal('func', self::LINE), [$args], self::LINE),
          self::LINE
        ),
        self::LINE
      )],
      '(self::func(...$args));'
    );
  }

  #[Test]
  public function yield() {
    $args= new YieldExpression(null, null, self::LINE);
    $this->assertParsed(
      [new Braced(new InvokeExpression(new Literal('func', self::LINE), [$args], self::LINE), self::LINE)],
      '(func(yield));'
    );
  }

  #[Test]
  public function function_with_typed_reference_arg() {
    $signature= new Signature(
      [new Parameter('arg', new IsValue('\\T'), null, true, false, null, null, null, self::LINE)],
      null,
      null,
      self::LINE
    );
    $this->assertParsed(
      [new Braced(new ClosureExpression($signature, null, [], false, self::LINE), self::LINE)],
      '(function(T &$arg) { });'
    );
  }
}