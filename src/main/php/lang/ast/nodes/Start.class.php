<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Start extends Node {
  public $kind= 'start';
  public $syntax;

  public function __construct($syntax, $line= -1) {
    $this->syntax= $syntax;
    $this->line= $line;
  }
}