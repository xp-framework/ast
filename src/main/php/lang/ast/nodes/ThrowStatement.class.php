<?php namespace lang\ast\nodes;

use lang\ast\Node;

class ThrowStatement extends Node {
  public $kind= 'throw';
  public $expression;

  public function __construct($expression, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return [$this->expression];
  }
}