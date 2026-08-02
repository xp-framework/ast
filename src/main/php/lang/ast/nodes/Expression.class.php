<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Expression extends Node {
  public $kind= 'expression';
  public $inline;

  public function __construct($inline, $line= -1) {
    $this->inline= $inline;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [&$this->inline]; }
}