<?php namespace lang\ast\nodes;

use lang\ast\Node;

class TryStatement extends Node {
  public $kind= 'try';
  public $body, $catches, $finally;

  public function __construct($body, $catches, $finally, $line= -1) {
    $this->body= $body;
    $this->catches= $catches;
    $this->finally= $finally;
    $this->line= $line;
  }

  /** @return iterable */
  public function &children() {
    foreach ($this->body as &$element) {
      yield $element;
    }
    foreach ($this->catches as &$catch) {
      yield $catch;
    }
    if ($this->finally) {
      yield $this->finally;
    }
  }
}