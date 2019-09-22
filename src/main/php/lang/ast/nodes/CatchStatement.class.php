<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Catch statement
 *
 * @test  xp://lang.ast.unittest.nodes.CatchStatementTest
 */
class CatchStatement extends Node {
  public $types, $variable, $body;

  public function __construct($types, $variable, $body) {
    $this->types= $types;
    $this->variable= $variable;
    $this->body= $body;
  }

  /** @return iterable */
  public function children() {
    foreach ($this->body as $element) {
      yield $element;
    }
  }
}