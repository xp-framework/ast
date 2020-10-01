<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\{IfStatement, Variable};
use unittest\Test;

class IfStatementTest extends NodeTest {
  private $condition;

  /** @return void */
  public function setUp() {
    $this->condition= new Variable('condition');
  }

  #[Test]
  public function can_create() {
    new IfStatement($this->condition, [], null);
  }

  #[Test]
  public function expression() {
    $this->assertEquals($this->condition, (new IfStatement($this->condition, [], null))->expression);
  }

  #[Test]
  public function body() {
    $body= [$this->returns('true')];
    $this->assertEquals($body, (new IfStatement($this->condition, $body, null))->body);
  }

  #[Test]
  public function otherwise() {
    $otherwise= [$this->returns('true')];
    $this->assertEquals($otherwise, (new IfStatement($this->condition, [], $otherwise))->otherwise);
  }

  #[Test]
  public function children() {
    $body= [$this->returns('true')];
    $otherwise= [$this->returns('false')];

    $this->assertEquals(
      array_merge([$this->condition], $body, $otherwise),
      $this->childrenOf(new IfStatement($this->condition, $body, $otherwise))
    );
  }
}