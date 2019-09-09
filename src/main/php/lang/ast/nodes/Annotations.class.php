<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Annotations extends Node {
  public $kind= 'annotation';
  public $values;

  public function __construct($values, $line= -1) {
    $this->values= $values;
    $this->line= $line;
  }
}