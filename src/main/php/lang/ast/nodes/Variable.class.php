<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Variable extends Node {
  public $kind= 'variable';
  public $name;

  public function __construct($name, $line= -1) {
    $this->name= $name;
    $this->line= $line;
  }
}