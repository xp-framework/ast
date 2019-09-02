<?php namespace lang\ast\nodes;

class Assignment extends Value {
  public $kind= 'assignment';
  public $variable, $operator, $expression;

  public function __construct($variable, $operator, $expression, $line= -1) {
    $this->variable= $variable;
    $this->operator= $operator;
    $this->expression= $expression;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->variable, $this->expression]; }
}