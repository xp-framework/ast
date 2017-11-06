<?php namespace lang\ast\nodes;

class CastExpression extends Value {
  public $type, $expression;

  public function __construct($type, $expression) {
    $this->type= $type;
    $this->expression= $expression;
  }
}