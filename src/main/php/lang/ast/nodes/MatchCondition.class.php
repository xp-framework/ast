<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Match condition inside a match expression
 */
class MatchCondition extends Node {
  public $kind= 'condition';
  public $expressions, $body;

  public function __construct($expressions, $body, $line= -1) {
    $this->expressions= $expressions;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function &children() {
    foreach ($this->expressions as &$expression) {
      yield $expression;
    }
    yield $this->body;
  }
}