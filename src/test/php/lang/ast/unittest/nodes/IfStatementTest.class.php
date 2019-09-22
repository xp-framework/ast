<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\IfStatement;
use lang\ast\nodes\Variable;

class IfStatementTest extends NodeTest {
  private $condition;

  /** @return void */
  public function setUp() {
    $this->condition= new Variable('condition');
  }

  #[@test]
  public function can_create() {
    new IfStatement($this->condition, [], null);
  }

  #[@test]
  public function expression() {
    $this->assertEquals($this->condition, (new IfStatement($this->condition, [], null))->expression);
  }

  #[@test]
  public function body() {
    $body= [$this->returns('true')];
    $this->assertEquals($body, (new IfStatement($this->condition, $body, null))->body);
  }

  #[@test]
  public function otherwise() {
    $otherwise= [$this->returns('true')];
    $this->assertEquals($otherwise, (new IfStatement($this->condition, [], $otherwise))->otherwise);
  }

  #[@test]
  public function children() {
    $body= [$this->returns('true')];
    $otherwise= [$this->returns('false')];

    $this->assertEquals(
      array_merge([$this->condition], $body, $otherwise),
      iterator_to_array((new IfStatement($this->condition, $body, $otherwise))->children())
    );
  }
}