<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\Literal;

class LiteralTest extends NodeTest {

  #[@test]
  public function can_create() {
    new Literal('true');
  }

  #[@test]
  public function expression() {
    $this->assertEquals('true', (new Literal('true'))->expression);
  }
}