<?php namespace lang\ast\nodes;

class OffsetExpression extends Value {
  public $expression, $offset;

  public function __construct($expression, $offset) {
    $this->expression= $expression;
    $this->offset= $offset;
  }
}