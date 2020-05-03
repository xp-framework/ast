<?php namespace lang\ast\nodes;

use lang\ast\Node;

class UnaryExpression extends Node {
  public $expression, $operator;

  public function __construct($kind, $expression, $operator, $line= -1) {
    $this->kind= $kind;
    $this->expression= $expression;
    $this->operator= $operator;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->expression]; }
}