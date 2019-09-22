<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\IfStatement;
use lang\ast\nodes\Variable;

class IfStatementTest extends NodeTest {

  #[@test]
  public function can_create() {
    new IfStatement(new Variable('condition'), [], null);
  }

  #[@test]
  public function expression() {
    $condition= new Variable('condition');
    $this->assertEquals($condition, (new IfStatement(new Variable('condition'), [], null))->expression);
  }

  #[@test]
  public function body() {
    $body= [$this->returns('true')];
    $this->assertEquals($body, (new IfStatement(new Variable('condition'), $body, null))->body);
  }

  #[@test]
  public function otherwise() {
    $otherwise= [$this->returns('true')];
    $this->assertEquals($otherwise, (new IfStatement(new Variable('condition'), [], $otherwise))->otherwise);
  }

  #[@test]
  public function children() {
    $condition= new Variable('condition');
    $body= [$this->returns('true')];
    $otherwise= [$this->returns('false')];

    $this->assertEquals(
      array_merge([$condition], $body, $otherwise),
      iterator_to_array((new IfStatement($condition, $body, $otherwise))->children())
    );
  }
}