<?php namespace lang\ast\nodes;

class TernaryExpression extends Value {
  public $condition, $expression, $otherwise;

  public function __construct($condition, $expression, $otherwise) {
    $this->condition= $condition;
    $this->expression= $expression;
    $this->otherwise= $otherwise;
  }
}