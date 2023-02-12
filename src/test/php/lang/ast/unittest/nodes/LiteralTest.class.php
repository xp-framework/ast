<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\Literal;
use test\{Assert, Test};

class LiteralTest extends NodeTest {

  #[Test]
  public function can_create() {
    new Literal('true');
  }

  #[Test]
  public function expression() {
    Assert::equals('true', (new Literal('true'))->expression);
  }
}