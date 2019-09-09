<?php namespace lang\ast\nodes;

use lang\ast\Node;

class UsingStatement extends Node {
  public $kind= 'using';
  public $arguments, $body;

  public function __construct($arguments, $body, $line= -1) {
    $this->arguments= $arguments;
    $this->body= $body;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    foreach ($this->arguments as $element) {
      yield $element;
    }
    foreach ($this->body as $element) {
      yield $element;
    }
  }
}