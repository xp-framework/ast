<?php namespace lang\ast\nodes;

class UnaryExpression extends Value {
  public $kind= 'unary';
  public $expression, $operator;

  public function __construct($expression, $operator, $line= -1) {
    $this->expression= $expression;
    $this->operator= $operator;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->expression]; }
}