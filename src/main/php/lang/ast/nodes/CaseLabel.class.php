<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Case label inside a switch statement
 *
 * @test xp://lang.ast.unittest.nodes.CaseLabelTest
 */
class CaseLabel extends Node {
  public $kind= 'case';
  public $expression, $body;

  public function __construct($expression, $body) {
    $this->expression= $expression;
    $this->body= $body;
  }

  /** @return iterable */
  public function children() {
    if (null !== $this->expression) {
      yield $this->expression;
    }
    foreach ($this->body as $node) {
      yield $node;
    }
  }
}