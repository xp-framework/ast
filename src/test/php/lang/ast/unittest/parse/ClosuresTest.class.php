<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{BinaryExpression, ClosureExpression, Literal, Parameter, ReturnStatement, Signature, Variable};
use lang\ast\{FunctionType, Type};
use unittest\{Assert, Before, Test};

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
      [new ClosureExpression(new Signature([], null, self::LINE), null, [$this->returns], self::LINE)],
      'function() { return $a + 1; };'
    );
  }

  #[Test]
  public function with_param() {
    $params= [new Parameter('a', null, null, false, false, null, null, null, self::LINE)];
    $this->assertParsed(
      [new ClosureExpression(new Signature($params, null, self::LINE), null, [$this->returns], self::LINE)],
      'function($a) { return $a + 1; };'
    );
  }

  #[Test]
  public function with_use_by_value() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], null, self::LINE), ['$a', '$b'], [$this->returns], self::LINE)],
      'function() use($a, $b) { return $a + 1; };'
    );
  }

  #[Test]
  public function with_use_by_reference() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], null, self::LINE), ['$a', '&$b'], [$this->returns], self::LINE)],
      'function() use($a, &$b) { return $a + 1; };'
    );
  }

  #[Test]
  public function with_return_type() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], new Type('int'), self::LINE), null, [$this->returns], self::LINE)],
      'function(): int { return $a + 1; };'
    );
  }

  #[Test]
  public function with_nullable_return_type() {
    $this->assertParsed(
      [new ClosureExpression(new Signature([], new Type('?int'), self::LINE), null, [$this->returns], self::LINE)],
      'function(): ?int { return $a + 1; };'
    );
  }
}