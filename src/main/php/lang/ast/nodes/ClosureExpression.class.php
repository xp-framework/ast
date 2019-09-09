<?php namespace lang\ast\nodes;

use lang\ast\Node;

class ClosureExpression extends Node {
  public $kind= 'closure';
  public $signature, $use, $body;

  public function __construct($signature, $use, $body, $line= -1) {
    $this->signature= $signature;
    $this->use= $use;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->body; }
}