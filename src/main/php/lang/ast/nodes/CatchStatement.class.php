<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Catch statement
 *
 * @test  xp://lang.ast.unittest.nodes.CatchStatementTest
 */
class CatchStatement extends Node {
  public $types, $variable, $body;

  public function __construct($types, $variable, $body, $line= -1) {
    $this->types= $types;
    $this->variable= $variable;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->body; }
}