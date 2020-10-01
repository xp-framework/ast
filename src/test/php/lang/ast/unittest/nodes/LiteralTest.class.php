<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\Literal;
use unittest\Test;

class LiteralTest extends NodeTest {

  #[Test]
  public function can_create() {
    new Literal('true');
  }

  #[Test]
  public function expression() {
    $this->assertEquals('true', (new Literal('true'))->expression);
  }
}