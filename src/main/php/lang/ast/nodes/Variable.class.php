<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Variable extends Node {
  public $kind= 'variable';
  public $pointer, $const;

  public function __construct($pointer, $line= -1) {
    $this->pointer= $pointer;
    $this->const= is_string($pointer);
    $this->line= $line;
  }
}