<?php namespace lang\ast\nodes;

use lang\ast\Node;

class CallableNewExpression extends Node {
  public $kind= 'callablenew';
  public $type;

  public function __construct($type, $line= -1) {
    $this->type= $type;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->type]; }
}