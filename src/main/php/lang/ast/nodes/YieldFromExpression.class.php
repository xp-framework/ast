<?php namespace lang\ast\nodes;

use lang\ast\Node;

class YieldFromExpression extends Node {
  public $kind= 'from';
  public $iterable;

  public function __construct($iterable, $line= -1) {
    $this->iterable= $iterable;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [&$this->iterable]; }
}