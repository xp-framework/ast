<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Expression extends Node {
  public $kind= 'expression';
  public $variable, $inline;

  public function __construct($inline, $variable, $line= -1) {
    $this->inline= $inline;
    $this->variable= $variable;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->inline]; }
}