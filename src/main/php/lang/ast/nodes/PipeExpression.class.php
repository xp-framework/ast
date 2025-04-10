<?php namespace lang\ast\nodes;

use lang\ast\Node;

class PipeExpression extends Node {
  public $kind= 'pipe';
  public $expression, $target;

  public function __construct($expression, $target, $line= -1) {
    $this->expression= $expression;
    $this->target= $target;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return [$this->expression, $this->target];
  }
}