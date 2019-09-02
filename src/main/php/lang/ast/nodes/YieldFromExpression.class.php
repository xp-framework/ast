<?php namespace lang\ast\nodes;

class YieldFromExpression extends Value {
  public $kind= 'from';
  public $iterable;

  public function __construct($iterable, $line= -1) {
    $this->iterable= $iterable;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->iterable]; }
}