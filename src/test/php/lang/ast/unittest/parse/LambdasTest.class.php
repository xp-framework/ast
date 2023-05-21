<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{BinaryExpression, Block, InvokeExpression, LambdaExpression, Literal, Parameter, ReturnStatement, Signature, Variable};
use test\{Assert, Before, Test};

class LambdasTest extends ParseTest {
  private $expression, $parameter;

  #[Before]
  public function expression() {
    $this->expression= new BinaryExpression(new Variable('a', self::LINE), '+', new Literal('1', self::LINE), self::LINE);
    $this->parameter= new Parameter('a', null, null, false, false, null, null, null, self::LINE);
  }

  #[Test]
  public function short_closure() {
    $this->assertParsed(
      [new LambdaExpression(new Signature([$this->parameter], null, false, self::LINE), $this->expression, false, self::LINE)],
      'fn($a) => $a + 1;'
    );
  }

  #[Test]
  public function static_closure() {
    $this->assertParsed(
      [new LambdaExpression(new Signature([$this->parameter], null, false, self::LINE), $this->expression, true, self::LINE)],
      'static fn($a) => $a + 1;'
    );
  }

  #[Test]
  public function short_closure_as_arg() {
    $this->assertParsed(
      [new InvokeExpression(
        new Literal('execute', self::LINE),
        [new LambdaExpression(new Signature([$this->parameter], null, false, self::LINE), $this->expression, false, self::LINE)],
        self::LINE
      )],
      'execute(fn($a) => $a + 1);'
    );
  }

  #[Test]
  public function short_closure_with_block() {
    $this->assertParsed(
      [new LambdaExpression(
        new Signature([$this->parameter], null, false, self::LINE),
        new Block([new ReturnStatement($this->expression, self::LINE)], self::LINE),
        false,
        self::LINE
      )],
      'fn($a) => { return $a + 1; };'
    );
  }
}