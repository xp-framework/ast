<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * If statement with optional else
 *
 * @test xp://lang.ast.unittest.nodes.IfStatementTest
 */
class IfStatement extends Node {
  public $kind= 'if';
  public $expression, $body, $otherwise;

  public function __construct($expression, $body, $otherwise= null, $line= -1) {
    $this->expression= $expression;
    $this->body= $body;
    $this->otherwise= $otherwise;
    $this->line= $line;
  }

  /** @return iterable */
  public function &children() {
    yield $this->expression;
    foreach ($this->body as &$node) {
      yield $node;
    }
    foreach ((array)$this->otherwise as &$node) {
      yield $node;
    }
  }
}