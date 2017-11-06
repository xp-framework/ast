<?php namespace lang\ast\nodes;

class UnaryExpression extends Value {
  public $expression, $operator;

  public function __construct($expression, $operator) {
    $this->expression= $expression;
    $this->operator= $operator;
  }
}