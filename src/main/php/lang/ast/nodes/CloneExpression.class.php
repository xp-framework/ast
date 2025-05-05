<?php namespace lang\ast\nodes;

use lang\ast\Node;

class CloneExpression extends Node {
  public $kind= 'clone';
  public $expression;
  public $with;

  public function __construct($expression, $with, $line= -1) {
    $this->expression= $expression;
    $this->with= $with;
  }

  /** @return iterable */
  public function children() {
    yield &$this->expression;
    foreach ($this->with as &$expr) {
      yield &$expr;
    }
  }
}