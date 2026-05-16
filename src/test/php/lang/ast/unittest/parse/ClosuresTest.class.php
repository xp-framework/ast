<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{
  BinaryExpression,
  Braced,
  ClosureExpression,
  InvokeExpression,
  Literal,
  Parameter,
  ReturnStatement,
  Signature,
  Variable
};
use lang\ast\{FunctionType, Type};
use test\{Assert, Before, Test};

class ClosuresTest extends ParseTest {
  private $returns, $invoke;

  #[Before]
  public function returns() {
    $this->returns= new ReturnStatement(
      new BinaryExpression(
        new Variable('a', self::LINE),
        '+',
        new Literal('1', self::LINE),
        self::LINE
      ),
      self::LINE
    );
  }

  #[Before]
  public function invoke() {
    $this->invoke= new InvokeExpression(
      new Literal('var_dump', self::LINE),
      [new Literal('true', self::LINE)],
      self::LINE
    );
  }

  #[Test]
  public function with_body() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], null, false, self::LINE), null, [$this->returns], false, self::LINE)],
      'function() { return $a + 1; };'
    );
  }

  #[Test]
  public function with_param() {
    $params= [new Parameter('a', null, null, false, false, null, null, null, self::LINE)];
    $this->assertParsed(
      [new ClosureExpression(new Signature($params, null, false, self::LINE), null, [$this->returns], false, self::LINE)],
      'function($a) { return $a + 1; };'
    );
  }

  #[Test]
  public function with_use_by_value() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], null, false, self::LINE), ['$a', '$b'], [$this->returns], false, self::LINE)],
      'function() use($a, $b) { return $a + 1; };'
    );
  }

  #[Test]
  public function with_use_and_return() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], new Type('int'), false, self::LINE), ['$a'], [$this->returns], false, self::LINE)],
      'function() use($a): int { return $a + 1; };'
    );
  }

  #[Test]
  public function with_use_by_reference() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], null, false, self::LINE), ['$a', '&$b'], [$this->returns], false, self::LINE)],
      'function() use($a, &$b) { return $a + 1; };'
    );
  }

  #[Test]
  public function with_return_type() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], new Type('int'), false, self::LINE), null, [$this->invoke], false, self::LINE)],
      'function(): int { var_dump(true); };'
    );
  }

  #[Test]
  public function with_nullable_return_type() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], new Type('?int'), false, self::LINE), null, [$this->invoke], false, self::LINE)],
      'function(): ?int { var_dump(true); };'
    );
  }

  #[Test]
  public function static_function() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], null, false, self::LINE), null, [$this->invoke], true, self::LINE)],
      'static function() { var_dump(true); };'
    );
  }

  /** @see https://github.com/xp-framework/ast/issues/60 */
  #[Test]
  public function iife_with_statement() {
    $this->assertParsed(
      [new InvokeExpression(
        new Braced(
          new ClosureExpression(new Signature([], null, false, self::LINE), null, [$this->invoke], false, self::LINE),
          self::LINE
        ),
        [],
        self::LINE
      )],
      '(function() { var_dump(true); })();'
    );
  }
}