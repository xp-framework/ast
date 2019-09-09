<?php namespace lang\ast\nodes;

use lang\ast\Node;

class UnpackExpression extends Node {
  public $kind= 'unpack';
  public $expression;

  public function __construct($expression, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->expression]; }
}