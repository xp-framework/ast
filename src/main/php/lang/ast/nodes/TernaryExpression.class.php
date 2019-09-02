<?php namespace lang\ast\nodes;

class TernaryExpression extends Value {
  public $kind= 'ternary';
  public $condition, $expression, $otherwise;

  public function __construct($condition, $expression, $otherwise, $line= -1) {
    $this->condition= $condition;
    $this->expression= $expression;
    $this->otherwise= $otherwise;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    yield $this->condition;
    if ($this->expression) {
      yield $this->expression;
    }
    yield $this->otherwise;
  }
}