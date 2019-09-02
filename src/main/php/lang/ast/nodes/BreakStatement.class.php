<?php namespace lang\ast\nodes;

class BreakStatement extends Value {
  public $kind= 'break';
  public $expression;

  public function __construct($expression= null, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->expression ? [$this->expression] : []; }
}