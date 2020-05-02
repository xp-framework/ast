<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Literal extends Node {
  public $kind= 'literal';
  public $expression;

  public function __construct($expression, $line= -1) {
    $this->expression= $expression;
    $this->line= $line;
  }

  public function resolve($scope) {
    return eval('return '.$this->expression.';');
  }

  /** @return string */
  public function __toString() { return $this->expression; }
}