<?php namespace lang\ast\nodes;

class UnpackExpression extends Value {
  public $kind= 'unpack';
  public $expression;

  public function __construct($expression, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->expression]; }
}