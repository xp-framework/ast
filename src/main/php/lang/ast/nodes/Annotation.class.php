<?php namespace lang\ast\nodes;

class Annotation {
  public $name, $node;

  public function __construct($name, $node) {
    $this->name= $name;
    $this->node= $node;
  }
}