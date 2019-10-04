<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\Literal;
use lang\ast\nodes\ReturnStatement;

class ReturnStatementTest extends NodeTest {

  #[@test]
  public function return_without_expression() {
    $this->assertNull((new ReturnStatement())->expression);
  }

  #[@test]
  public function return_with_expression() {
    $expr= new Literal(1);
    $this->assertEquals($expr, (new ReturnStatement($expr))->expression);
  }

  #[@test]
  public function children_without_expression() {
    $this->assertEquals([], (new ReturnStatement())->children());
  }

  #[@test]
  public function children_with_expression() {
    $expr= new Literal(1);
    $this->assertEquals([$expr], (new ReturnStatement($expr))->children());
  }
}