<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\{Literal, ReturnStatement};
use unittest\Test;

class ReturnStatementTest extends NodeTest {

  #[Test]
  public function return_without_expression() {
    $this->assertNull((new ReturnStatement())->expression);
  }

  #[Test]
  public function return_with_expression() {
    $expr= new Literal(1);
    $this->assertEquals($expr, (new ReturnStatement($expr))->expression);
  }

  #[Test]
  public function children_without_expression() {
    $this->assertEquals([], (new ReturnStatement())->children());
  }

  #[Test]
  public function children_with_expression() {
    $expr= new Literal(1);
    $this->assertEquals([$expr], (new ReturnStatement($expr))->children());
  }
}