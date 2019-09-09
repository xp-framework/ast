<?php namespace lang\ast\nodes;

use lang\ast\Node;

class StaticLocals extends Node {
  public $kind= 'static';
  public $initializations;

  public function __construct($initializations= null, $line= -1) {
    $this->initializations= $initializations;
    $this->line= $line;
  }
}