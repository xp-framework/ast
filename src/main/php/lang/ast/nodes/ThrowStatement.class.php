<?php namespace lang\ast\nodes;

class ThrowStatement extends Value {
  public $kind= 'throw';
  public $expression;

  public function __construct($expression, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return [$this->expression];
  }
}