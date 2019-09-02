<?php namespace lang\ast\nodes;

class Block extends Value {
  public $kind= 'block';
  public $statements;

  public function __construct($statements, $line= -1) {
    $this->statements= $statements;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->statements; }
}