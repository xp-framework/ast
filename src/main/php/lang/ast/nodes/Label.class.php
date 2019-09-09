<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Label extends Node {
  public $kind= 'label';
  public $name;

  public function __construct($name, $line= -1) {
    $this->name= $name;
    $this->line= $line;
  }
}