<?php namespace lang\ast\nodes;

use lang\ast\Node;

class UseStatement extends Node {
  public $kind= 'import';
  public $type, $names;

  public function __construct($type, $names, $line= -1) {
    $this->type= $type;
    $this->names= $names;
    $this->line= $line;
  }
}