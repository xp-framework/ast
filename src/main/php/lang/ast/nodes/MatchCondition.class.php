<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Match condition inside a match expression
 */
class MatchCondition extends Node {
  public $kind= 'condition';
  public $expressions, $body;

  public function __construct($expressions, $body) {
    $this->expressions= $expressions;
    $this->body= $body;
  }

  /** @return iterable */
  public function children() {
    foreach ($this->expressions as $expression) {
      yield $expression;
    }
    foreach ($this->body as $node) {
      yield $node;
    }
  }
}