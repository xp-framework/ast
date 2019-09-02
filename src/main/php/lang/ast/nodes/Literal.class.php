<?php namespace lang\ast\nodes;

class Literal extends Value {
  public $kind= 'literal';
  public $expression;

  public function __construct($expression, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }
}