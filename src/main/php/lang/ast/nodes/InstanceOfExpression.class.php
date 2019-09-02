<?php namespace lang\ast\nodes;

class InstanceOfExpression extends Value {
  public $kind= 'instanceof';
  public $expression, $type;

  public function __construct($expression, $type, $line= 1) {
    $this->expression= $expression;
    $this->type= $type;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->expression]; }
}