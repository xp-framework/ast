<?php namespace lang\ast\nodes;

use lang\ast\Node;

class NewExpression extends Node {
  public $kind= 'new';
  public $type, $arguments;

  public function __construct($type, $arguments, $line= -1) {
    $this->type= $type;
    $this->arguments= $arguments;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->arguments; }
}