<?php namespace lang\ast\nodes;

use lang\ast\Node;

class UseExpression extends Node {
  public $kind= 'use';
  public $types, $aliases;

  public function __construct($types, $aliases, $line= -1) {
    $this->types= $types;
    $this->aliases= $aliases;
    $this->line= $line;
  }
}