<?php namespace lang\ast\nodes;

use lang\ast\Node;

class WhileLoop extends Node {
  public $kind= 'while';
  public $expression, $body;

  public function __construct($expression, $body, $line= -1) {
    $this->expression= $expression;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function &children() {
    yield $this->expression;
    foreach ($this->body as &$element) {
      yield $element;
    }
  }
}