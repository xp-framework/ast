<?php namespace lang\ast\nodes;

use lang\ast\Node;

class EnumCase extends Node {
  public $kind= 'enumcase';
  public $name;

  public function __construct($name, $line= -1) {
    $this->name= $name;
    $this->line= $line;
  }
}