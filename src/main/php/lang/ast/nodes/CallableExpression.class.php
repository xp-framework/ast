<?php namespace lang\ast\nodes;

use lang\ast\Node;

class CallableExpression extends Node {
  public $kind= 'callable';
  public $expression;

  public function __construct($expression, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->expression]; }
}