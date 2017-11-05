<?php namespace lang\ast\nodes;

class Annotation {
  public $node;

  public function __construct($node) {
    $this->node= $node;
  }
}