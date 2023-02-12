<?php namespace lang\ast\unittest\nodes;

use lang\IllegalArgumentException;
use lang\ast\nodes\{CatchStatement, Variable};
use test\{Assert, Before, Test};

class CatchStatementTest extends NodeTest {
  private $types;

  #[Before]
  public function newTypes() {
    $this->types= [IllegalArgumentException::class];
  }

  #[Test]
  public function can_create() {
    new CatchStatement($this->types, 'e', []);
  }

  #[Test]
  public function types() {
    Assert::equals($this->types, (new CatchStatement($this->types, 'e', []))->types);
  }

  #[Test]
  public function body() {
    $body= [$this->returns('true')];
    Assert::equals($body, (new CatchStatement($this->types, 'e', $body))->body);
  }

  #[Test]
  public function variable() {
    Assert::equals('e', (new CatchStatement($this->types, 'e', []))->variable);
  }

  #[Test]
  public function children() {
    $block= [$this->returns('true')];
    Assert::equals($block, $this->childrenOf(new CatchStatement($this->types, 'e', $block)));
  }
}