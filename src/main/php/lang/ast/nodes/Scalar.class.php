<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Scalar extends Node {
  public $literal;

  public function __construct($literal, $kind, $line= -1) {
    $this->literal= $literal;
    $this->kind= $kind;
    $this->line= $line;
  }

  /** @return string */
  public function __toString() { return $this->literal; }
}