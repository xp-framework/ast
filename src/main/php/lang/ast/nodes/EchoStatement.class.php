<?php namespace lang\ast\nodes;

use lang\ast\Node;

class EchoStatement extends Node {
  public $kind= 'echo';
  public $expressions;

  public function __construct($expressions, $line= -1) {
    $this->expressions= $expressions;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->expressions; }
}