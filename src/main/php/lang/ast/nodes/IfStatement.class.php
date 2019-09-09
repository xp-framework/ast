<?php namespace lang\ast\nodes;

use lang\ast\Node;

class IfStatement extends Node {
  public $kind= 'if';
  public $expression, $body, $otherwise;

  public function __construct($expression, $body, $otherwise= null, $line= -1) {
    $this->expression= $expression;
    $this->body= $body;
    $this->otherwise= $otherwise;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    yield $this->expression;
    yield $this->body;
    if ($this->otherwise) {
      yield $this->otherwise;
    }
  }
}