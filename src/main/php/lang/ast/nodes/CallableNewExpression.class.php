<?php namespace lang\ast\nodes;

use lang\ast\Node;

class CallableNewExpression extends Node {
  public $kind= 'callablenew';
  public $type;
  public $arguments;

  public function __construct($type, $arguments= [], $line= -1) {
    $this->type= $type;
    $this->arguments= $arguments;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [&$this->type]; }
}