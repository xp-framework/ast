<?php namespace lang\ast\nodes;

use lang\ast\Node;

class CatchStatement extends Node {
  public $types, $variable, $body;

  public function __construct($types, $variable, $body) {
    $this->types= $types;
    $this->variable= $variable;
    $this->body= $body;
  }

  /** @return iterable */
  public function children() {
    yield $this->variable;
    foreach ($this->body as $element) {
      yield $element;
    }
  }
}