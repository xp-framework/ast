<?php namespace lang\ast\nodes;

use lang\ast\Node;

class OffsetExpression extends Node {
  public $kind= 'offset';
  public $expression, $offset;

  public function __construct($expression, $offset, $line= -1) {
    $this->expression= $expression;
    $this->offset= $offset;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return $this->offset ? [&$this->expression, &$this->offset] : [&$this->expression];
  }
}