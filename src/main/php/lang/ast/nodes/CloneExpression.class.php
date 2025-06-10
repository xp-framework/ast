<?php namespace lang\ast\nodes;

use lang\ast\Node;

class CloneExpression extends Node {
  public $kind= 'clone';
  public $arguments;

  public function __construct($arguments, $line= -1) {
    $this->arguments= $arguments;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    foreach ($this->arguments as &$expr) {
      yield &$expr;
    }
  }
}