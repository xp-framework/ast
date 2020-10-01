<?php namespace lang\ast\unittest\nodes;

use lang\IllegalArgumentException;
use lang\ast\nodes\{CatchStatement, Variable};
use unittest\Test;

class CatchStatementTest extends NodeTest {
  private $types;

  /** @return void */
  public function setUp() {
    $this->types= [IllegalArgumentException::class];
  }

  #[Test]
  public function can_create() {
    new CatchStatement($this->types, 'e', []);
  }

  #[Test]
  public function types() {
    $this->assertEquals($this->types, (new CatchStatement($this->types, 'e', []))->types);
  }

  #[Test]
  public function body() {
    $body= [$this->returns('true')];
    $this->assertEquals($body, (new CatchStatement($this->types, 'e', $body))->body);
  }

  #[Test]
  public function variable() {
    $this->assertEquals('e', (new CatchStatement($this->types, 'e', []))->variable);
  }

  #[Test]
  public function children() {
    $block= [$this->returns('true')];
    $this->assertEquals($block, $this->childrenOf(new CatchStatement($this->types, 'e', $block)));
  }
}