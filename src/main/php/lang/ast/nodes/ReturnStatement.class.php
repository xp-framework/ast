<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Return statement
 *
 * @test  xp://lang.ast.unittest.nodes.ReturnStatementTest
 */
class ReturnStatement extends Node {
  public $kind= 'return';
  public $expression;

  public function __construct($expression= null, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return $this->expression ? [$this->expression] : [];
  }
}