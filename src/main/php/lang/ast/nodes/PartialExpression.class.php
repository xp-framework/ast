<?php namespace lang\ast\nodes;

use lang\ast\Node;

class PartialExpression extends Node {
  public $kind= 'partial';
  public $placeholders, $invocation;

  public function __construct($placeholders, $invocation, $line= -1) {
    $this->placeholders= $placeholders;
    $this->invocation= $invocation;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->invocation]; }
}