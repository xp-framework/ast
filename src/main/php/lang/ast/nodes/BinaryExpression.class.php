<?php namespace lang\ast\nodes;

class BinaryExpression extends Value {
  public $kind= 'binary';
  public $left, $operator, $right;

  public function __construct($left, $operator, $right, $line= -1) {
    $this->left= $left;
    $this->operator= $operator;
    $this->right= $right;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->left, $this->right]; }
}