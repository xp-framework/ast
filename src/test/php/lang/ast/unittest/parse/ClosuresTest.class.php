<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{BinaryExpression, ClosureExpression, Literal, Parameter, ReturnStatement, Signature, Variable};
use lang\ast\{FunctionType, Type};
use test\{Assert, Before, Test};

class ClosuresTest extends ParseTest {
  private $returns;

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
      [new ClosureExpression(new Signature([], new Type('int'), false, self::LINE), null, [$this->returns], false, self::LINE)],
      'function(): int { return $a + 1; };'
    );
  }

  #[Test]
  public function with_nullable_return_type() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], new Type('?int'), false, self::LINE), null, [$this->returns], false, self::LINE)],
      'function(): ?int { return $a + 1; };'
    );
  }

  #[Test]
  public function static_function() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], null, false, self::LINE), null, [$this->returns], true, self::LINE)],
      'static function() { return $a + 1; };'
    );
  }
}